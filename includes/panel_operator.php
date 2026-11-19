<?php
$sayilar = satir_getir(
    "SELECT SUM(durum = 'Bekliyor') AS bekleyen,
            SUM(durum = 'Bekliyor' AND oncelik = 'Acil') AS acil,
            SUM(durum IN ('Ekip Yolda', 'Çalışılıyor')) AS sahada,
            SUM(DATE(olusturma_tarihi) = CURDATE()) AS bugun
     FROM ihbarlar"
);

// Acil olanlar en üstte, sonra eskiden yeniye
$bekleyenler = satirlar_getir(
    "SELECT i.*, k.ad AS kategori, m.ad AS mahalle,
            (SELECT COUNT(*) FROM destekler d WHERE d.ihbar_id = i.id) AS destek
     FROM ihbarlar i
     JOIN kategoriler k ON k.id = i.kategori_id
     JOIN mahalleler m ON m.id = i.mahalle_id
     WHERE i.durum = 'Bekliyor'
     ORDER BY FIELD(i.oncelik, 'Acil', 'Yüksek', 'Normal', 'Düşük'), i.olusturma_tarihi
     LIMIT 8"
);

$ekipler = satirlar_getir(
    "SELECT e.*, k.ad AS kategori FROM ekipler e
     JOIN kategoriler k ON k.id = e.kategori_id
     ORDER BY FIELD(e.durum, 'Müsait', 'Görevde', 'Bakımda'), e.ekip_kodu"
);
?>

<div class="izgara izgara-4 bosluk-alt">
    <div class="kart istatistik istatistik-kart" style="--renk:#d97706">
        <div class="istatistik-ikon"><?= ikon('saat', 22) ?></div>
        <div><strong><?= (int) $sayilar['bekleyen'] ?></strong><span>Bekleyen ihbar</span></div>
    </div>
    <div class="kart istatistik istatistik-kart" style="--renk:#dc2626">
        <div class="istatistik-ikon"><?= ikon('uyari', 22) ?></div>
        <div><strong><?= (int) $sayilar['acil'] ?></strong><span>Acil bekleyen</span></div>
    </div>
    <div class="kart istatistik istatistik-kart" style="--renk:#2563eb">
        <div class="istatistik-ikon"><?= ikon('kamyon', 22) ?></div>
        <div><strong><?= (int) $sayilar['sahada'] ?></strong><span>Sahada</span></div>
    </div>
    <div class="kart istatistik istatistik-kart" style="--renk:#16a34a">
        <div class="istatistik-ikon"><?= ikon('arti', 22) ?></div>
        <div><strong><?= (int) $sayilar['bugun'] ?></strong><span>Bugün gelen</span></div>
    </div>
</div>

<div class="izgara duzen-sag">
    <div class="kart">
        <div class="kart-baslik">
            <h3>Öncelikli bekleyen ihbarlar</h3>
            <a href="ihbarlar.php?durum=Bekliyor">Tümü</a>
        </div>
        <div class="tablo-kutu">
            <table class="tablo">
                <tr><th>İhbar</th><th>Öncelik</th><th>Destek</th><th>Geliş</th><th></th></tr>
                <?php foreach ($bekleyenler as $i): ?>
                    <tr>
                        <td>
                            <?= e($i['baslik']) ?>
                            <small><?= e($i['takip_no']) ?> · <?= e($i['kategori']) ?> · <?= e($i['mahalle']) ?></small>
                        </td>
                        <td><?= oncelik_etiketi($i['oncelik']) ?></td>
                        <td><?= $i['destek'] ?></td>
                        <td><?= ne_zaman($i['olusturma_tarihi']) ?></td>
                        <td><a href="ihbar_detay.php?id=<?= $i['id'] ?>" class="buton buton-kucuk">İncele</a></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (count($bekleyenler) == 0): ?>
                    <tr><td colspan="5" class="bos-durum">Bekleyen ihbar yok.</td></tr>
                <?php endif; ?>
            </table>
        </div>
    </div>

    <div class="kart">
        <h3>Saha ekipleri</h3>
        <?php foreach ($ekipler as $ekip): ?>
            <div class="yorum">
                <div style="flex:1">
                    <strong><?= e($ekip['ekip_kodu']) ?></strong> - <?= e($ekip['ad']) ?>
                    <div class="yorum-ust"><?= e($ekip['kategori']) ?> · <?= e($ekip['plaka']) ?></div>
                </div>
                <span class="etiket <?= $ekip['durum'] == 'Müsait' ? 'etiket-cozuldu' : ($ekip['durum'] == 'Görevde' ? 'etiket-yolda' : 'etiket-iptal') ?>">
                    <?= e($ekip['durum']) ?>
                </span>
            </div>
        <?php endforeach; ?>
    </div>
</div>
