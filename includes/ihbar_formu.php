<?php
// ihbar_olustur.php ve ihbar_duzenle.php sayfalarındaki ortak form
$oncelikler = ['Düşük' => 'dusuk', 'Normal' => 'normal', 'Yüksek' => 'yuksek', 'Acil' => 'acil'];
?>
<?php hatalari_goster($hatalar); ?>

<form method="post" enctype="multipart/form-data" novalidate>
    <div class="kart bosluk-alt">
        <h3>1. Sorunun türü</h3>
        <div class="secim-kartlari">
            <?php foreach ($kategoriler as $k): ?>
                <label class="secim-karti" style="--renk:<?= e($k['renk']) ?>">
                    <input type="radio" name="kategori_id" value="<?= $k['id'] ?>" <?= $form['kategori_id'] == $k['id'] ? 'checked' : '' ?>>
                    <span><i><?= ikon($k['ikon'], 18) ?></i> <?= e($k['ad']) ?></span>
                </label>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="kart bosluk-alt">
        <h3>2. Sorunu anlatın</h3>
        <div class="form-satir">
            <label for="baslik">Başlık</label>
            <input type="text" id="baslik" name="baslik" maxlength="120" value="<?= e($form['baslik']) ?>" placeholder="Örn: Sokak lambası yanmıyor">
        </div>
        <div class="form-satir">
            <label for="aciklama">Açıklama</label>
            <textarea id="aciklama" name="aciklama" rows="5" data-sayac="20" placeholder="Sorunu, ne zamandır devam ettiğini ve tehlike durumunu kısaca yazın."><?= e($form['aciklama']) ?></textarea>
        </div>
        <div class="form-satir">
            <label>Öncelik</label>
            <div class="oncelik-secimi">
                <?php foreach ($oncelikler as $o => $sinif): ?>
                    <label class="oncelik oncelik-<?= $sinif ?>">
                        <input type="radio" name="oncelik" value="<?= $o ?>" <?= $form['oncelik'] == $o ? 'checked' : '' ?>>
                        <span><?= $o ?></span>
                    </label>
                <?php endforeach; ?>
            </div>
            <small>Can ve mal güvenliğini tehdit eden durumlar için "Acil" seçin.</small>
        </div>
    </div>

    <div class="kart bosluk-alt">
        <h3>3. Konum ve fotoğraf</h3>
        <div class="form-ikili">
            <div class="form-satir">
                <label for="mahalle_id">Mahalle</label>
                <select id="mahalle_id" name="mahalle_id">
                    <option value="">Seçiniz</option>
                    <?= mahalle_secenekleri($form['mahalle_id']) ?>
                </select>
            </div>
            <div class="form-satir">
                <label for="adres">Sokak / adres tarifi</label>
                <input type="text" id="adres" name="adres" maxlength="200" value="<?= e($form['adres']) ?>" placeholder="Örn: Atatürk Cad. No: 42 önü">
            </div>
        </div>

        <div class="form-satir">
            <label for="foto">Fotoğraf <small>(isteğe bağlı, JPG/PNG/WEBP, en fazla 2 MB)</small></label>
            <div class="dosya-alani">
                <?= ikon('kamera', 28) ?>
                <div>Sorunu gösteren bir fotoğraf ekleyin</div>
                <input type="file" id="foto" name="foto" accept="image/jpeg,image/png,image/webp">
                <img id="foto-onizleme" alt="Önizleme" <?= !empty($form['foto']) ? 'src="' . e($form['foto']) . '"' : 'hidden' ?>>
            </div>
            <?php if (!empty($form['foto'])): ?>
                <label class="onay-kutusu" style="margin-top:8px">
                    <input type="checkbox" name="foto_kaldir"> Mevcut fotoğrafı kaldır
                </label>
            <?php endif; ?>
        </div>
    </div>

    <div class="islemler">
        <button type="submit" class="buton buton-buyuk"><?= ikon('gonder', 18) ?> <?= $duzenleme ? 'Değişiklikleri Kaydet' : 'İhbarı Gönder' ?></button>
        <a href="<?= $duzenleme ? 'ihbar_detay.php?id=' . $form['id'] : 'panel.php' ?>" class="buton buton-gri buton-buyuk">Vazgeç</a>
    </div>
</form>
