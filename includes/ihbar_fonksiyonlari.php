<?php
// ---------- İhbar işlemleri ----------

function takip_no_uret()
{
    $yil = date('Y');
    $son = deger_getir(
        "SELECT MAX(CAST(SUBSTRING(takip_no, 9) AS UNSIGNED)) FROM ihbarlar WHERE takip_no LIKE ?",
        ['KK-' . $yil . '-%']
    );
    return 'KK-' . $yil . '-' . str_pad((int) $son + 1, 4, '0', STR_PAD_LEFT);
}

// Yüklenen fotoğrafı kontrol edip uploads klasörüne kaydeder.
// Başarılıysa dosya yolunu, hata varsa false döndürür ve $hata değişkenini doldurur.
function foto_yukle($dosya, &$hata)
{
    $izinli_turler = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    $azami_boyut = 2 * 1024 * 1024;

    if ($dosya['error'] != UPLOAD_ERR_OK) {
        $hata = 'Fotoğraf yüklenemedi (hata kodu: ' . $dosya['error'] . ').';
        return false;
    }
    if ($dosya['size'] > $azami_boyut) {
        $hata = 'Fotoğraf en fazla 2 MB olabilir.';
        return false;
    }

    $tur = mime_content_type($dosya['tmp_name']);
    if (!isset($izinli_turler[$tur])) {
        $hata = 'Yalnızca JPG, PNG veya WEBP fotoğraf yükleyebilirsiniz.';
        return false;
    }

    $yeni_ad = 'uploads/ihbarlar/' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $izinli_turler[$tur];
    if (!move_uploaded_file($dosya['tmp_name'], __DIR__ . '/../' . $yeni_ad)) {
        $hata = 'Fotoğraf sunucuya kaydedilemedi.';
        return false;
    }
    return $yeni_ad;
}

function foto_sil($yol)
{
    if ($yol && file_exists(__DIR__ . '/../' . $yol)) {
        unlink(__DIR__ . '/../' . $yol);
    }
}

// Vatandaş yalnızca kendi ihbarını, saha şefi yalnızca ekibine atananları görebilir
function ihbari_gorebilir_mi($ihbar, $kullanici)
{
    if (in_array($kullanici['rol'], ['admin', 'operator'])) {
        return true;
    }
    if ($kullanici['rol'] == 'vatandas') {
        return $ihbar['kullanici_id'] == $kullanici['id'];
    }
    return deger_getir(
        "SELECT COUNT(*) FROM sevkler s JOIN ekipler e ON e.id = s.ekip_id WHERE s.ihbar_id = ? AND e.sef_id = ?",
        [$ihbar['id'], $kullanici['id']]
    ) > 0;
}

// Formdan gelen ihbar alanlarını okur ve kontrol eder, hataları dizi olarak döndürür
function ihbar_formunu_oku(&$form)
{
    $form['kategori_id'] = (int) ($_POST['kategori_id'] ?? 0);
    $form['mahalle_id'] = (int) ($_POST['mahalle_id'] ?? 0);
    $form['baslik'] = trim($_POST['baslik'] ?? '');
    $form['aciklama'] = trim($_POST['aciklama'] ?? '');
    $form['adres'] = trim($_POST['adres'] ?? '');
    $form['oncelik'] = $_POST['oncelik'] ?? '';

    $hatalar = [];
    if (!deger_getir("SELECT COUNT(*) FROM kategoriler WHERE id = ? AND aktif = 1", [$form['kategori_id']])) {
        $hatalar[] = 'Sorunun türünü seçin.';
    }
    if (mb_strlen($form['baslik']) < 5) {
        $hatalar[] = 'Başlık en az 5 karakter olmalı.';
    }
    if (mb_strlen($form['aciklama']) < 20) {
        $hatalar[] = 'Açıklama en az 20 karakter olmalı.';
    }
    if (!in_array($form['oncelik'], ['Düşük', 'Normal', 'Yüksek', 'Acil'])) {
        $hatalar[] = 'Öncelik seçin.';
    }
    if (!deger_getir("SELECT COUNT(*) FROM mahalleler WHERE id = ?", [$form['mahalle_id']])) {
        $hatalar[] = 'Mahalle seçin.';
    }
    return $hatalar;
}
