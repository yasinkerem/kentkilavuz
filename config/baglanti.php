<?php
require_once __DIR__ . '/ayarlar.php';

try {
    $baglanti = mysqli_connect(DB_HOST, DB_KULLANICI, DB_PAROLA, '', DB_PORT);
} catch (mysqli_sql_exception $hata) {
    http_response_code(500);
    die('<div style="font-family:sans-serif;max-width:600px;margin:60px auto;padding:24px;border:1px solid #fca5a5;background:#fef2f2;border-radius:8px">'
        . '<h2 style="margin-top:0;color:#b91c1c">Veritabanına bağlanılamadı</h2>'
        . '<p>WAMP simgesinin yeşil olduğundan emin olun. Bağlantı ayarları <code>config/ayarlar.php</code> dosyasındadır.</p>'
        . '<small style="color:#666">' . htmlspecialchars($hata->getMessage()) . '</small></div>');
}

mysqli_set_charset($baglanti, 'utf8mb4');

// Veritabanı yoksa ilk açılışta SQL dosyasını çalıştırıp kuruyoruz
$vt_var = mysqli_query($baglanti, "SHOW DATABASES LIKE '" . DB_ADI . "'");
if (mysqli_num_rows($vt_var) == 0) {
    $sql = file_get_contents(__DIR__ . '/../veritabani/kent_kilavuz.sql');
    mysqli_multi_query($baglanti, $sql);
    while (mysqli_more_results($baglanti)) {
        mysqli_next_result($baglanti);
    }
}

mysqli_select_db($baglanti, DB_ADI);
mysqli_query($baglanti, "SET time_zone = '+03:00'");
