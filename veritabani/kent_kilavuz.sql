-- KentKılavuz veritabanı
-- phpMyAdmin > İçe Aktar ile de yüklenebilir. Site ilk açıldığında bu dosya otomatik çalıştırılır.

CREATE DATABASE IF NOT EXISTS kent_kilavuz CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci;
USE kent_kilavuz;

CREATE TABLE mahalleler (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ilce VARCHAR(50) NOT NULL,
    ad VARCHAR(80) NOT NULL,
    enlem DECIMAL(9,6) NOT NULL,
    boylam DECIMAL(9,6) NOT NULL,
    nufus INT DEFAULT 0,
    UNIQUE KEY ilce_mahalle (ilce, ad)
) ENGINE=InnoDB;

CREATE TABLE kullanicilar (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ad_soyad VARCHAR(100) NOT NULL,
    eposta VARCHAR(120) NOT NULL UNIQUE,
    telefon VARCHAR(15),
    parola VARCHAR(255) NOT NULL,
    rol ENUM('admin', 'operator', 'saha', 'vatandas') NOT NULL DEFAULT 'vatandas',
    mahalle_id INT NULL,
    kent_puan INT NOT NULL DEFAULT 0,
    aktif TINYINT(1) NOT NULL DEFAULT 1,
    sifirlama_kodu VARCHAR(6) NULL,
    sifirlama_bitis DATETIME NULL,
    kayit_tarihi DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (mahalle_id) REFERENCES mahalleler(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE kategoriler (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ad VARCHAR(60) NOT NULL UNIQUE,
    sorumlu_birim VARCHAR(80) NOT NULL,
    aciklama VARCHAR(255),
    renk VARCHAR(7) NOT NULL DEFAULT '#0f5ea8',
    ikon VARCHAR(20) NOT NULL DEFAULT 'uyari',
    aktif TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB;

CREATE TABLE ihbarlar (
    id INT AUTO_INCREMENT PRIMARY KEY,
    takip_no VARCHAR(15) NOT NULL UNIQUE,
    kullanici_id INT NOT NULL,
    kategori_id INT NOT NULL,
    mahalle_id INT NOT NULL,
    baslik VARCHAR(120) NOT NULL,
    aciklama TEXT NOT NULL,
    adres VARCHAR(200),
    enlem DECIMAL(9,6) NOT NULL,
    boylam DECIMAL(9,6) NOT NULL,
    foto VARCHAR(255) NULL,
    oncelik ENUM('Düşük', 'Normal', 'Yüksek', 'Acil') NOT NULL DEFAULT 'Normal',
    durum ENUM('Bekliyor', 'Ekip Yolda', 'Çalışılıyor', 'Çözüldü', 'İptal') NOT NULL DEFAULT 'Bekliyor',
    olusturma_tarihi DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    guncelleme_tarihi DATETIME NULL,
    cozum_tarihi DATETIME NULL,
    FOREIGN KEY (kullanici_id) REFERENCES kullanicilar(id),
    FOREIGN KEY (kategori_id) REFERENCES kategoriler(id) ON DELETE RESTRICT,
    FOREIGN KEY (mahalle_id) REFERENCES mahalleler(id)
) ENGINE=InnoDB;

CREATE TABLE ekipler (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ekip_kodu VARCHAR(10) NOT NULL UNIQUE,
    ad VARCHAR(80) NOT NULL,
    plaka VARCHAR(15) NOT NULL UNIQUE,
    kategori_id INT NOT NULL,
    sef_id INT NULL,
    telefon VARCHAR(15),
    durum ENUM('Müsait', 'Görevde', 'Bakımda') NOT NULL DEFAULT 'Müsait',
    FOREIGN KEY (kategori_id) REFERENCES kategoriler(id),
    FOREIGN KEY (sef_id) REFERENCES kullanicilar(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE sevkler (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ihbar_id INT NOT NULL,
    ekip_id INT NOT NULL,
    atayan_id INT NOT NULL,
    sevk_notu VARCHAR(255),
    cozum_notu TEXT,
    kanit_foto VARCHAR(255),
    atanma_tarihi DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    tamamlanma_tarihi DATETIME NULL,
    tamamlandi TINYINT(1) NOT NULL DEFAULT 0,
    FOREIGN KEY (ihbar_id) REFERENCES ihbarlar(id) ON DELETE CASCADE,
    FOREIGN KEY (ekip_id) REFERENCES ekipler(id),
    FOREIGN KEY (atayan_id) REFERENCES kullanicilar(id)
) ENGINE=InnoDB;

CREATE TABLE destekler (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ihbar_id INT NOT NULL,
    kullanici_id INT NOT NULL,
    tarih DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY tek_destek (ihbar_id, kullanici_id),
    FOREIGN KEY (ihbar_id) REFERENCES ihbarlar(id) ON DELETE CASCADE,
    FOREIGN KEY (kullanici_id) REFERENCES kullanicilar(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE yorumlar (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ihbar_id INT NOT NULL,
    kullanici_id INT NOT NULL,
    metin TEXT NOT NULL,
    tarih DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ihbar_id) REFERENCES ihbarlar(id) ON DELETE CASCADE,
    FOREIGN KEY (kullanici_id) REFERENCES kullanicilar(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE calismalar (
    id INT AUTO_INCREMENT PRIMARY KEY,
    mahalle_id INT NOT NULL,
    baslik VARCHAR(120) NOT NULL,
    aciklama TEXT,
    sorumlu_birim VARCHAR(80) NOT NULL,
    baslangic DATE NOT NULL,
    bitis DATE NOT NULL,
    durum ENUM('Planlandı', 'Devam Ediyor', 'Tamamlandı') NOT NULL DEFAULT 'Planlandı',
    ekleyen_id INT NOT NULL,
    FOREIGN KEY (mahalle_id) REFERENCES mahalleler(id),
    FOREIGN KEY (ekleyen_id) REFERENCES kullanicilar(id)
) ENGINE=InnoDB;

CREATE TABLE acil_durumlar (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ihbar_id INT NULL,
    baslatan_id INT NOT NULL,
    baslik VARCHAR(120) NOT NULL,
    mesaj VARCHAR(255) NOT NULL,
    seviye ENUM('Bilgi', 'Uyarı', 'Kritik') NOT NULL DEFAULT 'Uyarı',
    tarih DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ihbar_id) REFERENCES ihbarlar(id) ON DELETE SET NULL,
    FOREIGN KEY (baslatan_id) REFERENCES kullanicilar(id)
) ENGINE=InnoDB;

CREATE TABLE iletisim_mesajlari (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ad_soyad VARCHAR(100) NOT NULL,
    eposta VARCHAR(120) NOT NULL,
    konu VARCHAR(60) NOT NULL,
    mesaj TEXT NOT NULL,
    okundu TINYINT(1) NOT NULL DEFAULT 0,
    tarih DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------------------------------------------------------------
-- Örnek veriler
-- Parolalar: admin123, operator123, saha123, vatandas123
-- ---------------------------------------------------------------

INSERT INTO mahalleler (id, ilce, ad, enlem, boylam, nufus) VALUES
(1, 'Şehzadeler', 'Peker', 38.614200, 27.428900, 8450),
(2, 'Şehzadeler', 'Alaybey', 38.619000, 27.436500, 6120),
(3, 'Şehzadeler', '1. Anafartalar', 38.612000, 27.425000, 5300),
(4, 'Şehzadeler', 'Tevfikiye', 38.610000, 27.433000, 7240),
(5, 'Şehzadeler', 'Yarhasanlar', 38.616500, 27.432000, 4980),
(6, 'Yunusemre', 'Muradiye', 38.654000, 27.358000, 21300),
(7, 'Yunusemre', 'Güzelyurt', 38.628000, 27.389000, 15600),
(8, 'Yunusemre', 'Laleli', 38.621500, 27.401000, 9870),
(9, 'Yunusemre', 'Uncubozköy', 38.609000, 27.397000, 18250),
(10, 'Yunusemre', 'Kuyualan', 38.623000, 27.383000, 11400),
(11, 'Turgutlu', 'Ergenekon', 38.499000, 27.708000, 12100),
(12, 'Turgutlu', 'Şehitler', 38.496000, 27.701000, 8900);

INSERT INTO kullanicilar (id, ad_soyad, eposta, telefon, parola, rol, mahalle_id, kent_puan, kayit_tarihi) VALUES
(1, 'Fadime Ersöz', 'admin@kentkilavuz.com', '05551110001', '$2y$10$P01H6Z/q/88XkcJYmaCpauQ9n2lGPHySuL5fx5vxI4CJEFgOT0.sO', 'admin', 1, 0, '2026-10-01 09:00:00'),
(2, 'Ahmet Yılmaz', 'operator@kentkilavuz.com', '05552220002', '$2y$10$XEnfR3yBaCG8AbVhMrbxse8johNlFfdRifQu8ZazN.Bd.7UnH9Tf.', 'operator', 7, 0, '2026-10-01 09:10:00'),
(3, 'Mehmet Demir', 'saha@kentkilavuz.com', '05553330003', '$2y$10$r/FO/zQif1EqWv8H1abeBu.CKHNtsMDltCbbVkdTwEdYEAJe2Oxia', 'saha', 5, 0, '2026-10-01 09:20:00'),
(4, 'Selin Kaya', 'saha2@kentkilavuz.com', '05553330004', '$2y$10$r/FO/zQif1EqWv8H1abeBu.CKHNtsMDltCbbVkdTwEdYEAJe2Oxia', 'saha', 8, 0, '2026-10-01 09:25:00'),
(5, 'Murat Şahin', 'murat@ornek.com', '05554440005', '$2y$10$l7qBO66FGBN979XjkSnHhu60IG0nyZo7TtsNnlFwf0cWT..tfyj7q', 'vatandas', 6, 145, '2026-10-05 18:30:00'),
(6, 'Ayşe Yıldız', 'ayse@ornek.com', '05554440006', '$2y$10$l7qBO66FGBN979XjkSnHhu60IG0nyZo7TtsNnlFwf0cWT..tfyj7q', 'vatandas', 1, 90, '2026-10-07 12:15:00'),
(7, 'Can Öztürk', 'can@ornek.com', '05554440007', '$2y$10$l7qBO66FGBN979XjkSnHhu60IG0nyZo7TtsNnlFwf0cWT..tfyj7q', 'vatandas', 7, 60, '2026-10-09 20:40:00');

INSERT INTO kategoriler (id, ad, sorumlu_birim, aciklama, renk, ikon) VALUES
(1, 'Su ve Kanalizasyon', 'MASKİ', 'Boru patlağı, kanal taşması, rögar kapağı sorunları', '#0284c7', 'damla'),
(2, 'Yol ve Kaldırım', 'Fen İşleri Müdürlüğü', 'Çukur, asfalt çökmesi, kırık kaldırım taşı', '#d97706', 'yol'),
(3, 'Park ve Yeşil Alan', 'Park ve Bahçeler Müdürlüğü', 'Kırık oyun grubu, kuruyan ağaç, bakımsız yeşil alan', '#16a34a', 'agac'),
(4, 'Sokak Aydınlatma', 'Elektrik Birimi', 'Yanmayan lamba, sarkan kablo', '#7c3aed', 'simsek'),
(5, 'Çöp ve Temizlik', 'Temizlik İşleri Müdürlüğü', 'Taşan konteyner, toplanmayan çöp', '#64748b', 'cop'),
(6, 'Zabıta', 'Zabıta Müdürlüğü', 'Kaldırım işgali, kaçak döküm, gürültü', '#dc2626', 'kalkan');

INSERT INTO ekipler (id, ekip_kodu, ad, plaka, kategori_id, sef_id, telefon, durum) VALUES
(1, 'SU-01', 'MASKİ Acil Müdahale', '45 MSK 101', 1, 3, '05321010101', 'Görevde'),
(2, 'FEN-01', 'Asfalt ve Yama Ekibi', '45 FEN 201', 2, 4, '05322020202', 'Müsait'),
(3, 'PRK-01', 'Budama ve Bakım Ekibi', '45 PRK 301', 3, 3, '05323030303', 'Müsait'),
(4, 'ELK-01', 'Aydınlatma Arıza Ekibi', '45 ELK 401', 4, 4, '05324040404', 'Görevde'),
(5, 'TMZ-01', 'Temizlik Ekibi', '45 TMZ 501', 5, NULL, '05325050505', 'Müsait'),
(6, 'ZBT-01', 'Zabıta Denetim Ekibi', '45 ZBT 601', 6, NULL, '05326060606', 'Müsait');

INSERT INTO ihbarlar (id, takip_no, kullanici_id, kategori_id, mahalle_id, baslik, aciklama, adres, enlem, boylam, oncelik, durum, olusturma_tarihi, cozum_tarihi) VALUES
(1, 'KK-2026-0001', 5, 1, 6, 'Ana su borusu patladı', 'Sabahtan beri yola temiz su akıyor, kaldırımın altı oyulmaya başladı.', 'Atatürk Cad. No: 42 önü', 38.654600, 27.358700, 'Acil', 'Ekip Yolda', '2026-10-29 08:40:00', NULL),
(2, 'KK-2026-0002', 5, 2, 6, 'Kavşakta derin çukur', 'Üniversite kavşağında araçların altını vuran derin bir çukur oluştu. Gece hiç görünmüyor.', 'Kampüs girişi kavşağı', 38.655200, 27.359900, 'Yüksek', 'Bekliyor', '2026-10-28 14:30:00', NULL),
(3, 'KK-2026-0003', 6, 4, 1, 'Sokak lambaları yanmıyor', '3. sokağın başındaki iki lamba bir haftadır yanmıyor, akşamları sokak tamamen karanlık.', '3. Sokak başı', 38.614600, 27.429400, 'Normal', 'Çalışılıyor', '2026-10-27 19:05:00', NULL),
(4, 'KK-2026-0004', 6, 3, 1, 'Parkta kırık salıncak', 'Peker parkındaki salıncağın zinciri kopmuş, çocuklar için tehlikeli.', 'Peker Parkı', 38.615000, 27.428000, 'Düşük', 'Çözüldü', '2026-10-18 11:00:00', '2026-10-20 15:30:00'),
(5, 'KK-2026-0005', 7, 6, 7, 'Kaldırım işgali', 'Market önüne konan tezgahlar yüzünden yayalar yola inmek zorunda kalıyor.', 'Güzelyurt Çarşısı', 38.628500, 27.389500, 'Normal', 'Bekliyor', '2026-10-30 10:20:00', NULL),
(6, 'KK-2026-0006', 7, 1, 7, 'Rögar kapağı yerinden çıkmış', 'Okulun önündeki rögar kapağı yerinden oynamış, araçlar geçerken ses çıkarıyor.', 'Güzelyurt İlkokulu önü', 38.627600, 27.388300, 'Yüksek', 'Çözüldü', '2026-10-22 08:45:00', '2026-10-22 13:10:00'),
(7, 'KK-2026-0007', 5, 5, 6, 'Çöp konteyneri taşıyor', 'Konteyner üç gündür boşaltılmadı, etrafa koku yayılıyor.', 'Muradiye 12. Sokak', 38.653100, 27.357400, 'Normal', 'Çözüldü', '2026-10-15 09:30:00', '2026-10-16 10:00:00'),
(8, 'KK-2026-0008', 6, 2, 2, 'Kaldırım taşları kırık', 'Kaldırımın yaklaşık 10 metrelik bölümünde taşlar kırılmış, yaşlılar için tehlikeli.', 'Alaybey Camii yanı', 38.619400, 27.436900, 'Düşük', 'Bekliyor', '2026-10-26 17:45:00', NULL),
(9, 'KK-2026-0009', 7, 3, 8, 'Devrilmek üzere olan ağaç', 'Dün akşamki rüzgardan sonra ağaç evlerin tarafına doğru yattı.', 'Laleli Parkı kuzey girişi', 38.621900, 27.401500, 'Yüksek', 'Bekliyor', '2026-10-31 07:50:00', NULL),
(10, 'KK-2026-0010', 5, 4, 9, 'Trafo yanında sarkan kablo', 'Trafonun yanındaki direkten kablo sarkıyor, yerden yaklaşık bir metre yukarıda.', 'Uncubozköy Sağlık Ocağı yanı', 38.609400, 27.397600, 'Acil', 'Bekliyor', '2026-10-31 09:15:00', NULL),
(11, 'KK-2026-0011', 6, 5, 3, 'Pazar sonrası temizlik yapılmadı', 'Pazar kurulan alan akşam temizlenmedi.', 'Anafartalar pazar yeri', 38.612300, 27.425400, 'Normal', 'İptal', '2026-10-12 18:00:00', NULL),
(12, 'KK-2026-0012', 7, 2, 11, 'Yağmur sonrası yol çöktü', 'Şiddetli yağmurdan sonra yolun kenarı yaklaşık yarım metre çöktü.', 'Ergenekon 5. Sokak', 38.499300, 27.708400, 'Yüksek', 'Çözüldü', '2026-10-10 16:20:00', '2026-10-12 11:45:00');

INSERT INTO sevkler (ihbar_id, ekip_id, atayan_id, sevk_notu, cozum_notu, atanma_tarihi, tamamlanma_tarihi, tamamlandi) VALUES
(1, 1, 2, 'Vana kapatılıp boru değişecek.', NULL, '2026-10-29 09:05:00', NULL, 0),
(3, 4, 2, 'İki armatür kontrol edilecek.', NULL, '2026-10-28 08:30:00', NULL, 0),
(4, 3, 2, 'Salıncak zinciri değişecek.', 'Kopan zincir ve iki bağlantı halkası yenisiyle değiştirildi.', '2026-10-19 09:00:00', '2026-10-20 15:30:00', 1),
(6, 1, 2, NULL, 'Rögar kapağı yerine oturtuldu, kenarları harçla sabitlendi.', '2026-10-22 09:10:00', '2026-10-22 13:10:00', 1),
(7, 5, 2, NULL, 'Konteyner boşaltıldı ve yıkandı.', '2026-10-15 13:00:00', '2026-10-16 10:00:00', 1),
(12, 2, 2, 'Bölge şeritle kapatılsın.', 'Çöken bölüm dolduruldu ve sıcak asfalt ile kaplandı.', '2026-10-10 17:00:00', '2026-10-12 11:45:00', 1);

INSERT INTO destekler (ihbar_id, kullanici_id, tarih) VALUES
(1, 6, '2026-10-29 10:00:00'), (1, 7, '2026-10-29 11:20:00'),
(2, 6, '2026-10-28 18:00:00'), (2, 7, '2026-10-29 08:10:00'),
(3, 5, '2026-10-28 09:00:00'),
(9, 5, '2026-10-31 08:30:00'), (9, 6, '2026-10-31 08:45:00'),
(10, 6, '2026-10-31 09:40:00'), (10, 7, '2026-10-31 09:55:00');

INSERT INTO yorumlar (ihbar_id, kullanici_id, metin, tarih) VALUES
(1, 6, 'Bizim sokakta da su basıncı düştü, aynı arızadan olabilir.', '2026-10-29 10:05:00'),
(1, 2, 'Ekip yönlendirildi, bölgede kısa süreli su kesintisi olacaktır.', '2026-10-29 09:06:00'),
(4, 6, 'Çok hızlı ilgilenildi, teşekkürler.', '2026-10-20 18:00:00');

INSERT INTO calismalar (mahalle_id, baslik, aciklama, sorumlu_birim, baslangic, bitis, durum, ekleyen_id) VALUES
(6, 'İçme suyu hattı yenileme', 'Atatürk Caddesi boyunca eski hat yenilenecek. Gündüz saatlerinde kesinti olabilir.', 'MASKİ', '2026-11-03', '2026-11-07', 'Planlandı', 2),
(1, 'Park aydınlatma bakımı', 'Peker Parkı aydınlatma direkleri bakımdan geçirilecek.', 'Elektrik Birimi', '2026-10-30', '2026-11-01', 'Devam Ediyor', 2),
(7, 'Kaldırım düzenleme', 'Güzelyurt Çarşısı kaldırımları yenileniyor.', 'Fen İşleri Müdürlüğü', '2026-10-20', '2026-10-27', 'Tamamlandı', 2),
(9, 'Ağaç budama', 'Sağlık ocağı çevresindeki ağaçlar budanacak.', 'Park ve Bahçeler Müdürlüğü', '2026-11-05', '2026-11-05', 'Planlandı', 2);

INSERT INTO acil_durumlar (ihbar_id, baslatan_id, baslik, mesaj, seviye, tarih) VALUES
(1, 2, 'Ana boru patlağı', 'Muradiye Atatürk Caddesi su verilemiyor, ekip yolda.', 'Kritik', '2026-10-29 09:00:00');

INSERT INTO iletisim_mesajlari (ad_soyad, eposta, konu, mesaj, okundu, tarih) VALUES
('Zeynep Arslan', 'zeynep@ornek.com', 'Öneri', 'İhbar açarken birden fazla fotoğraf eklenebilse güzel olur.', 0, '2026-10-25 21:10:00'),
('Hakan Çelik', 'hakan@ornek.com', 'Teknik Sorun', 'Kayıt olurken mahallemi listede bulamadım.', 1, '2026-10-27 13:45:00');
