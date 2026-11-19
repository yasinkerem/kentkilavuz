<?php
require_once 'includes/baslangic.php';

$oturum = yetki_kontrol(['admin']);

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    yonlendir('kullanicilar.php');
}

$id = (int) ($_POST['id'] ?? 0);
$kullanici = satir_getir("SELECT id, ad_soyad FROM kullanicilar WHERE id = ?", [$id]);

if (!$kullanici) {
    mesaj_ekle('hata', 'Kullanıcı bulunamadı.');
} elseif ($id == $oturum['id']) {
    mesaj_ekle('hata', 'Kendi hesabınızı silemezsiniz.');
} else {
    try {
        sorgu("DELETE FROM kullanicilar WHERE id = ?", [$id]);
        mesaj_ekle('basari', $kullanici['ad_soyad'] . ' silindi.');
    } catch (mysqli_sql_exception $hata) {
        // 1451: kullanıcıya bağlı ihbar/sevk kaydı var, yabancı anahtar silmeye izin vermiyor
        if ($hata->getCode() == 1451) {
            sorgu("UPDATE kullanicilar SET aktif = 0 WHERE id = ?", [$id]);
            mesaj_ekle('bilgi', $kullanici['ad_soyad'] . ' adlı kullanıcının sistemde kayıtları olduğu için silinemedi, hesabı pasife alındı.');
        } else {
            mesaj_ekle('hata', 'Kullanıcı silinirken bir hata oluştu.');
        }
    }
}

yonlendir('kullanicilar.php');
