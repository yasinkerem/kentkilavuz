<?php
require_once 'includes/baslangic.php';

$oturum = yetki_kontrol(['vatandas']);

$id = (int) ($_GET['id'] ?? 0);
$ihbar = satir_getir("SELECT * FROM ihbarlar WHERE id = ? AND kullanici_id = ?", [$id, $oturum['id']]);

if (!$ihbar) {
    mesaj_ekle('hata', 'İhbar bulunamadı.');
    yonlendir('ihbarlarim.php');
}
if ($ihbar['durum'] != 'Bekliyor') {
    mesaj_ekle('hata', 'Ekip yönlendirilen ihbarlar düzenlenemez.');
    yonlendir('ihbar_detay.php?id=' . $id);
}

$kategoriler = satirlar_getir("SELECT * FROM kategoriler WHERE aktif = 1 ORDER BY id");
$form = $ihbar;
$hatalar = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $hatalar = ihbar_formunu_oku($form);

    $yeni_foto = $ihbar['foto'];
    if (count($hatalar) == 0 && !empty($_FILES['foto']['name'])) {
        $foto_hatasi = '';
        $yuklenen = foto_yukle($_FILES['foto'], $foto_hatasi);
        if ($yuklenen === false) {
            $hatalar[] = $foto_hatasi;
        } else {
            $yeni_foto = $yuklenen;
        }
    } elseif (isset($_POST['foto_kaldir'])) {
        $yeni_foto = null;
    }

    if (count($hatalar) == 0) {
        // Mahalle değiştiyse konumu yeni mahallenin merkezine taşıyoruz
        $konum = satir_getir("SELECT enlem, boylam FROM mahalleler WHERE id = ?", [$form['mahalle_id']]);
        if ($form['mahalle_id'] == $ihbar['mahalle_id']) {
            $konum = ['enlem' => $ihbar['enlem'], 'boylam' => $ihbar['boylam']];
        }

        sorgu(
            "UPDATE ihbarlar SET kategori_id = ?, mahalle_id = ?, baslik = ?, aciklama = ?, adres = ?,
                    oncelik = ?, foto = ?, enlem = ?, boylam = ?, guncelleme_tarihi = NOW()
             WHERE id = ?",
            [$form['kategori_id'], $form['mahalle_id'], $form['baslik'], $form['aciklama'], $form['adres'],
             $form['oncelik'], $yeni_foto, $konum['enlem'], $konum['boylam'], $id]
        );

        if ($yeni_foto != $ihbar['foto']) {
            foto_sil($ihbar['foto']);
        }

        mesaj_ekle('basari', 'İhbarınız güncellendi.');
        yonlendir('ihbar_detay.php?id=' . $id);
    }
}

$sayfa_basligi = 'İhbarı Düzenle';
$aktif_sayfa = 'ihbarlarim';
require 'includes/header.php';
?>

<section class="sayfa-ust">
    <div class="kapsayici">
        <h1>İhbarı Düzenle</h1>
        <p><?= e($ihbar['takip_no']) ?> · Ekip yönlendirilene kadar bilgileri değiştirebilirsiniz.</p>
    </div>
</section>

<div class="kapsayici sayfa-govde" style="max-width:900px">
    <?php
    $duzenleme = true;
    require 'includes/ihbar_formu.php';
    ?>
</div>

<?php require 'includes/footer.php'; ?>
