# KentKılavuz

Manisa için akıllı şehir ihbar sistemi. Vatandaş sokağındaki sorunu fotoğrafıyla bildirir, belediye ihbarı ilgili ekibe yönlendirir ve vatandaş ihbarın durumunu takip numarasıyla izler.

BİL 2119 İnternet Programcılığı 2 dersi projesi - PHP, MySQL, HTML, CSS.

## Kurulum

1. WAMP'ı başlatın ve proje klasörünü `C:\wamp64\www\kentkilavuz` olarak kopyalayın.
2. Tarayıcıda `http://localhost/kentkilavuz/` adresini açın.
3. Veritabanı ilk açılışta kendiliğinden kurulur. Bağlantı ayarları `config/ayarlar.php` içindedir.

## Test hesapları

| Rol | E-posta | Parola |
|---|---|---|
| Yönetici | admin@kentkilavuz.com | admin123 |
| Operatör | operator@kentkilavuz.com | operator123 |
| Saha Şefi | saha@kentkilavuz.com | saha123 |
| Vatandaş | murat@ornek.com | vatandas123 |

## Modül sorumluları

| Kişi | Sorumlu olduğu bölümler |
|---|---|
| Fadime ERSÖZ | Veritabanı, ihbar oluşturma ve ihbar detayı, ekip sevk ve saha işlemleri, raporlar |
| Yasin Kerem UZUN | Üyelik ve giriş, yetki ve paneller, kullanıcı ve kategori yönetimi, harita ve konum |
| Yağmur Sena YILMAZ | Arayüz tasarımı, ihbar listeleri, destek ve yorumlar, mahallem ve çalışma takvimi, acil durum |

Tasarım belgeleri (akış şeması, ER diyagramı, wireframe, mockup) `docs` klasöründedir. Mockup'ların Figma dosyası: https://www.figma.com/design/LW7q3hxCiCVWQzNzCbeboZ/kentk%C4%B1lavuz

## Ekip

Fadime ERSÖZ · Yasin Kerem UZUN · Yağmur Sena YILMAZ - Danışman: Öğr. Gör. Dr. Semih GENÇAY
