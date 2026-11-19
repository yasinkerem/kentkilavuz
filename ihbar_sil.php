<?php
require_once 'includes/baslangic.php';

yetki_kontrol(['admin']);

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    yonlendir('ihbarlar.php');
}

$id = (int) ($_POST['id'] ?? 0);
$ihbar = satir_getir("SELECT id, takip_no, foto FROM ihbarlar WHERE id = ?", [$id]);

if (!$ihbar) {
    mesaj_ekle('hata', 'Silinecek ihbar bulunamadı.');
} else {
    // Sevk, destek ve yorum kayıtları ON DELETE CASCADE ile birlikte silinir
    sorgu("DELETE FROM ihbarlar WHERE id = ?", [$id]);
    foto_sil($ihbar['foto']);
    mesaj_ekle('basari', $ihbar['takip_no'] . ' numaralı ihbar silindi.');
}

yonlendir('ihbarlar.php');
