<?php
require_once 'includes/baslangic.php';

$oturum = yetki_kontrol(['admin', 'operator']);

$durumlar = ['Bekliyor', 'Ekip Yolda', 'Çalışılıyor', 'Çözüldü', 'İptal'];
$oncelikler = ['Düşük', 'Normal', 'Yüksek', 'Acil'];
$kategoriler = satirlar_getir("SELECT id, ad FROM kategoriler ORDER BY ad");

$filtre = [
    'durum' => $_GET['durum'] ?? '',
    'kategori_id' => $_GET['kategori_id'] ?? '',
    'oncelik' => $_GET['oncelik'] ?? '',
    'mahalle_id' => $_GET['mahalle_id'] ?? '',
];

// Seçilen filtrelere göre WHERE koşullarını oluşturuyoruz
$kosullar = [];
$parametreler = [];
if (in_array($filtre['durum'], $durumlar)) {
    $kosullar[] = 'i.durum = ?';
    $parametreler[] = $filtre['durum'];
}
if ($filtre['kategori_id'] != '') {
    $kosullar[] = 'i.kategori_id = ?';
    $parametreler[] = (int) $filtre['kategori_id'];
}
if (in_array($filtre['oncelik'], $oncelikler)) {
    $kosullar[] = 'i.oncelik = ?';
    $parametreler[] = $filtre['oncelik'];
}
if ($filtre['mahalle_id'] != '') {
    $kosullar[] = 'i.mahalle_id = ?';
    $parametreler[] = (int) $filtre['mahalle_id'];
}

$sql = "SELECT i.*, k.ad AS kategori, m.ad AS mahalle, m.ilce, u.ad_soyad AS bildiren,
               (SELECT COUNT(*) FROM destekler d WHERE d.ihbar_id = i.id) AS destek
        FROM ihbarlar i
        JOIN kategoriler k ON k.id = i.kategori_id
        JOIN mahalleler m ON m.id = i.mahalle_id
        JOIN kullanicilar u ON u.id = i.kullanici_id";
if (count($kosullar) > 0) {
    $sql .= ' WHERE ' . implode(' AND ', $kosullar);
}
$sql .= " ORDER BY i.olusturma_tarihi DESC";
$ihbarlar = satirlar_getir($sql, $parametreler);

$sayfa_basligi = 'İhbarlar';
$aktif_sayfa = 'ihbarlar';
require 'includes/header.php';
?>

<section class="sayfa-ust">
    <div class="kapsayici">
        <h1>İhbar Yönetimi</h1>
        <p>Sistemdeki tüm ihbarları filtreleyin, inceleyin ve durumlarını güncelleyin.</p>
    </div>
</section>

<div class="kapsayici sayfa-govde">
    <?php mesajlari_goster(); ?>

    <form class="kart filtre-cubugu" method="get">
        <div class="form-satir">
            <label for="durum">Durum</label>
            <select id="durum" name="durum">
                <option value="">Tümü</option>
                <?php foreach ($durumlar as $d): ?>
                    <option <?= secili($d, $filtre['durum']) ?>><?= $d ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-satir">
            <label for="kategori_id">Kategori</label>
            <select id="kategori_id" name="kategori_id">
                <option value="">Tümü</option>
                <?php foreach ($kategoriler as $k): ?>
                    <option value="<?= $k['id'] ?>" <?= secili($k['id'], $filtre['kategori_id']) ?>><?= e($k['ad']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-satir">
            <label for="oncelik">Öncelik</label>
            <select id="oncelik" name="oncelik">
                <option value="">Tümü</option>
                <?php foreach ($oncelikler as $o): ?>
                    <option <?= secili($o, $filtre['oncelik']) ?>><?= $o ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-satir">
            <label for="mahalle_id">Mahalle</label>
            <select id="mahalle_id" name="mahalle_id">
                <option value="">Tümü</option>
                <?= mahalle_secenekleri($filtre['mahalle_id']) ?>
            </select>
        </div>
        <button type="submit" class="buton"><?= ikon('filtre', 16) ?> Filtrele</button>
        <a href="ihbarlar.php" class="buton buton-gri">Temizle</a>
    </form>

    <p style="color:var(--yazi-soluk)"><?= count($ihbarlar) ?> ihbar listeleniyor.</p>

    <div class="tablo-kutu kart" style="padding:0">
        <table class="tablo">
            <tr><th>İhbar</th><th>Konum</th><th>Bildiren</th><th>Öncelik</th><th>Durum</th><th>Tarih</th><th class="sag">İşlemler</th></tr>
            <?php foreach ($ihbarlar as $i): ?>
                <tr>
                    <td>
                        <a href="ihbar_detay.php?id=<?= $i['id'] ?>"><strong><?= e($i['baslik']) ?></strong></a>
                        <small><?= e($i['takip_no']) ?> · <?= e($i['kategori']) ?> · <?= $i['destek'] ?> destek</small>
                    </td>
                    <td><?= e($i['mahalle']) ?><small><?= e($i['ilce']) ?></small></td>
                    <td><?= e($i['bildiren']) ?></td>
                    <td><?= oncelik_etiketi($i['oncelik']) ?></td>
                    <td><?= durum_etiketi($i['durum']) ?></td>
                    <td><?= tarih_tr($i['olusturma_tarihi'], false) ?><small><?= ne_zaman($i['olusturma_tarihi']) ?></small></td>
                    <td>
                        <div class="islemler" style="justify-content:flex-end">
                            <a href="ihbar_detay.php?id=<?= $i['id'] ?>" class="buton buton-gri buton-ikon" title="İncele"><?= ikon('goz', 16) ?></a>
                            <?php if ($oturum['rol'] == 'admin'): ?>
                                <form action="ihbar_sil.php" method="post" data-onay="<?= e($i['takip_no']) ?> numaralı ihbar ve bağlı tüm kayıtlar kalıcı olarak silinecek. Emin misiniz?">
                                    <input type="hidden" name="id" value="<?= $i['id'] ?>">
                                    <button type="submit" class="buton buton-tehlike buton-ikon" title="Sil"><?= ikon('sil', 16) ?></button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (count($ihbarlar) == 0): ?>
                <tr><td colspan="7" class="bos-durum">Seçilen filtrelere uygun ihbar bulunamadı.</td></tr>
            <?php endif; ?>
        </table>
    </div>
</div>

<?php require 'includes/footer.php'; ?>
