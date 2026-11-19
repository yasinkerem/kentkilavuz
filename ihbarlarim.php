<?php
require_once 'includes/baslangic.php';

$oturum = yetki_kontrol(['vatandas']);

$durumlar = ['Bekliyor', 'Ekip Yolda', 'Çalışılıyor', 'Çözüldü', 'İptal'];
$secili_durum = $_GET['durum'] ?? '';
if (!in_array($secili_durum, $durumlar)) {
    $secili_durum = '';
}

// Sekmelerde göstermek için her durumdaki ihbar sayısı
$sayilar = [];
foreach (satirlar_getir("SELECT durum, COUNT(*) AS sayi FROM ihbarlar WHERE kullanici_id = ? GROUP BY durum", [$oturum['id']]) as $s) {
    $sayilar[$s['durum']] = $s['sayi'];
}

$sql = "SELECT i.*, k.ad AS kategori, k.renk, m.ad AS mahalle,
               (SELECT COUNT(*) FROM destekler d WHERE d.ihbar_id = i.id) AS destek
        FROM ihbarlar i
        JOIN kategoriler k ON k.id = i.kategori_id
        JOIN mahalleler m ON m.id = i.mahalle_id
        WHERE i.kullanici_id = ?";
$parametreler = [$oturum['id']];
if ($secili_durum != '') {
    $sql .= " AND i.durum = ?";
    $parametreler[] = $secili_durum;
}
$sql .= " ORDER BY i.olusturma_tarihi DESC";
$ihbarlar = satirlar_getir($sql, $parametreler);

$sayfa_basligi = 'İhbarlarım';
$aktif_sayfa = 'ihbarlarim';
require 'includes/header.php';
?>

<section class="sayfa-ust">
    <div class="kapsayici sayfa-ust-ic">
        <div>
            <h1>İhbarlarım</h1>
            <p>Oluşturduğunuz tüm ihbarlar ve son durumları.</p>
        </div>
        <a href="ihbar_olustur.php" class="buton buton-vurgu"><?= ikon('arti', 18) ?> Yeni İhbar</a>
    </div>
</section>

<div class="kapsayici sayfa-govde">
    <?php mesajlari_goster(); ?>

    <nav class="sekmeler">
        <a href="ihbarlarim.php" class="<?= $secili_durum == '' ? 'aktif' : '' ?>">Tümü<span><?= array_sum($sayilar) ?></span></a>
        <?php foreach ($durumlar as $d): ?>
            <a href="ihbarlarim.php?durum=<?= urlencode($d) ?>" class="<?= $secili_durum == $d ? 'aktif' : '' ?>">
                <?= $d ?><span><?= $sayilar[$d] ?? 0 ?></span>
            </a>
        <?php endforeach; ?>
    </nav>

    <?php if (count($ihbarlar) == 0): ?>
        <div class="kart bos-durum">
            <?= ikon('liste', 36) ?>
            <p>Bu durumda ihbarınız bulunmuyor.</p>
        </div>
    <?php else: ?>
        <div class="tablo-kutu kart" style="padding:0">
            <table class="tablo">
                <tr><th></th><th>İhbar</th><th>Konum</th><th>Öncelik</th><th>Durum</th><th>Tarih</th><th class="sag">İşlemler</th></tr>
                <?php foreach ($ihbarlar as $i): ?>
                    <tr>
                        <td>
                            <?php if ($i['foto']): ?>
                                <img src="<?= e($i['foto']) ?>" alt="" class="kucuk-foto">
                            <?php else: ?>
                                <span class="kategori-ikon" style="--renk:<?= e($i['renk']) ?>;width:44px;height:44px"><?= ikon('kamera', 18) ?></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="ihbar_detay.php?id=<?= $i['id'] ?>"><strong><?= e($i['baslik']) ?></strong></a>
                            <small><?= e($i['takip_no']) ?> · <?= e($i['kategori']) ?> · <?= $i['destek'] ?> destek</small>
                        </td>
                        <td><?= e($i['mahalle']) ?></td>
                        <td><?= oncelik_etiketi($i['oncelik']) ?></td>
                        <td><?= durum_etiketi($i['durum']) ?></td>
                        <td><?= tarih_tr($i['olusturma_tarihi'], false) ?></td>
                        <td>
                            <div class="islemler" style="justify-content:flex-end">
                                <a href="ihbar_detay.php?id=<?= $i['id'] ?>" class="buton buton-gri buton-ikon" title="Görüntüle"><?= ikon('goz', 16) ?></a>
                                <?php if ($i['durum'] == 'Bekliyor'): ?>
                                    <a href="ihbar_duzenle.php?id=<?= $i['id'] ?>" class="buton buton-gri buton-ikon" title="Düzenle"><?= ikon('duzenle', 16) ?></a>
                                    <form action="ihbar_iptal.php" method="post" data-onay="Bu ihbarı iptal etmek istediğinize emin misiniz?">
                                        <input type="hidden" name="id" value="<?= $i['id'] ?>">
                                        <button type="submit" class="buton buton-tehlike buton-ikon" title="İptal et"><?= ikon('sil', 16) ?></button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php require 'includes/footer.php'; ?>
