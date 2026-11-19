<?php
$sayfa_basligi = $sayfa_basligi ?? 'Ana Sayfa';
$aktif_sayfa = $aktif_sayfa ?? '';
$kullanici = aktif_kullanici();

if ($kullanici) {
    $menu = rol_menusu($kullanici['rol']);
} else {
    $menu = [
        'index' => ['index.php', 'Ana Sayfa'],
        'sorgula' => ['ihbar_sorgula.php', 'İhbar Sorgula'],
        'hakkimizda' => ['hakkimizda.php', 'Hakkımızda'],
        'yardim' => ['yardim.php', 'Yardım'],
        'iletisim' => ['iletisim.php', 'İletişim'],
    ];
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($sayfa_basligi) ?> | <?= SITE_ADI ?></title>
    <link rel="icon" href="assets/img/logo.svg" type="image/svg+xml">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<header class="ust-bar">
    <div class="kapsayici ust-bar-ic">
        <a href="index.php" class="logo">
            <img src="assets/img/logo.svg" alt="" width="36" height="36">
            <span>
                <strong><?= SITE_ADI ?></strong>
                <small><?= SITE_SLOGAN ?></small>
            </span>
        </a>

        <input type="checkbox" id="menu-ac" class="menu-ac">
        <label for="menu-ac" class="menu-buton" aria-label="Menüyü aç"><span></span></label>

        <nav class="ana-menu">
            <?php foreach ($menu as $kod => $oge): ?>
                <a href="<?= $oge[0] ?>" class="<?= $aktif_sayfa == $kod ? 'aktif' : '' ?>"><?= $oge[1] ?></a>
            <?php endforeach; ?>

            <div class="menu-hesap">
                <?php if ($kullanici): ?>
                    <a href="panel.php" class="menu-kullanici">
                        <span class="avatar avatar-kucuk"><?= e(bas_harfler($kullanici['ad_soyad'])) ?></span>
                        <span>
                            <?= e($kullanici['ad_soyad']) ?>
                            <small class="menu-rol"><?= rol_adi($kullanici['rol']) ?></small>
                        </span>
                    </a>
                    <a href="cikis.php" class="buton buton-cizgili buton-kucuk"><?= ikon('cikis', 16) ?> Çıkış</a>
                <?php else: ?>
                    <a href="giris.php" class="<?= $aktif_sayfa == 'giris' ? 'aktif' : '' ?>">Giriş Yap</a>
                    <a href="kayit.php" class="buton buton-kucuk">Kayıt Ol</a>
                <?php endif; ?>
            </div>
        </nav>
    </div>
</header>

<main class="icerik">
