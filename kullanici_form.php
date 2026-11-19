<?php
require_once 'includes/baslangic.php';

$oturum = yetki_kontrol(['admin']);

$roller = ['admin', 'operator', 'saha', 'vatandas'];
$id = (int) ($_GET['id'] ?? 0);
$duzenleme = $id > 0;

if ($duzenleme) {
    $form = satir_getir("SELECT * FROM kullanicilar WHERE id = ?", [$id]);
    if (!$form) {
        mesaj_ekle('hata', 'Kullanıcı bulunamadı.');
        yonlendir('kullanicilar.php');
    }
} else {
    $form = ['ad_soyad' => '', 'eposta' => '', 'telefon' => '', 'rol' => 'vatandas', 'mahalle_id' => '', 'aktif' => 1];
}

$hatalar = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $form['ad_soyad'] = trim($_POST['ad_soyad'] ?? '');
    $form['eposta'] = trim(mb_strtolower($_POST['eposta'] ?? ''));
    $form['telefon'] = preg_replace('/\D/', '', $_POST['telefon'] ?? '');
    $form['rol'] = $_POST['rol'] ?? '';
    $form['mahalle_id'] = $_POST['mahalle_id'] ?? '';
    $form['aktif'] = isset($_POST['aktif']) ? 1 : 0;
    $parola = $_POST['parola'] ?? '';

    if (mb_strlen($form['ad_soyad']) < 5) {
        $hatalar[] = 'Ad soyad en az 5 karakter olmalı.';
    }
    if (!filter_var($form['eposta'], FILTER_VALIDATE_EMAIL)) {
        $hatalar[] = 'Geçerli bir e-posta adresi girin.';
    } elseif (deger_getir("SELECT COUNT(*) FROM kullanicilar WHERE eposta = ? AND id <> ?", [$form['eposta'], $id]) > 0) {
        $hatalar[] = 'Bu e-posta başka bir kullanıcıya ait.';
    }
    if ($form['telefon'] != '' && !preg_match('/^05\d{9}$/', $form['telefon'])) {
        $hatalar[] = 'Telefon numarası 05XX XXX XX XX biçiminde olmalı.';
    }
    if (!in_array($form['rol'], $roller)) {
        $hatalar[] = 'Geçerli bir rol seçin.';
    }
    if ($form['rol'] == 'vatandas' && $form['mahalle_id'] == '') {
        $hatalar[] = 'Vatandaş hesapları için mahalle seçilmelidir.';
    }
    if (!$duzenleme && strlen($parola) < 6) {
        $hatalar[] = 'Yeni kullanıcı için en az 6 karakterlik parola belirleyin.';
    }
    if ($duzenleme && $parola != '' && strlen($parola) < 6) {
        $hatalar[] = 'Yeni parola en az 6 karakter olmalı.';
    }
    // Yönetici kendi hesabının rolünü düşürür ya da pasife alırsa panele giremez
    if ($duzenleme && $id == $oturum['id'] && ($form['rol'] != 'admin' || $form['aktif'] == 0)) {
        $hatalar[] = 'Kendi hesabınızın rolünü değiştiremez veya pasife alamazsınız.';
    }

    if (count($hatalar) == 0) {
        $mahalle = $form['mahalle_id'] != '' ? (int) $form['mahalle_id'] : null;
        $telefon = $form['telefon'] != '' ? $form['telefon'] : null;

        if ($duzenleme) {
            sorgu(
                "UPDATE kullanicilar SET ad_soyad = ?, eposta = ?, telefon = ?, rol = ?, mahalle_id = ?, aktif = ? WHERE id = ?",
                [$form['ad_soyad'], $form['eposta'], $telefon, $form['rol'], $mahalle, $form['aktif'], $id]
            );
            if ($parola != '') {
                sorgu("UPDATE kullanicilar SET parola = ? WHERE id = ?", [password_hash($parola, PASSWORD_DEFAULT), $id]);
            }
            mesaj_ekle('basari', $form['ad_soyad'] . ' adlı kullanıcının bilgileri güncellendi.');
        } else {
            sorgu(
                "INSERT INTO kullanicilar (ad_soyad, eposta, telefon, parola, rol, mahalle_id, aktif) VALUES (?, ?, ?, ?, ?, ?, ?)",
                [$form['ad_soyad'], $form['eposta'], $telefon, password_hash($parola, PASSWORD_DEFAULT), $form['rol'], $mahalle, $form['aktif']]
            );
            mesaj_ekle('basari', $form['ad_soyad'] . ' adlı kullanıcı eklendi.');
        }
        yonlendir('kullanicilar.php');
    }
}

$sayfa_basligi = $duzenleme ? 'Kullanıcı Düzenle' : 'Yeni Kullanıcı';
$aktif_sayfa = 'kullanicilar';
require 'includes/header.php';
?>

<section class="sayfa-ust">
    <div class="kapsayici">
        <h1><?= $sayfa_basligi ?></h1>
        <p><?= $duzenleme ? e($form['eposta']) : 'Personel veya vatandaş hesabı oluşturun.' ?></p>
    </div>
</section>

<div class="kapsayici sayfa-govde" style="max-width:760px">
    <?php hatalari_goster($hatalar); ?>

    <form class="kart" method="post" novalidate>
        <div class="form-ikili">
            <div class="form-satir">
                <label for="ad_soyad">Ad Soyad</label>
                <input type="text" id="ad_soyad" name="ad_soyad" value="<?= e($form['ad_soyad']) ?>">
            </div>
            <div class="form-satir">
                <label for="eposta">E-posta</label>
                <input type="email" id="eposta" name="eposta" value="<?= e($form['eposta']) ?>">
            </div>
        </div>
        <div class="form-ikili">
            <div class="form-satir">
                <label for="telefon">Telefon</label>
                <input type="tel" id="telefon" name="telefon" value="<?= e($form['telefon']) ?>" placeholder="05XX XXX XX XX">
            </div>
            <div class="form-satir">
                <label for="rol">Rol</label>
                <select id="rol" name="rol">
                    <?php foreach ($roller as $r): ?>
                        <option value="<?= $r ?>" <?= secili($r, $form['rol']) ?>><?= rol_adi($r) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="form-ikili">
            <div class="form-satir">
                <label for="mahalle_id">Mahalle</label>
                <select id="mahalle_id" name="mahalle_id">
                    <option value="">Seçilmedi</option>
                    <?= mahalle_secenekleri($form['mahalle_id']) ?>
                </select>
            </div>
            <div class="form-satir">
                <label for="parola"><?= $duzenleme ? 'Yeni parola' : 'Parola' ?></label>
                <input type="password" id="parola" name="parola" autocomplete="new-password">
                <?php if ($duzenleme): ?><small>Değiştirmek istemiyorsanız boş bırakın.</small><?php endif; ?>
            </div>
        </div>
        <div class="form-satir">
            <label class="onay-kutusu">
                <input type="checkbox" name="aktif" <?= $form['aktif'] ? 'checked' : '' ?>> Hesap aktif (pasif hesaplar giriş yapamaz)
            </label>
        </div>
        <div class="islemler">
            <button type="submit" class="buton"><?= $duzenleme ? 'Kaydet' : 'Kullanıcıyı Ekle' ?></button>
            <a href="kullanicilar.php" class="buton buton-gri">Vazgeç</a>
        </div>
    </form>
</div>

<?php require 'includes/footer.php'; ?>
