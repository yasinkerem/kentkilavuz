<?php
require_once 'includes/baslangic.php';

$oturum = giris_zorunlu();

$bilgi = satir_getir(
    "SELECT k.*, m.ad AS mahalle, m.ilce
     FROM kullanicilar k
     LEFT JOIN mahalleler m ON m.id = k.mahalle_id
     WHERE k.id = ?",
    [$oturum['id']]
);

$sayfa_basligi = 'Panel';
$aktif_sayfa = 'panel';
require 'includes/header.php';
?>

<section class="sayfa-ust">
    <div class="kapsayici sayfa-ust-ic">
        <div class="panel-ust">
            <span class="avatar"><?= e(bas_harfler($bilgi['ad_soyad'])) ?></span>
            <div>
                <h1>Merhaba, <?= e($bilgi['ad_soyad']) ?></h1>
                <p><?= rol_adi($bilgi['rol']) ?> · <?= e($bilgi['mahalle'] ?? '-') ?>, <?= e($bilgi['ilce'] ?? '') ?> · <?= tarih_tr(date('Y-m-d'), false) ?></p>
            </div>
        </div>
        <?php if ($bilgi['rol'] == 'vatandas'): ?>
            <a href="ihbar_olustur.php" class="buton buton-vurgu buton-buyuk"><?= ikon('arti', 18) ?> Yeni İhbar</a>
        <?php elseif ($bilgi['rol'] == 'admin'): ?>
            <a href="kullanici_form.php" class="buton buton-beyaz"><?= ikon('arti', 18) ?> Kullanıcı Ekle</a>
        <?php endif; ?>
    </div>
</section>

<div class="kapsayici sayfa-govde">
    <?php mesajlari_goster(); ?>
    <?php require 'includes/panel_' . $bilgi['rol'] . '.php'; ?>
</div>

<?php require 'includes/footer.php'; ?>
