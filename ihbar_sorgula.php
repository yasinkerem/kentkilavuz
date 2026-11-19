<?php
require_once 'includes/baslangic.php';

$takip_no = strtoupper(trim($_GET['takip_no'] ?? ''));
$ihbar = null;
$hata = '';

if ($takip_no != '') {
    if (!preg_match('/^KK-\d{4}-\d{4,}$/', $takip_no)) {
        $hata = 'Takip numarası KK-2026-0001 biçiminde olmalıdır.';
    } else {
        $ihbar = satir_getir(
            "SELECT i.*, k.ad AS kategori, k.sorumlu_birim, m.ad AS mahalle, m.ilce
             FROM ihbarlar i
             JOIN kategoriler k ON k.id = i.kategori_id
             JOIN mahalleler m ON m.id = i.mahalle_id
             WHERE i.takip_no = ?",
            [$takip_no]
        );
        if (!$ihbar) {
            $hata = $takip_no . ' numaralı bir ihbar bulunamadı.';
        }
    }
}

// Durum çizelgesi için hangi adımda olduğumuzu buluyoruz
$adimlar = ['Bekliyor', 'Ekip Yolda', 'Çalışılıyor', 'Çözüldü'];
$simdiki_adim = $ihbar ? array_search($ihbar['durum'], $adimlar) : false;

$sayfa_basligi = 'İhbar Sorgula';
$aktif_sayfa = 'sorgula';
require 'includes/header.php';
?>

<section class="sayfa-ust">
    <div class="kapsayici">
        <h1>İhbar Sorgula</h1>
        <p>Takip numaranızla ihbarınızın hangi aşamada olduğunu öğrenin.</p>
    </div>
</section>

<div class="kapsayici" style="max-width:820px;margin-top:28px">
    <form class="kart" method="get" style="margin-bottom:20px">
        <label for="takip_no" style="font-weight:600;display:block;margin-bottom:6px">Takip numarası</label>
        <div class="arama-satiri">
            <input type="text" id="takip_no" name="takip_no" value="<?= e($takip_no) ?>" placeholder="KK-2026-0001" required>
            <button type="submit" class="buton"><?= ikon('ara', 18) ?> Sorgula</button>
        </div>
    </form>

    <?php if ($hata): ?>
        <div class="uyari uyari-hata"><?= e($hata) ?></div>
    <?php endif; ?>

    <?php if ($ihbar): ?>
        <div class="kart">
            <div class="ihbar-ozet-ust">
                <div>
                    <small style="color:var(--yazi-soluk)"><?= e($ihbar['takip_no']) ?></small>
                    <h2 style="margin:2px 0 0"><?= e($ihbar['baslik']) ?></h2>
                </div>
                <?= durum_etiketi($ihbar['durum']) ?>
            </div>

            <?php if ($ihbar['durum'] == 'İptal'): ?>
                <div class="uyari uyari-bilgi" style="margin-top:20px">Bu ihbar iptal edilmiştir.</div>
            <?php else: ?>
                <div class="takip-adimlari">
                    <?php foreach ($adimlar as $sira => $adim):
                        $sinif = '';
                        if ($sira < $simdiki_adim || $ihbar['durum'] == 'Çözüldü') $sinif = 'tamam';
                        elseif ($sira == $simdiki_adim) $sinif = 'simdi';
                    ?>
                        <div class="takip-adim <?= $sinif ?>">
                            <span><?= $sinif == 'tamam' ? '✓' : $sira + 1 ?></span>
                            <?= $adim ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <hr style="border:0;border-top:1px solid var(--cizgi);margin:24px 0">

            <dl class="bilgi-listesi">
                <dt>Kategori</dt><dd><?= e($ihbar['kategori']) ?></dd>
                <dt>Sorumlu birim</dt><dd><?= e($ihbar['sorumlu_birim']) ?></dd>
                <dt>Konum</dt><dd><?= e($ihbar['mahalle']) ?> Mah., <?= e($ihbar['ilce']) ?><?= $ihbar['adres'] ? ' - ' . e($ihbar['adres']) : '' ?></dd>
                <dt>Öncelik</dt><dd><?= oncelik_etiketi($ihbar['oncelik']) ?></dd>
                <dt>Bildirim tarihi</dt><dd><?= tarih_tr($ihbar['olusturma_tarihi']) ?></dd>
                <?php if ($ihbar['cozum_tarihi']): ?>
                    <dt>Çözüm tarihi</dt><dd><?= tarih_tr($ihbar['cozum_tarihi']) ?></dd>
                <?php endif; ?>
                <dt>Açıklama</dt><dd><?= nl2br(e($ihbar['aciklama'])) ?></dd>
            </dl>
        </div>
    <?php elseif ($takip_no == ''): ?>
        <div class="kart bos-durum">
            <?= ikon('ara', 40) ?>
            <p>İhbar oluşturduğunuzda size verilen takip numarasını yukarıya yazın.</p>
        </div>
    <?php endif; ?>
</div>

<?php require 'includes/footer.php'; ?>
