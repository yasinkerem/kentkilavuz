<?php
require_once 'includes/baslangic.php';

if (giris_yapmis_mi()) {
    yonlendir('panel.php');
}

$hata = '';
$eposta = $_GET['eposta'] ?? ($_COOKIE['son_eposta'] ?? '');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $eposta = trim(mb_strtolower($_POST['eposta'] ?? ''));
    $parola = $_POST['parola'] ?? '';

    if ($eposta == '' || $parola == '') {
        $hata = 'E-posta ve parola alanları boş bırakılamaz.';
    } else {
        $kullanici = satir_getir("SELECT * FROM kullanicilar WHERE eposta = ?", [$eposta]);

        if (!$kullanici || !password_verify($parola, $kullanici['parola'])) {
            $hata = 'E-posta veya parola hatalı.';
        } elseif ($kullanici['aktif'] == 0) {
            $hata = 'Hesabınız pasif durumda. Lütfen yönetici ile iletişime geçin.';
        } else {
            session_regenerate_id(true);
            $_SESSION['kullanici'] = [
                'id' => $kullanici['id'],
                'ad_soyad' => $kullanici['ad_soyad'],
                'eposta' => $kullanici['eposta'],
                'rol' => $kullanici['rol'],
                'mahalle_id' => $kullanici['mahalle_id'],
            ];

            if (isset($_POST['hatirla'])) {
                setcookie('son_eposta', $eposta, time() + 60 * 60 * 24 * 30);
            } else {
                setcookie('son_eposta', '', time() - 3600);
            }

            mesaj_ekle('basari', 'Hoş geldiniz, ' . $kullanici['ad_soyad'] . '!');
            yonlendir('panel.php');
        }
    }
}

$sayfa_basligi = 'Giriş Yap';
$aktif_sayfa = 'giris';
require 'includes/header.php';
?>

<div class="yetki-sayfa">
    <div class="kapsayici">
        <div class="kart yetki-kutu">
            <h1>Giriş Yap</h1>
            <p>Hesabınıza e-posta adresiniz ve parolanızla girin.</p>

            <?php mesajlari_goster(); ?>
            <?php if ($hata): ?>
                <div class="uyari uyari-hata"><?= e($hata) ?></div>
            <?php endif; ?>

            <form method="post">
                <div class="form-satir">
                    <label for="eposta">E-posta</label>
                    <input type="email" id="eposta" name="eposta" value="<?= e($eposta) ?>" required autofocus>
                </div>
                <div class="form-satir">
                    <label for="parola">Parola</label>
                    <input type="password" id="parola" name="parola" required>
                </div>
                <div class="form-satir">
                    <label class="onay-kutusu">
                        <input type="checkbox" name="hatirla" <?= isset($_COOKIE['son_eposta']) ? 'checked' : '' ?>>
                        E-posta adresimi hatırla
                    </label>
                </div>
                <button type="submit" class="buton buton-tam buton-buyuk">Giriş Yap</button>
            </form>

            <p class="yetki-alt">Hesabınız yok mu? <a href="kayit.php">Hemen kayıt olun</a></p>

            <details class="test-hesaplar">
                <summary>Test hesapları</summary>
                <table>
                    <tr><td>Yönetici</td><td>admin@kentkilavuz.com</td><td>admin123</td></tr>
                    <tr><td>Operatör</td><td>operator@kentkilavuz.com</td><td>operator123</td></tr>
                    <tr><td>Saha Şefi</td><td>saha@kentkilavuz.com</td><td>saha123</td></tr>
                    <tr><td>Vatandaş</td><td>murat@ornek.com</td><td>vatandas123</td></tr>
                </table>
            </details>
        </div>
    </div>
</div>

<?php require 'includes/footer.php'; ?>
