<?php
require_once 'includes/baslangic.php';

$sorular = [
    'İhbar oluşturmak için üye olmam gerekiyor mu?' =>
        'Evet. İhbarınızı takip edebilmeniz ve gerektiğinde size ulaşılabilmesi için ücretsiz vatandaş kaydı yapmanız gerekir. İhbar sorgulama ise üye olmadan yapılabilir.',
    'Takip numarası nedir, nereden bulurum?' =>
        'Her ihbara KK-2026-0001 biçiminde benzersiz bir numara verilir. Bu numara ihbarı oluşturduğunuzda ekranda gösterilir ve panelinizdeki ihbar listesinde de yer alır.',
    'İhbarım ne kadar sürede çözülür?' =>
        'Süre sorunun türüne ve önceliğine göre değişir. Acil ihbarlar (sarkan kablo, boru patlağı gibi) önceliklidir ve aynı gün içinde ekip yönlendirilir.',
    'Gönderdiğim ihbarı değiştirebilir miyim?' =>
        'İhbarınız "Bekliyor" durumundayken açıklamasını ve diğer bilgilerini düzenleyebilir ya da iptal edebilirsiniz. Ekip yönlendirildikten sonra değişiklik yapılamaz.',
    'Aynı sorunu başkası da bildirmişse ne olur?' =>
        'Aynı yerdeki aynı sorun için yeni ihbar açmak yerine mevcut ihbara destek verebilirsiniz. Destek sayısı arttıkça ihbarın önceliği yükselir.',
    'KentPuan nedir?' =>
        'İhbar açan ve diğer ihbarlara destek veren vatandaşlar KentPuan kazanır. Puan, şehrine katkı sağlayan vatandaşları görünür kılmak içindir.',
    'Kişisel bilgilerim başkaları tarafından görülür mü?' =>
        'Hayır. Adınız ve iletişim bilgileriniz yalnızca yetkili belediye personeli tarafından görülebilir. Herkese açık listelerde isimler gizlenir.',
];

$sayfa_basligi = 'Yardım';
$aktif_sayfa = 'yardim';
require 'includes/header.php';
?>

<section class="sayfa-ust">
    <div class="kapsayici">
        <h1>Yardım Merkezi</h1>
        <p>Sık sorulan sorular ve sistemin nasıl kullanılacağı hakkında bilgiler.</p>
    </div>
</section>

<section class="bolum">
    <div class="kapsayici izgara duzen-sag">
        <div>
            <h2>Sık Sorulan Sorular</h2>
            <div class="sss">
                <?php foreach ($sorular as $soru => $cevap): ?>
                    <details>
                        <summary><?= e($soru) ?></summary>
                        <p><?= e($cevap) ?></p>
                    </details>
                <?php endforeach; ?>
            </div>

            <h2 style="margin-top:40px">İhbar durumlarının anlamı</h2>
            <div class="tablo-kutu">
                <table class="tablo">
                    <tr><th>Durum</th><th>Açıklama</th></tr>
                    <tr><td><?= durum_etiketi('Bekliyor') ?></td><td>İhbar alındı, operatör incelemesini bekliyor.</td></tr>
                    <tr><td><?= durum_etiketi('Ekip Yolda') ?></td><td>Uygun saha ekibi görevlendirildi ve olay yerine gidiyor.</td></tr>
                    <tr><td><?= durum_etiketi('Çalışılıyor') ?></td><td>Ekip olay yerinde, onarım devam ediyor.</td></tr>
                    <tr><td><?= durum_etiketi('Çözüldü') ?></td><td>Sorun giderildi, ekip kanıt fotoğrafıyla işi kapattı.</td></tr>
                    <tr><td><?= durum_etiketi('İptal') ?></td><td>İhbar, sahibi tarafından ya da mükerrer olduğu için iptal edildi.</td></tr>
                </table>
            </div>
        </div>

        <aside>
            <div class="kart" style="margin-bottom:20px">
                <h3>Kullanıcı türleri</h3>
                <dl class="bilgi-listesi" style="grid-template-columns:1fr">
                    <dt><strong style="color:var(--yazi)">Vatandaş</strong></dt>
                    <dd style="font-weight:400;color:var(--yazi-soluk);margin-bottom:8px">İhbar açar, takip eder, destek verir.</dd>
                    <dt><strong style="color:var(--yazi)">Operatör</strong></dt>
                    <dd style="font-weight:400;color:var(--yazi-soluk);margin-bottom:8px">İhbarları inceler, ekip yönlendirir.</dd>
                    <dt><strong style="color:var(--yazi)">Saha Şefi</strong></dt>
                    <dd style="font-weight:400;color:var(--yazi-soluk);margin-bottom:8px">Ekibine atanan işleri yürütür ve kapatır.</dd>
                    <dt><strong style="color:var(--yazi)">Yönetici</strong></dt>
                    <dd style="font-weight:400;color:var(--yazi-soluk)">Kullanıcıları, kategorileri ve raporları yönetir.</dd>
                </dl>
            </div>
            <div class="kart">
                <h3>Sorunuz cevaplanmadı mı?</h3>
                <p style="color:var(--yazi-soluk)">Bize iletişim formundan yazabilirsiniz.</p>
                <a href="iletisim.php" class="buton buton-tam">İletişime Geç</a>
            </div>
        </aside>
    </div>
</section>

<?php require 'includes/footer.php'; ?>
