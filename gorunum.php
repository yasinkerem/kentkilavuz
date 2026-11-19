<?php
// ---------- Tarih gösterimi ----------

function tarih_tr($tarih, $saatli = true)
{
    if (!$tarih) {
        return '-';
    }
    $aylar = ['', 'Ocak', 'Şubat', 'Mart', 'Nisan', 'Mayıs', 'Haziran', 'Temmuz',
              'Ağustos', 'Eylül', 'Ekim', 'Kasım', 'Aralık'];
    $zaman = strtotime($tarih);
    $metin = date('j', $zaman) . ' ' . $aylar[date('n', $zaman)] . ' ' . date('Y', $zaman);
    if ($saatli) {
        $metin .= ', ' . date('H:i', $zaman);
    }
    return $metin;
}

// "3 saat önce" gibi kısa gösterim
function ne_zaman($tarih)
{
    $fark = time() - strtotime($tarih);
    if ($fark < 60) return 'az önce';
    if ($fark < 3600) return floor($fark / 60) . ' dakika önce';
    if ($fark < 86400) return floor($fark / 3600) . ' saat önce';
    if ($fark < 86400 * 7) return floor($fark / 86400) . ' gün önce';
    return tarih_tr($tarih, false);
}

// ---------- Etiketler ve ikonlar ----------

function durum_etiketi($durum)
{
    $siniflar = [
        'Bekliyor' => 'bekliyor',
        'Ekip Yolda' => 'yolda',
        'Çalışılıyor' => 'calisiliyor',
        'Çözüldü' => 'cozuldu',
        'İptal' => 'iptal',
    ];
    $sinif = $siniflar[$durum] ?? 'bekliyor';
    return '<span class="etiket etiket-' . $sinif . '">' . e($durum) . '</span>';
}

function oncelik_etiketi($oncelik)
{
    $siniflar = ['Düşük' => 'dusuk', 'Normal' => 'normal', 'Yüksek' => 'yuksek', 'Acil' => 'acil'];
    $sinif = $siniflar[$oncelik] ?? 'normal';
    return '<span class="oncelik oncelik-' . $sinif . '">' . e($oncelik) . '</span>';
}

function bas_harfler($ad_soyad)
{
    $parcalar = explode(' ', trim($ad_soyad));
    $harfler = mb_substr($parcalar[0], 0, 1);
    if (count($parcalar) > 1) {
        $harfler .= mb_substr(end($parcalar), 0, 1);
    }
    return mb_strtoupper($harfler);
}

function ikon($ad, $boyut = 20)
{
    $yollar = [
        'damla' => '<path d="M12 2.7l5.7 5.6a8 8 0 1 1-11.3 0z"/>',
        'yol' => '<path d="M4 22 8 2"/><path d="M20 22 16 2"/><path d="M12 4v3"/><path d="M12 11v3"/><path d="M12 18v3"/>',
        'agac' => '<path d="M12 2 5 12h4l-3 5h12l-3-5h4z"/><path d="M12 17v5"/>',
        'simsek' => '<path d="M13 2 3 14h9l-1 8 10-12h-9z"/>',
        'cop' => '<path d="M3 6h18"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/><path d="M10 11v6"/><path d="M14 11v6"/>',
        'kalkan' => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>',
        'uyari' => '<path d="M10.3 3.9 1.8 18a2 2 0 0 0 1.7 3h17a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0z"/><path d="M12 9v4"/><path d="M12 17h.01"/>',
        'ara' => '<circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>',
        'konum' => '<path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0z"/><circle cx="12" cy="10" r="3"/>',
        'onay' => '<path d="M22 11.1V12a10 10 0 1 1-5.9-9.1"/><path d="M22 4 12 14l-3-3"/>',
        'saat' => '<circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/>',
        'kisiler' => '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.9"/><path d="M16 3.1a4 4 0 0 1 0 7.8"/>',
        'kamera' => '<path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/>',
        'gonder' => '<path d="M22 2 11 13"/><path d="M22 2 15 22l-4-9-9-4z"/>',
        'kamyon' => '<path d="M1 3h15v13H1z"/><path d="M16 8h4l3 3v5h-7z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/>',
        'telefon' => '<path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1 1 .4 1.9.7 2.8a2 2 0 0 1-.5 2.1L8 9.9a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.4c.9.3 1.8.6 2.8.7a2 2 0 0 1 1.7 2z"/>',
        'posta' => '<path d="M4 4h16a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2z"/><path d="m22 6-10 7L2 6"/>',
        'kullanici' => '<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>',
        'cikis' => '<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="m16 17 5-5-5-5"/><path d="M21 12H9"/>',
        'liste' => '<path d="M8 6h13"/><path d="M8 12h13"/><path d="M8 18h13"/><path d="M3 6h.01"/><path d="M3 12h.01"/><path d="M3 18h.01"/>',
        'ev' => '<path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><path d="M9 22V12h6v10"/>',
        'arti' => '<path d="M12 5v14"/><path d="M5 12h14"/>',
        'duzenle' => '<path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4z"/>',
        'sil' => '<path d="M3 6h18"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M9 6V4h6v2"/>',
        'goz' => '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>',
        'kilit' => '<rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>',
        'yildiz' => '<path d="m12 2 3.1 6.3 6.9 1-5 4.9 1.2 6.8L12 17.8 5.8 21l1.2-6.8-5-4.9 6.9-1z"/>',
        'mesaj' => '<path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>',
        'filtre' => '<path d="M22 3H2l8 9.5V19l4 2v-8.5z"/>',
        'bina' => '<path d="M3 21h18"/><path d="M5 21V7l7-4 7 4v14"/><path d="M9 21v-6h6v6"/><path d="M9 10h.01"/><path d="M15 10h.01"/>',
    ];
    $yol = $yollar[$ad] ?? $yollar['uyari'];
    return '<svg class="ikon" width="' . $boyut . '" height="' . $boyut . '" viewBox="0 0 24 24" fill="none" '
        . 'stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">'
        . $yol . '</svg>';
}
