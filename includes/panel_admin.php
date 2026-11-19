<?php
$sayilar = satir_getir(
    "SELECT COUNT(*) AS toplam,
            SUM(durum = 'Çözüldü') AS cozulen,
            SUM(durum IN ('Bekliyor', 'Ekip Yolda', 'Çalışılıyor')) AS acik
     FROM ihbarlar WHERE durum <> 'İptal'"
);
$cozum_orani = $sayilar['toplam'] > 0 ? round($sayilar['cozulen'] * 100 / $sayilar['toplam']) : 0;

$kullanici_sayisi = deger_getir("SELECT COUNT(*) FROM kullanicilar WHERE aktif = 1");
$okunmamis_mesaj = deger_getir("SELECT COUNT(*) FROM iletisim_mesajlari WHERE okundu = 0");

$rol_dagilimi = satirlar_getir("SELECT rol, COUNT(*) AS sayi FROM kullanicilar GROUP BY rol");

$kategori_dagilimi = satirlar_getir(
    "SELECT k.ad, k.renk, COUNT(i.id) AS sayi
     FROM kategoriler k
     LEFT JOIN ihbarlar i ON i.kategori_id = k.id AND i.durum <> 'İptal'
     GROUP BY k.id ORDER BY sayi DESC"
);
$en_cok = max(array_column($kategori_dagilimi, 'sayi')) ?: 1;

$son_kullanicilar = satirlar_getir(
    "SELECT k.*, m.ad AS mahalle FROM kullanicilar k
     LEFT JOIN mahalleler m ON m.id = k.mahalle_id
     ORDER BY k.kayit_tarihi DESC LIMIT 5"
);
?>

<div class="izgara izgara-4 bosluk-alt">
    <div class="kart istatistik istatistik-kart" style="--renk:#0f5ea8">
        <div class="istatistik-ikon"><?= ikon('liste', 22) ?></div>
        <div><strong><?= (int) $sayilar['toplam'] ?></strong><span>Toplam ihbar</span></div>
    </div>
    <div class="kart istatistik istatistik-kart" style="--renk:#16a34a">
        <div class="istatistik-ikon"><?= ikon('onay', 22) ?></div>
        <div><strong>%<?= $cozum_orani ?></strong><span>Çözüm oranı</span></div>
    </div>
    <div class="kart istatistik istatistik-kart" style="--renk:#7c3aed">
        <div class="istatistik-ikon"><?= ikon('kisiler', 22) ?></div>
        <div><strong><?= $kullanici_sayisi ?></strong><span>Aktif kullanıcı</span></div>
    </div>
    <div class="kart istatistik istatistik-kart" style="--renk:#d97706">
        <div class="istatistik-ikon"><?= ikon('mesaj', 22) ?></div>
        <div><strong><?= $okunmamis_mesaj ?></strong><span>Okunmamış mesaj</span></div>
    </div>
</div>

<div class="izgara izgara-2 bosluk-alt">
    <div class="kart">
        <h3>Kategorilere göre ihbarlar</h3>
        <?php foreach ($kategori_dagilimi as $k): ?>
            <div class="cubuk-satir" style="--renk:<?= e($k['renk']) ?>">
                <span><?= e($k['ad']) ?></span>
                <div class="cubuk"><i style="width:<?= round($k['sayi'] * 100 / $en_cok) ?>%"></i></div>
                <strong><?= $k['sayi'] ?></strong>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="kart">
        <h3>Kullanıcı dağılımı</h3>
        <div class="izgara izgara-2" style="gap:12px">
            <?php foreach ($rol_dagilimi as $r): ?>
                <a href="kullanicilar.php?rol=<?= e($r['rol']) ?>" class="kart" style="padding:16px;box-shadow:none;color:inherit;text-decoration:none">
                    <strong style="font-size:24px"><?= $r['sayi'] ?></strong><br>
                    <span style="color:var(--yazi-soluk)"><?= rol_adi($r['rol']) ?></span>
                </a>
            <?php endforeach; ?>
        </div>
        <p style="margin:16px 0 0;color:var(--yazi-soluk);font-size:14px"><?= (int) $sayilar['acik'] ?> ihbar şu anda açık durumda.</p>
    </div>
</div>

<div class="kart">
    <div class="kart-baslik">
        <h3>Son kayıt olan kullanıcılar</h3>
        <a href="kullanicilar.php">Kullanıcı yönetimi</a>
    </div>
    <div class="tablo-kutu">
        <table class="tablo">
            <tr><th>Ad Soyad</th><th>E-posta</th><th>Rol</th><th>Mahalle</th><th>Kayıt</th></tr>
            <?php foreach ($son_kullanicilar as $k): ?>
                <tr>
                    <td><?= e($k['ad_soyad']) ?></td>
                    <td><?= e($k['eposta']) ?></td>
                    <td><?= rol_adi($k['rol']) ?></td>
                    <td><?= e($k['mahalle'] ?? '-') ?></td>
                    <td><?= tarih_tr($k['kayit_tarihi'], false) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</div>
