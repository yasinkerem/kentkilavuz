<?php
require_once 'includes/baslangic.php';

$konular = ['Genel Bilgi', 'Öneri', 'Şikayet', 'Teknik Sorun'];
$hatalar = [];

// Giriş yapmış kullanıcının bilgileri formda hazır gelsin
$oturum = aktif_kullanici();
if ($oturum && $_SERVER['REQUEST_METHOD'] != 'POST') {
    $_POST['ad_soyad'] = $oturum['ad_soyad'];
    $_POST['eposta'] = $oturum['eposta'];
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ad_soyad = trim($_POST['ad_soyad'] ?? '');
    $eposta = trim($_POST['eposta'] ?? '');
    $konu = $_POST['konu'] ?? '';
    $mesaj = trim($_POST['mesaj'] ?? '');

    if (mb_strlen($ad_soyad) < 3) {
        $hatalar[] = 'Adınızı yazın.';
    }
    if (!filter_var($eposta, FILTER_VALIDATE_EMAIL)) {
        $hatalar[] = 'Geçerli bir e-posta adresi girin.';
    }
    if (!in_array($konu, $konular)) {
        $hatalar[] = 'Bir konu seçin.';
    }
    if (mb_strlen($mesaj) < 15) {
        $hatalar[] = 'Mesajınız en az 15 karakter olmalı.';
    }

    if (count($hatalar) == 0) {
        sorgu(
            "INSERT INTO iletisim_mesajlari (ad_soyad, eposta, konu, mesaj) VALUES (?, ?, ?, ?)",
            [$ad_soyad, $eposta, $konu, $mesaj]
        );
        mesaj_ekle('basari', 'Mesajınız bize ulaştı. En kısa sürede dönüş yapacağız.');
        yonlendir('iletisim.php');
    }
}

$sayfa_basligi = 'İletişim';
$aktif_sayfa = 'iletisim';
require 'includes/header.php';
?>

<section class="sayfa-ust">
    <div class="kapsayici">
        <h1>İletişim</h1>
        <p>Görüş, öneri ve sorunlarınızı bize iletin.</p>
    </div>
</section>

<section class="bolum">
    <div class="kapsayici izgara duzen-sol">
        <div>
            <div class="iletisim-bilgi">
                <div class="istatistik-ikon"><?= ikon('telefon', 22) ?></div>
                <div><strong>Çözüm Merkezi</strong><p>153 (7/24)</p></div>
            </div>
            <div class="iletisim-bilgi">
                <div class="istatistik-ikon"><?= ikon('posta', 22) ?></div>
                <div><strong>E-posta</strong><p>bilgi@kentkilavuz.com</p></div>
            </div>
            <div class="iletisim-bilgi">
                <div class="istatistik-ikon"><?= ikon('konum', 22) ?></div>
                <div><strong>Adres</strong><p>Manisa Büyükşehir Belediyesi<br>Şehzadeler / Manisa</p></div>
            </div>
            <div class="uyari uyari-bilgi">
                Acil durumlar (yangın, kaza vb.) için lütfen <strong>112</strong>'yi arayın.
            </div>
        </div>

        <div class="kart">
            <h2>Bize yazın</h2>
            <?php mesajlari_goster(); ?>
            <?php hatalari_goster($hatalar); ?>

            <form method="post" novalidate>
                <div class="form-ikili">
                    <div class="form-satir">
                        <label for="ad_soyad">Ad Soyad</label>
                        <input type="text" id="ad_soyad" name="ad_soyad" value="<?= eski('ad_soyad') ?>">
                    </div>
                    <div class="form-satir">
                        <label for="eposta">E-posta</label>
                        <input type="email" id="eposta" name="eposta" value="<?= eski('eposta') ?>">
                    </div>
                </div>
                <div class="form-satir">
                    <label for="konu">Konu</label>
                    <select id="konu" name="konu">
                        <option value="">Seçiniz</option>
                        <?php foreach ($konular as $k): ?>
                            <option <?= ($_POST['konu'] ?? '') == $k ? 'selected' : '' ?>><?= $k ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-satir">
                    <label for="mesaj">Mesajınız</label>
                    <textarea id="mesaj" name="mesaj" rows="6"><?= eski('mesaj') ?></textarea>
                </div>
                <button type="submit" class="buton"><?= ikon('gonder', 18) ?> Gönder</button>
            </form>
        </div>
    </div>
</section>

<?php require 'includes/footer.php'; ?>
