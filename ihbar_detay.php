<?php
require_once 'includes/baslangic.php';

$oturum = giris_zorunlu();
$personel_mi = in_array($oturum['rol'], ['admin', 'operator']);

$id = (int) ($_GET['id'] ?? 0);
$ihbar = satir_getir(
    "SELECT i.*, k.ad AS kategori, k.sorumlu_birim, k.renk, m.ad AS mahalle, m.ilce,
            u.ad_soyad AS bildiren, u.telefon AS bildiren_telefon, u.eposta AS bildiren_eposta
     FROM ihbarlar i
     JOIN kategoriler k ON k.id = i.kategori_id
     JOIN mahalleler m ON m.id = i.mahalle_id
     JOIN kullanicilar u ON u.id = i.kullanici_id
     WHERE i.id = ?",
    [$id]
);

if (!$ihbar || !ihbari_gorebilir_mi($ihbar, $oturum)) {
    http_response_code(404);
    $sayfa_basligi = 'İhbar bulunamadı';
    require 'includes/header.php';
    echo '<div class="kapsayici" style="max-width:560px;padding:64px 20px"><div class="kart bos-durum">'
        . '<h2>İhbar bulunamadı</h2><p>Aradığınız ihbar yok ya da görüntüleme yetkiniz bulunmuyor.</p>'
        . '<a href="panel.php" class="buton">Panele dön</a></div></div>';
    require 'includes/footer.php';
    exit;
}

$durumlar = ['Bekliyor', 'Ekip Yolda', 'Çalışılıyor', 'Çözüldü', 'İptal'];
$hatalar = [];

// Operatör ve yönetici ihbarın durumunu değiştirebilir
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $personel_mi) {
    $yeni_durum = $_POST['durum'] ?? '';
    $not = trim($_POST['not'] ?? '');

    if (!in_array($yeni_durum, $durumlar)) {
        $hatalar[] = 'Geçerli bir durum seçin.';
    } elseif ($yeni_durum == 'Çözüldü' && !deger_getir("SELECT COUNT(*) FROM sevkler WHERE ihbar_id = ?", [$id])) {
        $hatalar[] = 'Ekip atanmamış bir ihbar "Çözüldü" olarak kapatılamaz.';
    } elseif ($yeni_durum == $ihbar['durum'] && $not == '') {
        $hatalar[] = 'Durum zaten "' . $yeni_durum . '". Değiştirmek için farklı bir durum seçin ya da not yazın.';
    }

    if (count($hatalar) == 0) {
        $cozum_tarihi = $yeni_durum == 'Çözüldü' ? date('Y-m-d H:i:s') : null;
        sorgu(
            "UPDATE ihbarlar SET durum = ?, cozum_tarihi = ?, guncelleme_tarihi = NOW() WHERE id = ?",
            [$yeni_durum, $cozum_tarihi, $id]
        );
        if ($not != '') {
            sorgu("INSERT INTO yorumlar (ihbar_id, kullanici_id, metin) VALUES (?, ?, ?)", [$id, $oturum['id'], $not]);
        }
        mesaj_ekle('basari', 'İhbar durumu "' . $yeni_durum . '" olarak güncellendi.');
        yonlendir('ihbar_detay.php?id=' . $id);
    }
}

$sevkler = satirlar_getir(
    "SELECT s.*, e.ekip_kodu, e.ad AS ekip, e.plaka, u.ad_soyad AS atayan
     FROM sevkler s
     JOIN ekipler e ON e.id = s.ekip_id
     JOIN kullanicilar u ON u.id = s.atayan_id
     WHERE s.ihbar_id = ? ORDER BY s.atanma_tarihi DESC",
    [$id]
);

$yorumlar = satirlar_getir(
    "SELECT y.*, u.ad_soyad, u.rol FROM yorumlar y
     JOIN kullanicilar u ON u.id = y.kullanici_id
     WHERE y.ihbar_id = ? ORDER BY y.tarih",
    [$id]
);

