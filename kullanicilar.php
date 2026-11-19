<?php
require_once 'includes/baslangic.php';

$oturum = yetki_kontrol(['admin']);

$roller = ['admin', 'operator', 'saha', 'vatandas'];
$secili_rol = in_array($_GET['rol'] ?? '', $roller) ? $_GET['rol'] : '';
$arama = trim($_GET['ara'] ?? '');

$sql = "SELECT k.*, m.ad AS mahalle,
               (SELECT COUNT(*) FROM ihbarlar i WHERE i.kullanici_id = k.id) AS ihbar_sayisi
        FROM kullanicilar k
        LEFT JOIN mahalleler m ON m.id = k.mahalle_id
        WHERE 1 = 1";
$parametreler = [];
if ($secili_rol != '') {
    $sql .= " AND k.rol = ?";
    $parametreler[] = $secili_rol;
}
if ($arama != '') {
    $sql .= " AND (k.ad_soyad LIKE ? OR k.eposta LIKE ?)";
    $parametreler[] = '%' . $arama . '%';
    $parametreler[] = '%' . $arama . '%';
}
$sql .= " ORDER BY FIELD(k.rol, 'admin', 'operator', 'saha', 'vatandas'), k.ad_soyad";
$kullanicilar = satirlar_getir($sql, $parametreler);

$sayilar = [];
foreach (satirlar_getir("SELECT rol, COUNT(*) AS sayi FROM kullanicilar GROUP BY rol") as $s) {
    $sayilar[$s['rol']] = $s['sayi'];
}

$sayfa_basligi = 'Kullanıcılar';
$aktif_sayfa = 'kullanicilar';
require 'includes/header.php';
?>

<section class="sayfa-ust">
    <div class="kapsayici sayfa-ust-ic">
        <div>
            <h1>Kullanıcı Yönetimi</h1>
            <p>Personel ve vatandaş hesaplarını ekleyin, düzenleyin, rollerini belirleyin.</p>
        </div>
        <a href="kullanici_form.php" class="buton buton-vurgu"><?= ikon('arti', 18) ?> Yeni Kullanıcı</a>
    </div>
</section>

<div class="kapsayici sayfa-govde">
    <?php mesajlari_goster(); ?>

    <div class="sayfa-ust-ic" style="margin-bottom:6px">
        <nav class="sekmeler">
            <a href="kullanicilar.php" class="<?= $secili_rol == '' ? 'aktif' : '' ?>">Tümü<span><?= array_sum($sayilar) ?></span></a>
            <?php foreach ($roller as $r): ?>
                <a href="kullanicilar.php?rol=<?= $r ?>" class="<?= $secili_rol == $r ? 'aktif' : '' ?>"><?= rol_adi($r) ?><span><?= $sayilar[$r] ?? 0 ?></span></a>
            <?php endforeach; ?>
        </nav>
        <form method="get" class="arama-satiri" style="margin-bottom:18px;min-width:280px">
            <?php if ($secili_rol): ?><input type="hidden" name="rol" value="<?= e($secili_rol) ?>"><?php endif; ?>
            <input type="text" name="ara" value="<?= e($arama) ?>" placeholder="Ad veya e-posta ara">
            <button type="submit" class="buton"><?= ikon('ara', 16) ?></button>
        </form>
    </div>

    <div class="tablo-kutu kart" style="padding:0">
        <table class="tablo">
            <tr><th>Kullanıcı</th><th>Telefon</th><th>Rol</th><th>Mahalle</th><th>İhbar</th><th>Durum</th><th class="sag">İşlemler</th></tr>
            <?php foreach ($kullanicilar as $k): ?>
                <tr>
                    <td>
                        <div style="display:flex;gap:10px;align-items:center">
                            <span class="avatar avatar-kucuk" style="width:36px;height:36px"><?= e(bas_harfler($k['ad_soyad'])) ?></span>
                            <div><strong><?= e($k['ad_soyad']) ?></strong><small><?= e($k['eposta']) ?></small></div>
                        </div>
                    </td>
                    <td><?= e($k['telefon'] ?: '-') ?></td>
                    <td><?= rol_adi($k['rol']) ?></td>
                    <td><?= e($k['mahalle'] ?? '-') ?></td>
                    <td><?= $k['ihbar_sayisi'] ?></td>
                    <td>
                        <span class="etiket <?= $k['aktif'] ? 'etiket-cozuldu' : 'etiket-iptal' ?>"><?= $k['aktif'] ? 'Aktif' : 'Pasif' ?></span>
                    </td>
                    <td>
                        <div class="islemler" style="justify-content:flex-end">
                            <a href="kullanici_form.php?id=<?= $k['id'] ?>" class="buton buton-gri buton-ikon" title="Düzenle"><?= ikon('duzenle', 16) ?></a>
                            <?php if ($k['id'] != $oturum['id']): ?>
                                <form action="kullanici_sil.php" method="post" data-onay="<?= e($k['ad_soyad']) ?> adlı kullanıcı silinsin mi?">
                                    <input type="hidden" name="id" value="<?= $k['id'] ?>">
                                    <button type="submit" class="buton buton-tehlike buton-ikon" title="Sil"><?= ikon('sil', 16) ?></button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (count($kullanicilar) == 0): ?>
                <tr><td colspan="7" class="bos-durum">Aramanıza uygun kullanıcı bulunamadı.</td></tr>
            <?php endif; ?>
        </table>
    </div>
</div>

<?php require 'includes/footer.php'; ?>
