<?php
// ---------- Veritabanı yardımcıları ----------

// Hazırlanmış sorgu (prepared statement) çalıştırır. Tüm parametreler string olarak bağlanır.
function sorgu($sql, $parametreler = [])
{
    global $baglanti;
    $komut = mysqli_prepare($baglanti, $sql);
    if (count($parametreler) > 0) {
        $tipler = str_repeat('s', count($parametreler));
        mysqli_stmt_bind_param($komut, $tipler, ...$parametreler);
    }
    mysqli_stmt_execute($komut);
    return $komut;
}

function satir_getir($sql, $parametreler = [])
{
    $sonuc = mysqli_stmt_get_result(sorgu($sql, $parametreler));
    $satir = mysqli_fetch_assoc($sonuc);
    return $satir ? $satir : null;
}

function satirlar_getir($sql, $parametreler = [])
{
    $sonuc = mysqli_stmt_get_result(sorgu($sql, $parametreler));
    return mysqli_fetch_all($sonuc, MYSQLI_ASSOC);
}

function deger_getir($sql, $parametreler = [])
{
    $sonuc = mysqli_stmt_get_result(sorgu($sql, $parametreler));
    $satir = mysqli_fetch_row($sonuc);
    return $satir ? $satir[0] : null;
}

// ---------- Genel yardımcılar ----------

function e($metin)
{
    return htmlspecialchars((string) $metin, ENT_QUOTES, 'UTF-8');
}

function yonlendir($sayfa)
{
    header('Location: ' . $sayfa);
    exit;
}

// Form hatalıysa kullanıcının yazdıklarını geri doldurmak için
function eski($alan, $varsayilan = '')
{
    return e($_POST[$alan] ?? $varsayilan);
}

function mesaj_ekle($tur, $metin)
{
    $_SESSION['mesajlar'][] = ['tur' => $tur, 'metin' => $metin];
}

function mesajlari_goster()
{
    if (empty($_SESSION['mesajlar'])) {
        return;
    }
    foreach ($_SESSION['mesajlar'] as $m) {
        echo '<div class="uyari uyari-' . e($m['tur']) . '">' . e($m['metin']) . '</div>';
    }
    unset($_SESSION['mesajlar']);
}

function hatalari_goster($hatalar)
{
    if (count($hatalar) == 0) {
        return;
    }
    echo '<div class="uyari uyari-hata"><strong>Lütfen aşağıdakileri düzeltin:</strong><ul>';
    foreach ($hatalar as $hata) {
        echo '<li>' . e($hata) . '</li>';
    }
    echo '</ul></div>';
}

// ---------- Form yardımcıları ----------

function secili($deger1, $deger2)
{
    return (string) $deger1 === (string) $deger2 ? 'selected' : '';
}

function mahalle_secenekleri($secili_id = '')
{
    $mahalleler = satirlar_getir("SELECT id, ilce, ad FROM mahalleler ORDER BY ilce, ad");
    $html = '';
    $onceki_ilce = '';
    foreach ($mahalleler as $m) {
        if ($m['ilce'] != $onceki_ilce) {
            if ($onceki_ilce != '') $html .= '</optgroup>';
            $html .= '<optgroup label="' . e($m['ilce']) . '">';
            $onceki_ilce = $m['ilce'];
        }
        $html .= '<option value="' . $m['id'] . '" ' . secili($m['id'], $secili_id) . '>' . e($m['ad']) . '</option>';
    }
    return $html . '</optgroup>';
}

// ---------- Oturum ----------

function giris_yapmis_mi()
{
    return isset($_SESSION['kullanici']);
}

function aktif_kullanici()
{
    return $_SESSION['kullanici'] ?? null;
}

function giris_zorunlu()
{
    if (!giris_yapmis_mi()) {
        mesaj_ekle('bilgi', 'Bu sayfayı görmek için giriş yapmalısınız.');
        yonlendir('giris.php');
    }
    return aktif_kullanici();
}

function rol_adi($rol)
{
    $roller = [
        'admin' => 'Yönetici',
        'operator' => 'Operatör',
        'saha' => 'Saha Şefi',
        'vatandas' => 'Vatandaş',
    ];
    return $roller[$rol] ?? $rol;
}

// ---------- Yetkilendirme ----------

// Sayfayı yalnızca verilen rollere açar, diğerlerine 403 sayfası gösterir
function yetki_kontrol($izinli_roller)
{
    $kullanici = giris_zorunlu();
    if (!in_array($kullanici['rol'], $izinli_roller)) {
        http_response_code(403);
        $sayfa_basligi = 'Yetkisiz Erişim';
        require __DIR__ . '/header.php';
        require __DIR__ . '/yetkisiz.php';
        require __DIR__ . '/footer.php';
        exit;
    }
    return $kullanici;
}

function rol_menusu($rol)
{
    $menuler = [
        'vatandas' => [
            'panel' => ['panel.php', 'Panelim'],
            'olustur' => ['ihbar_olustur.php', 'İhbar Oluştur'],
            'ihbarlarim' => ['ihbarlarim.php', 'İhbarlarım'],
            'sorgula' => ['ihbar_sorgula.php', 'İhbar Sorgula'],
            'yardim' => ['yardim.php', 'Yardım'],
        ],
        'operator' => [
            'panel' => ['panel.php', 'Panel'],
            'ihbarlar' => ['ihbarlar.php', 'İhbarlar'],
            'sorgula' => ['ihbar_sorgula.php', 'İhbar Sorgula'],
            'yardim' => ['yardim.php', 'Yardım'],
        ],
        'saha' => [
            'panel' => ['panel.php', 'İş Emirlerim'],
            'sorgula' => ['ihbar_sorgula.php', 'İhbar Sorgula'],
            'yardim' => ['yardim.php', 'Yardım'],
        ],
        'admin' => [
            'panel' => ['panel.php', 'Panel'],
            'ihbarlar' => ['ihbarlar.php', 'İhbarlar'],
            'kullanicilar' => ['kullanicilar.php', 'Kullanıcılar'],
        ],
    ];
    return $menuler[$rol] ?? [];
}
