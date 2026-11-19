<?php
require_once 'includes/baslangic.php';

$toplam = deger_getir("SELECT COUNT(*) FROM ihbarlar WHERE durum <> 'İptal'");
$cozulen = deger_getir("SELECT COUNT(*) FROM ihbarlar WHERE durum = 'Çözüldü'");
$acik = deger_getir("SELECT COUNT(*) FROM ihbarlar WHERE durum IN ('Bekliyor', 'Ekip Yolda', 'Çalışılıyor')");
$vatandas = deger_getir("SELECT COUNT(*) FROM kullanicilar WHERE rol = 'vatandas'");

$kategoriler = satirlar_getir(
    "SELECT k.*, COUNT(i.id) AS ihbar_sayisi
     FROM kategoriler k
     LEFT JOIN ihbarlar i ON i.kategori_id = k.id
     WHERE k.aktif = 1
     GROUP BY k.id
     ORDER BY k.id"
);

$son_cozulenler = satirlar_getir(
    "SELECT i.takip_no, i.baslik, i.cozum_tarihi, i.olusturma_tarihi, m.ad AS mahalle, m.ilce, k.ad AS kategori
     FROM ihbarlar i
     JOIN mahalleler m ON m.id = i.mahalle_id
     JOIN kategoriler k ON k.id = i.kategori_id
     WHERE i.durum = 'Çözüldü'
     ORDER BY i.cozum_tarihi DESC
     LIMIT 3"
);

$sayfa_basligi = 'Ana Sayfa';
$aktif_sayfa = 'index';
require 'includes/header.php';
?>

<section class="vitrin">
    <div class="kapsayici vitrin-ic">
        <div>
            <span class="ust-yazi" style="color:#fcd34d">Manisa Büyükşehir için</span>
            <h1>Sokağındaki sorunu <span>bildir</span>, çözümünü adım adım <span>takip et</span>.</h1>
            <p>Patlayan boru, yanmayan lamba, yoldaki çukur... Fotoğrafını çek, konumunu seç; ihbarın doğrudan sorumlu birime ve saha ekibine ulaşsın.</p>
            <div class="vitrin-butonlar">
                <?php if (giris_yapmis_mi()): ?>
                    <?php if (aktif_kullanici()['rol'] == 'vatandas'): ?>
                        <a href="ihbar_olustur.php" class="buton buton-vurgu buton-buyuk">İhbar Oluştur</a>
                    <?php endif; ?>
                    <a href="panel.php" class="buton buton-beyaz buton-buyuk">Panelime Git</a>
                <?php else: ?>
                    <a href="kayit.php" class="buton buton-vurgu buton-buyuk">Hemen Üye Ol</a>
                    <a href="giris.php" class="buton buton-beyaz buton-buyuk">Giriş Yap</a>
                <?php endif; ?>
            </div>
        </div>

        <form class="hizli-sorgu" action="ihbar_sorgula.php" method="get">
            <h3><?= ikon('ara') ?> İhbarımı Sorgula</h3>
            <p>İhbar sonrası verilen takip numarasıyla durumunu üye olmadan öğrenebilirsiniz.</p>
            <div class="form-satir">
                <label for="takip_no">Takip numarası</label>
                <input type="text" id="takip_no" name="takip_no" placeholder="Örn: KK-2026-0001" required>
            </div>
            <button type="submit" class="buton buton-tam">Sorgula</button>
        </form>
    </div>
</section>

<section class="kapsayici istatistikler">
    <div class="izgara izgara-4">
        <div class="kart istatistik">
            <div class="istatistik-ikon"><?= ikon('liste', 24) ?></div>
            <div><strong><?= $toplam ?></strong><span>Toplam ihbar</span></div>
        </div>
        <div class="kart istatistik">
            <div class="istatistik-ikon" style="background:#dcfce7;color:#16a34a"><?= ikon('onay', 24) ?></div>
            <div><strong><?= $cozulen ?></strong><span>Çözülen ihbar</span></div>
        </div>
        <div class="kart istatistik">
            <div class="istatistik-ikon" style="background:#fef3c7;color:#d97706"><?= ikon('saat', 24) ?></div>
            <div><strong><?= $acik ?></strong><span>Üzerinde çalışılan</span></div>
        </div>
        <div class="kart istatistik">
            <div class="istatistik-ikon" style="background:#ede9fe;color:#7c3aed"><?= ikon('kisiler', 24) ?></div>
            <div><strong><?= $vatandas ?></strong><span>Kayıtlı vatandaş</span></div>
        </div>
    </div>
</section>

<section class="bolum">
    <div class="kapsayici">
        <div class="bolum-baslik">
            <span class="ust-yazi">Hizmet alanları</span>
            <h2>Hangi sorunları bildirebilirsiniz?</h2>
            <p>Her kategori, belediyenin ilgili müdürlüğüne otomatik olarak yönlendirilir.</p>
        </div>
        <div class="izgara izgara-3">
            <?php foreach ($kategoriler as $k): ?>
                <div class="kart kategori-kart" style="--renk: <?= e($k['renk']) ?>">
                    <div class="kategori-ikon"><?= ikon($k['ikon'], 22) ?></div>
                    <div>
                        <h3><?= e($k['ad']) ?></h3>
                        <p><?= e($k['aciklama']) ?></p>
                        <small><?= e($k['sorumlu_birim']) ?> · <?= $k['ihbar_sayisi'] ?> ihbar</small>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="bolum" style="background:#fff">
    <div class="kapsayici">
        <div class="bolum-baslik">
            <span class="ust-yazi">Nasıl çalışır?</span>
            <h2>Üç adımda ihbar</h2>
        </div>
        <div class="izgara izgara-3 adimlar">
            <div class="adim">
                <h3>Bildir</h3>
                <p>Kategoriyi seç, kısa bir açıklama yaz, fotoğraf ve konum ekle.</p>
            </div>
            <div class="adim">
                <h3>Yönlendirilsin</h3>
                <p>Operatör ihbarı inceler ve uygun saha ekibini görevlendirir.</p>
            </div>
            <div class="adim">
                <h3>Takip et</h3>
                <p>Ekip yola çıktığında ve sorun çözüldüğünde durumu anında gör.</p>
            </div>
        </div>
    </div>
</section>

<section class="bolum">
    <div class="kapsayici">
        <div class="bolum-baslik">
            <span class="ust-yazi">Son çözülenler</span>
            <h2>Birlikte düzelttiklerimiz</h2>
        </div>
        <div class="izgara izgara-3">
            <?php foreach ($son_cozulenler as $i): ?>
                <div class="kart ihbar-ozet">
                    <div class="ihbar-ozet-ust">
                        <small><?= e($i['takip_no']) ?></small>
                        <?= durum_etiketi('Çözüldü') ?>
                    </div>
                    <h4><?= e($i['baslik']) ?></h4>
                    <p><?= e($i['kategori']) ?></p>
                    <div class="kucuk-bilgi">
                        <span><?= ikon('konum', 14) ?> <?= e($i['mahalle']) ?>, <?= e($i['ilce']) ?></span>
                        <span><?= ikon('onay', 14) ?> <?= tarih_tr($i['cozum_tarihi'], false) ?></span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if (!giris_yapmis_mi()): ?>
            <div class="cagri" style="margin-top:48px">
                <div>
                    <h2>Mahallen için sen de harekete geç</h2>
                    <p>Ücretsiz üye ol, ihbarlarını tek yerden takip et.</p>
                </div>
                <a href="kayit.php" class="buton buton-vurgu buton-buyuk">Kayıt Ol</a>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require 'includes/footer.php'; ?>
