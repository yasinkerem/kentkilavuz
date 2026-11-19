<?php
require_once 'includes/baslangic.php';

if (giris_yapmis_mi()) {
    yonlendir('panel.php');
}

$hatalar = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ad_soyad = trim($_POST['ad_soyad'] ?? '');
    $eposta = trim(mb_strtolower($_POST['eposta'] ?? ''));
    $telefon = preg_replace('/\D/', '', $_POST['telefon'] ?? '');
    $mahalle_id = (int) ($_POST['mahalle_id'] ?? 0);
    $parola = $_POST['parola'] ?? '';
    $parola_tekrar = $_POST['parola_tekrar'] ?? '';

    if (mb_strlen($ad_soyad) < 5 || strpos($ad_soyad, ' ') === false) {
        $hatalar[] = 'Ad ve soyadınızı birlikte yazın.';
    }
    if (!filter_var($eposta, FILTER_VALIDATE_EMAIL)) {
        $hatalar[] = 'Geçerli bir e-posta adresi girin.';
    } elseif (deger_getir("SELECT COUNT(*) FROM kullanicilar WHERE eposta = ?", [$eposta]) > 0) {
        $hatalar[] = 'Bu e-posta adresiyle daha önce kayıt olunmuş.';
    }
    if ($telefon != '' && !preg_match('/^05\d{9}$/', $telefon)) {
        $hatalar[] = 'Telefon numarası 05XX XXX XX XX biçiminde olmalı.';
    }
    if (!deger_getir("SELECT COUNT(*) FROM mahalleler WHERE id = ?", [$mahalle_id])) {
        $hatalar[] = 'Mahallenizi seçin.';
    }
    if (strlen($parola) < 6 || !preg_match('/[0-9]/', $parola) || !preg_match('/[a-zA-Z]/', $parola)) {
        $hatalar[] = 'Parola en az 6 karakter olmalı ve harf ile rakam içermeli.';
    } elseif ($parola !== $parola_tekrar) {
        $hatalar[] = 'Parolalar birbiriyle aynı değil.';
    }
    if (!isset($_POST['kvkk'])) {
        $hatalar[] = 'Aydınlatma metnini onaylamanız gerekiyor.';
    }

    if (count($hatalar) == 0) {
        sorgu(
            "INSERT INTO kullanicilar (ad_soyad, eposta, telefon, parola, rol, mahalle_id) VALUES (?, ?, ?, ?, 'vatandas', ?)",
            [$ad_soyad, $eposta, $telefon ?: null, password_hash($parola, PASSWORD_DEFAULT), $mahalle_id]
        );
        mesaj_ekle('basari', 'Kaydınız oluşturuldu. Şimdi giriş yapabilirsiniz.');
        yonlendir('giris.php?eposta=' . urlencode($eposta));
    }
}

$sayfa_basligi = 'Kayıt Ol';
$aktif_sayfa = 'kayit';
require 'includes/header.php';
?>

<div class="yetki-sayfa">
    <div class="kapsayici">
        <div class="kart yetki-kutu genis">
            <h1>Vatandaş Kaydı</h1>
            <p>Üye olarak ihbar açabilir, ihbarlarınızın durumunu takip edebilirsiniz.</p>

            <?php hatalari_goster($hatalar); ?>

            <form method="post" novalidate>
                <div class="form-ikili">
                    <div class="form-satir">
                        <label for="ad_soyad">Ad Soyad</label>
                        <input type="text" id="ad_soyad" name="ad_soyad" value="<?= eski('ad_soyad') ?>" required>
                    </div>
                    <div class="form-satir">
                        <label for="telefon">Telefon <small>(isteğe bağlı)</small></label>
                        <input type="tel" id="telefon" name="telefon" value="<?= eski('telefon') ?>" placeholder="05XX XXX XX XX">
                    </div>
                </div>

                <div class="form-ikili">
                    <div class="form-satir">
                        <label for="eposta">E-posta</label>
                        <input type="email" id="eposta" name="eposta" value="<?= eski('eposta') ?>" required>
                    </div>
                    <div class="form-satir">
                        <label for="mahalle_id">Mahalle</label>
                        <select id="mahalle_id" name="mahalle_id" required>
                            <option value="">Seçiniz</option>
                            <?= mahalle_secenekleri($_POST['mahalle_id'] ?? '') ?>
                        </select>
                    </div>
                </div>

                <div class="form-ikili">
                    <div class="form-satir">
                        <label for="parola">Parola</label>
                        <input type="password" id="parola" name="parola" required>
                        <small>En az 6 karakter, harf ve rakam</small>
                    </div>
                    <div class="form-satir">
                        <label for="parola_tekrar">Parola (tekrar)</label>
                        <input type="password" id="parola_tekrar" name="parola_tekrar" required>
                    </div>
                </div>

                <div class="form-satir">
                    <label class="onay-kutusu">
                        <input type="checkbox" name="kvkk" <?= isset($_POST['kvkk']) ? 'checked' : '' ?>>
                        Kişisel verilerimin yalnızca ihbarlarımın işlenmesi amacıyla kullanılacağını okudum, onaylıyorum.
                    </label>
                </div>

                <button type="submit" class="buton buton-tam buton-buyuk">Kayıt Ol</button>
            </form>

            <p class="yetki-alt">Zaten üye misiniz? <a href="giris.php">Giriş yapın</a></p>
        </div>
    </div>
</div>

<?php require 'includes/footer.php'; ?>
