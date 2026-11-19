<?php
require_once 'includes/baslangic.php';

$oturum = yetki_kontrol(['vatandas']);

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    yonlendir('ihbarlarim.php');
}

$id = (int) ($_POST['id'] ?? 0);
$ihbar = satir_getir("SELECT * FROM ihbarlar WHERE id = ? AND kullanici_id = ?", [$id, $oturum['id']]);

if (!$ihbar) {
    mesaj_ekle('hata', 'İhbar bulunamadı.');
} elseif ($ihbar['durum'] != 'Bekliyor') {
    mesaj_ekle('hata', 'Yalnızca bekleyen ihbarlar iptal edilebilir.');
} else {
    sorgu("UPDATE ihbarlar SET durum = 'İptal', guncelleme_tarihi = NOW() WHERE id = ?", [$id]);
    $puan = $ihbar['foto'] ? 50 : 10;
    sorgu("UPDATE kullanicilar SET kent_puan = GREATEST(kent_puan - ?, 0) WHERE id = ?", [$puan, $oturum['id']]);
    mesaj_ekle('basari', $ihbar['takip_no'] . ' numaralı ihbarınız iptal edildi.');
}

yonlendir('ihbarlarim.php');
