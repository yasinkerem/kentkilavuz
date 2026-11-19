<?php
$ekiplerim = satirlar_getir(
    "SELECT e.*, k.ad AS kategori FROM ekipler e
     JOIN kategoriler k ON k.id = e.kategori_id
     WHERE e.sef_id = ?",
    [$bilgi['id']]
);

$aktif_isler = satirlar_getir(
    "SELECT i.*, s.atanma_tarihi, s.sevk_notu, e.ekip_kodu, m.ad AS mahalle, m.ilce, k.ad AS kategori
     FROM sevkler s
     JOIN ekipler e ON e.id = s.ekip_id
     JOIN ihbarlar i ON i.id = s.ihbar_id
     JOIN mahalleler m ON m.id = i.mahalle_id
     JOIN kategoriler k ON k.id = i.kategori_id
     WHERE e.sef_id = ? AND s.tamamlandi = 0
     ORDER BY FIELD(i.oncelik, 'Acil', 'Yüksek', 'Normal', 'Düşük'), s.atanma_tarihi",
    [$bilgi['id']]
);

$tamamlanan = deger_getir(
    "SELECT COUNT(*) FROM sevkler s JOIN ekipler e ON e.id = s.ekip_id WHERE e.sef_id = ? AND s.tamamlandi = 1",
    [$bilgi['id']]
);
?>

<div class="izgara izgara-3 bosluk-alt">
    <div class="kart istatistik istatistik-kart" style="--renk:#2563eb">
        <div class="istatistik-ikon"><?= ikon('kamyon', 22) ?></div>
        <div><strong><?= count($ekiplerim) ?></strong><span>Sorumlu olduğum ekip</span></div>
    </div>
    <div class="kart istatistik istatistik-kart" style="--renk:#d97706">
        <div class="istatistik-ikon"><?= ikon('saat', 22) ?></div>
        <div><strong><?= count($aktif_isler) ?></strong><span>Açık iş emri</span></div>
    </div>
    <div class="kart istatistik istatistik-kart" style="--renk:#16a34a">
        <div class="istatistik-ikon"><?= ikon('onay', 22) ?></div>
        <div><strong><?= $tamamlanan ?></strong><span>Tamamlanan iş</span></div>
    </div>
</div>

<div class="kart bosluk-alt">
    <h3>Açık iş emirleri</h3>
    <?php if (count($aktif_isler) == 0): ?>
        <div class="bos-durum">Ekiplerinize atanmış açık iş bulunmuyor.</div>
    <?php else: ?>
        <div class="tablo-kutu">
            <table class="tablo">
                <tr><th>İhbar</th><th>Konum</th><th>Ekip</th><th>Öncelik</th><th>Durum</th><th></th></tr>
                <?php foreach ($aktif_isler as $is): ?>
                    <tr>
                        <td><?= e($is['baslik']) ?><small><?= e($is['takip_no']) ?> · <?= e($is['kategori']) ?></small></td>
                        <td><?= e($is['mahalle']) ?>, <?= e($is['ilce']) ?><small><?= e($is['adres']) ?></small></td>
                        <td><?= e($is['ekip_kodu']) ?><small><?= ne_zaman($is['atanma_tarihi']) ?></small></td>
                        <td><?= oncelik_etiketi($is['oncelik']) ?></td>
                        <td><?= durum_etiketi($is['durum']) ?></td>
                        <td><a href="ihbar_detay.php?id=<?= $is['id'] ?>" class="buton buton-kucuk">Detay</a></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
        <p style="color:var(--yazi-soluk);font-size:14px;margin-bottom:0">
            Çalışmaya başlama ve kanıt fotoğrafıyla işi kapatma işlemleri saha modülüyle birlikte eklenecek.
        </p>
    <?php endif; ?>
</div>

<div class="izgara izgara-3">
    <?php foreach ($ekiplerim as $ekip): ?>
        <div class="kart">
            <h3 style="margin-bottom:4px"><?= e($ekip['ad']) ?></h3>
            <div class="kucuk-bilgi" style="margin-bottom:10px">
                <span><?= e($ekip['ekip_kodu']) ?></span><span><?= e($ekip['plaka']) ?></span>
            </div>
            <p style="margin:0 0 8px;color:var(--yazi-soluk)"><?= e($ekip['kategori']) ?></p>
            <span class="etiket <?= $ekip['durum'] == 'Müsait' ? 'etiket-cozuldu' : 'etiket-yolda' ?>"><?= e($ekip['durum']) ?></span>
        </div>
    <?php endforeach; ?>
</div>