$destek_sayisi = deger_getir("SELECT COUNT(*) FROM destekler WHERE ihbar_id = ?", [$id]);

$adimlar = ['Bekliyor', 'Ekip Yolda', 'Çalışılıyor', 'Çözüldü'];
$simdiki_adim = array_search($ihbar['durum'], $adimlar);

$sayfa_basligi = $ihbar['takip_no'];
$aktif_sayfa = $oturum['rol'] == 'vatandas' ? 'ihbarlarim' : 'ihbarlar';
require 'includes/header.php';
?>

<section class="sayfa-ust">
    <div class="kapsayici sayfa-ust-ic">
        <div>
            <p style="margin-bottom:4px"><?= e($ihbar['takip_no']) ?> · <?= e($ihbar['kategori']) ?></p>
            <h1><?= e($ihbar['baslik']) ?></h1>
        </div>
        <?php if ($oturum['id'] == $ihbar['kullanici_id'] && $ihbar['durum'] == 'Bekliyor'): ?>
            <div class="islemler">
                <a href="ihbar_duzenle.php?id=<?= $id ?>" class="buton buton-beyaz"><?= ikon('duzenle', 16) ?> Düzenle</a>
                <form action="ihbar_iptal.php" method="post" data-onay="Bu ihbarı iptal etmek istediğinize emin misiniz?">
                    <input type="hidden" name="id" value="<?= $id ?>">
                    <button type="submit" class="buton buton-tehlike"><?= ikon('sil', 16) ?> İptal Et</button>
                </form>
            </div>
        <?php endif; ?>
    </div>
</section>

