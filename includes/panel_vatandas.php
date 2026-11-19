<?php
$sayilar = satir_getir(
    "SELECT COUNT(*) AS toplam,
            SUM(durum = 'Bekliyor') AS bekleyen,
            SUM(durum IN ('Ekip Yolda', 'Çalışılıyor')) AS islemde,
            SUM(durum = 'Çözüldü') AS cozulen
     FROM ihbarlar WHERE kullanici_id = ?",
    [$bilgi['id']]
);

$son_ihbarlarim = satirlar_getir(
    "SELECT i.*, k.ad AS kategori FROM ihbarlar i
     JOIN kategoriler k ON k.id = i.kategori_id
     WHERE i.kullanici_id = ?
     ORDER BY i.olusturma_tarihi DESC LIMIT 5",
    [$bilgi['id']]
);

$mahalledekiler = satirlar_getir(
    "SELECT i.takip_no, i.baslik, i.durum, i.olusturma_tarihi, k.ad AS kategori, k.renk
     FROM ihbarlar i
     JOIN kategoriler k ON k.id = i.kategori_id
     WHERE i.mahalle_id = ? AND i.kullanici_id <> ? AND i.durum <> 'İptal'
     ORDER BY i.olusturma_tarihi DESC LIMIT 4",
    [$bilgi['mahalle_id'], $bilgi['id']]
);
?>

<div class="izgara izgara-4 bosluk-alt">
    <div class="kart istatistik istatistik-kart" style="--renk:#0f5ea8">
        <div class="istatistik-ikon"><?= ikon('liste', 22) ?></div>
        <div><strong><?= (int) $sayilar['toplam'] ?></strong><span>İhbarım</span></div>
    </div>
    <div class="kart istatistik istatistik-kart" style="--renk:#d97706">
        <div class="istatistik-ikon"><?= ikon('saat', 22) ?></div>
        <div><strong><?= (int) $sayilar['bekleyen'] + (int) $sayilar['islemde'] ?></strong><span>Süren</span></div>
    </div>
    <div class="kart istatistik istatistik-kart" style="--renk:#16a34a">
        <div class="istatistik-ikon"><?= ikon('onay', 22) ?></div>
        <div><strong><?= (int) $sayilar['cozulen'] ?></strong><span>Çözülen</span></div>
    </div>
    <div class="kart istatistik istatistik-kart" style="--renk:#7c3aed">
        <div class="istatistik-ikon"><?= ikon('yildiz', 22) ?></div>
        <div><strong><?= $bilgi['kent_puan'] ?></strong><span>KentPuan</span></div>
    </div>
</div>

<div class="izgara duzen-sag">
    <div class="kart">
        <div class="kart-baslik">
            <h3>Son ihbarlarım</h3>
            <a href="ihbarlarim.php">Tümünü gör</a>
        </div>
        <?php if (count($son_ihbarlarim) == 0): ?>
            <div class="bos-durum">
                <p>Henüz ihbar oluşturmadınız.</p>
                <a href="ihbar_olustur.php" class="buton">İlk ihbarımı oluştur</a>
            </div>
        <?php else: ?>
            <div class="tablo-kutu">
                <table class="tablo">
                    <tr><th>İhbar</th><th>Durum</th><th>Tarih</th></tr>
                    <?php foreach ($son_ihbarlarim as $i): ?>
                        <tr>
                            <td>
                                <a href="ihbar_detay.php?id=<?= $i['id'] ?>"><?= e($i['baslik']) ?></a>
                                <small><?= e($i['takip_no']) ?> · <?= e($i['kategori']) ?></small>
                            </td>
                            <td><?= durum_etiketi($i['durum']) ?></td>
                            <td><?= ne_zaman($i['olusturma_tarihi']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <div class="kart">
        <h3>Mahallemde neler oluyor?</h3>
        <?php if (count($mahalledekiler) == 0): ?>
            <p style="color:var(--yazi-soluk)">Mahallenizde başka açık ihbar yok.</p>
        <?php endif; ?>
        <?php foreach ($mahalledekiler as $m): ?>
            <div class="yorum">
                <span class="avatar" style="background:<?= e($m['renk']) ?>;color:#fff"><?= ikon('uyari', 16) ?></span>
                <div>
                    <strong><?= e($m['baslik']) ?></strong>
                    <div class="yorum-ust"><?= e($m['kategori']) ?> · <?= ne_zaman($m['olusturma_tarihi']) ?></div>
                    <div style="margin-top:4px"><?= durum_etiketi($m['durum']) ?></div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
