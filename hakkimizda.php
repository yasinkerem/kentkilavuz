<?php
require_once 'includes/baslangic.php';

$ekip = [
    ['ad' => 'Fadime Ersöz', 'gorev' => 'Proje lideri', 'alan' => 'Veritabanı tasarımı, ihbar modülü'],
    ['ad' => 'Yasin Kerem Uzun', 'gorev' => 'Geliştirici', 'alan' => 'Oturum yönetimi, yetkilendirme, yönetim paneli'],
    ['ad' => 'Yağmur Sena Yılmaz', 'gorev' => 'Geliştirici', 'alan' => 'Arayüz tasarımı, listeleme ve raporlama'],
];

$mahalle_sayisi = deger_getir("SELECT COUNT(*) FROM mahalleler");
$ekip_sayisi = deger_getir("SELECT COUNT(*) FROM ekipler");
$birim_sayisi = deger_getir("SELECT COUNT(DISTINCT sorumlu_birim) FROM kategoriler");

$sayfa_basligi = 'Hakkımızda';
$aktif_sayfa = 'hakkimizda';
require 'includes/header.php';
?>

<section class="sayfa-ust">
    <div class="kapsayici">
        <h1>Hakkımızda</h1>
        <p>KentKılavuz, vatandaş ile belediye arasındaki ihbar sürecini tek bir yerde toplar.</p>
    </div>
</section>

<section class="bolum">
    <div class="kapsayici izgara izgara-2" style="align-items:center;gap:48px">
        <div>
            <span class="ust-yazi">Projenin amacı</span>
            <h2>Sorun bildirmek zahmetli olmamalı</h2>
            <p>Bir vatandaş sokağında patlayan boruyu ya da yanmayan lambayı bildirmek istediğinde hangi birimi arayacağını bilmez,
                çoğu zaman da ihbarının ne olduğunu öğrenemez. KentKılavuz bu boşluğu doldurmak için geliştirildi.</p>
            <p>İhbarlar kategoriye göre doğru müdürlüğe yönlendirilir, operatörler tarafından saha ekiplerine atanır
                ve her adım vatandaşa açık şekilde gösterilir.</p>
        </div>
        <div class="izgara izgara-2">
            <div class="kart istatistik">
                <div class="istatistik-ikon"><?= ikon('konum', 24) ?></div>
                <div><strong><?= $mahalle_sayisi ?></strong><span>Mahalle</span></div>
            </div>
            <div class="kart istatistik">
                <div class="istatistik-ikon"><?= ikon('kamyon', 24) ?></div>
                <div><strong><?= $ekip_sayisi ?></strong><span>Saha ekibi</span></div>
            </div>
            <div class="kart istatistik">
                <div class="istatistik-ikon"><?= ikon('bina', 24) ?></div>
                <div><strong><?= $birim_sayisi ?></strong><span>Belediye birimi</span></div>
            </div>
            <div class="kart istatistik">
                <div class="istatistik-ikon"><?= ikon('saat', 24) ?></div>
                <div><strong>7/24</strong><span>İhbar kabulü</span></div>
            </div>
        </div>
    </div>
</section>

<section class="bolum" style="background:#fff">
    <div class="kapsayici">
        <div class="bolum-baslik">
            <span class="ust-yazi">Proje ekibi</span>
            <h2>Geliştirici ekip</h2>
            <p>Manisa Celal Bayar Üniversitesi, Manisa Teknik Bilimler MYO - BİL 2119 İnternet Programcılığı 2 dersi kapsamında hazırlanmıştır.</p>
        </div>
        <div class="izgara izgara-3">
            <?php foreach ($ekip as $kisi): ?>
                <div class="kart ekip-kart">
                    <span class="avatar"><?= e(bas_harfler($kisi['ad'])) ?></span>
                    <h3 style="margin-bottom:2px"><?= e($kisi['ad']) ?></h3>
                    <strong style="color:var(--ana);font-size:14px"><?= e($kisi['gorev']) ?></strong>
                    <p><?= e($kisi['alan']) ?></p>
                </div>
            <?php endforeach; ?>
        </div>
        <p style="text-align:center;color:var(--yazi-soluk);margin-top:28px">Ders sorumlusu: Öğr. Gör. Dr. Semih GENÇAY</p>
    </div>
</section>

<section class="bolum">
    <div class="kapsayici">
        <div class="bolum-baslik">
            <span class="ust-yazi">Kullanılan teknolojiler</span>
            <h2>Nasıl geliştirildi?</h2>
        </div>
        <div class="izgara izgara-4">
            <div class="kart"><h3>PHP</h3><p style="margin:0;color:var(--yazi-soluk)">Sunucu tarafı, form işleme, oturum yönetimi</p></div>
            <div class="kart"><h3>MySQL</h3><p style="margin:0;color:var(--yazi-soluk)">11 tablolu ilişkisel veritabanı</p></div>
            <div class="kart"><h3>HTML5</h3><p style="margin:0;color:var(--yazi-soluk)">Anlamsal sayfa yapısı ve formlar</p></div>
            <div class="kart"><h3>CSS3</h3><p style="margin:0;color:var(--yazi-soluk)">Grid/Flexbox düzen, mobil uyumlu tasarım</p></div>
        </div>
    </div>
</section>

<?php require 'includes/footer.php'; ?>