<div class="kapsayici sayfa-govde">
    <?php mesajlari_goster(); ?>
    <?php hatalari_goster($hatalar); ?>

    <div class="izgara duzen-sag">
        <div>
            <div class="kart bosluk-alt">
                <?php if ($ihbar['foto']): ?>
                    <img src="<?= e($ihbar['foto']) ?>" alt="İhbar fotoğrafı" class="detay-foto">
                <?php else: ?>
                    <div class="foto-yok"><span><?= ikon('kamera', 22) ?> Fotoğraf eklenmemiş</span></div>
                <?php endif; ?>

                <div class="ihbar-ozet-ust">
                    <div><?= durum_etiketi($ihbar['durum']) ?> <?= oncelik_etiketi($ihbar['oncelik']) ?></div>
                    <span class="kucuk-bilgi"><span><?= ikon('kisiler', 14) ?> <?= $destek_sayisi ?> kişi destekledi</span></span>
                </div>

                <?php if ($ihbar['durum'] == 'İptal'): ?>
                    <div class="uyari uyari-bilgi" style="margin:20px 0 0">Bu ihbar iptal edilmiştir.</div>
                <?php else: ?>
                    <div class="takip-adimlari">
                        <?php foreach ($adimlar as $sira => $adim):
                            $sinif = '';
                            if ($sira < $simdiki_adim || $ihbar['durum'] == 'Çözüldü') $sinif = 'tamam';
                            elseif ($sira == $simdiki_adim) $sinif = 'simdi';
                        ?>
                            <div class="takip-adim <?= $sinif ?>">
                                <span><?= $sinif == 'tamam' ? '✓' : $sira + 1 ?></span>
                                <?= $adim ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <h3 style="margin-top:24px">Açıklama</h3>
                <p style="margin-top:0"><?= nl2br(e($ihbar['aciklama'])) ?></p>
            </div>

            <div class="kart">
                <h3>Notlar ve yorumlar (<?= count($yorumlar) ?>)</h3>
                <?php if (count($yorumlar) == 0): ?>
                    <p style="color:var(--yazi-soluk);margin:0">Henüz yorum yok.</p>
                <?php endif; ?>
                <?php foreach ($yorumlar as $y): ?>
                    <div class="yorum">
                        <span class="avatar"><?= e(bas_harfler($y['ad_soyad'])) ?></span>
                        <div>
                            <div class="yorum-ust">
                                <strong><?= e($y['ad_soyad']) ?></strong>
                                <?php if ($y['rol'] != 'vatandas'): ?>
                                    <span class="personel-rozet"><?= rol_adi($y['rol']) ?></span>
                                <?php endif; ?>
                                · <?= ne_zaman($y['tarih']) ?>
                            </div>
                            <p><?= nl2br(e($y['metin'])) ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <aside>
            <?php if ($personel_mi): ?>
                <form class="kart bosluk-alt" method="post">
                    <h3>Durumu güncelle</h3>
                    <div class="form-satir">
                        <label for="durum">Yeni durum</label>
                        <select id="durum" name="durum">
                            <?php foreach ($durumlar as $d): ?>
                                <option <?= secili($d, $ihbar['durum']) ?>><?= $d ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-satir">
                        <label for="not">Vatandaşa not <small>(isteğe bağlı)</small></label>
                        <textarea id="not" name="not" rows="3" style="min-height:80px"></textarea>
                    </div>
                    <button type="submit" class="buton buton-tam">Kaydet</button>
                </form>
            <?php endif; ?>

            <div class="kart bosluk-alt">
                <h3>İhbar bilgileri</h3>
                <dl class="bilgi-listesi" style="grid-template-columns:1fr">
                    <dt>Sorumlu birim</dt><dd><?= e($ihbar['sorumlu_birim']) ?></dd>
                    <dt>Konum</dt>
                    <dd><?= e($ihbar['mahalle']) ?> Mah., <?= e($ihbar['ilce']) ?><br><small style="font-weight:400"><?= e($ihbar['adres']) ?></small></dd>
                    <dt>Koordinat</dt><dd><?= $ihbar['enlem'] ?>, <?= $ihbar['boylam'] ?></dd>
                    <dt>Bildirim tarihi</dt><dd><?= tarih_tr($ihbar['olusturma_tarihi']) ?></dd>
                    <?php if ($ihbar['guncelleme_tarihi']): ?>
                        <dt>Son güncelleme</dt><dd><?= tarih_tr($ihbar['guncelleme_tarihi']) ?></dd>
                    <?php endif; ?>
                    <?php if ($ihbar['cozum_tarihi']): ?>
                        <dt>Çözüm tarihi</dt><dd><?= tarih_tr($ihbar['cozum_tarihi']) ?></dd>
                    <?php endif; ?>
                </dl>
            </div>

            <?php if ($oturum['rol'] != 'vatandas'): ?>
                <div class="kart bosluk-alt">
                    <h3>Bildiren</h3>
                    <p style="margin:0"><strong><?= e($ihbar['bildiren']) ?></strong></p>
                    <div class="kucuk-bilgi" style="flex-direction:column;gap:2px;margin-top:6px">
                        <span><?= ikon('telefon', 14) ?> <?= e($ihbar['bildiren_telefon'] ?: '-') ?></span>
                        <span><?= ikon('posta', 14) ?> <?= e($ihbar['bildiren_eposta']) ?></span>
                    </div>
                </div>
            <?php endif; ?>

            <div class="kart">
                <h3>Saha ekibi</h3>
                <?php if (count($sevkler) == 0): ?>
                    <p style="color:var(--yazi-soluk);margin:0">Henüz ekip yönlendirilmedi.</p>
                <?php endif; ?>
                <?php foreach ($sevkler as $s): ?>
                    <div class="yorum">
                        <div>
                            <strong><?= e($s['ekip_kodu']) ?></strong> - <?= e($s['ekip']) ?>
                            <div class="yorum-ust"><?= e($s['plaka']) ?> · <?= tarih_tr($s['atanma_tarihi']) ?></div>
                            <?php if ($s['sevk_notu']): ?><p><?= e($s['sevk_notu']) ?></p><?php endif; ?>
                            <?php if ($s['tamamlandi']): ?>
                                <p style="color:#166534"><?= ikon('onay', 14) ?> <?= e($s['cozum_notu']) ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </aside>
    </div>
</div>

<?php require 'includes/footer.php'; ?>
