<?php
require_once 'includes/baslangic.php';

$oturum = yetki_kontrol(['vatandas']);

$kategoriler = satirlar_getir("SELECT * FROM kategoriler WHERE aktif = 1 ORDER BY id");
$form = [
    'kategori_id' => '', 'mahalle_id' => $oturum['mahalle_id'], 'baslik' => '',
    'aciklama' => '', 'adres' => '', 'oncelik' => 'Normal', 'foto' => '',
];
$hatalar = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $hatalar = ihbar_formunu_oku($form);

    $foto = null;
    if (count($hatalar) == 0 && !empty($_FILES['foto']['name'])) {
        $foto_hatasi = '';
        $foto = foto_yukle($_FILES['foto'], $foto_hatasi);
        if ($foto === false) {
            $hatalar[] = $foto_hatasi;
        }
    }

    if (count($hatalar) == 0) {
        // Harita 4. haftada eklenecek, şimdilik mahallenin merkez koordinatı kullanılıyor
        $mahalle = satir_getir("SELECT enlem, boylam FROM mahalleler WHERE id = ?", [$form['mahalle_id']]);
        $takip_no = takip_no_uret();

        sorgu(
            "INSERT INTO ihbarlar (takip_no, kullanici_id, kategori_id, mahalle_id, baslik, aciklama, adres, enlem, boylam, foto, oncelik)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
            [$takip_no, $oturum['id'], $form['kategori_id'], $form['mahalle_id'], $form['baslik'],
             $form['aciklama'], $form['adres'], $mahalle['enlem'], $mahalle['boylam'], $foto, $form['oncelik']]
        );
        $yeni_id = mysqli_insert_id($baglanti);

        // Fotoğraflı ihbar 50, fotoğrafsız ihbar 10 KentPuan kazandırır
        $puan = $foto ? 50 : 10;
        sorgu("UPDATE kullanicilar SET kent_puan = kent_puan + ? WHERE id = ?", [$puan, $oturum['id']]);

        mesaj_ekle('basari', 'İhbarınız alındı. Takip numaranız: ' . $takip_no . ' (+' . $puan . ' KentPuan)');
        yonlendir('ihbar_detay.php?id=' . $yeni_id);
    }
}

$sayfa_basligi = 'İhbar Oluştur';
$aktif_sayfa = 'olustur';
require 'includes/header.php';
?>

<section class="sayfa-ust">
    <div class="kapsayici">
        <h1>Yeni İhbar</h1>
        <p>Bildirdiğiniz sorun, türüne göre ilgili belediye birimine iletilecek.</p>
    </div>
</section>

<div class="kapsayici sayfa-govde" style="max-width:900px">
    <?php
    $duzenleme = false;
    require 'includes/ihbar_formu.php';
    ?>
</div>

<?php require 'includes/footer.php'; ?>
