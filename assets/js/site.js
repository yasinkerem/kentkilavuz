// Silme / iptal gibi işlemlerden önce onay sor
document.querySelectorAll('form[data-onay]').forEach(function (form) {
    form.addEventListener('submit', function (olay) {
        if (!confirm(form.dataset.onay)) {
            olay.preventDefault();
        }
    });
});

// Fotoğraf seçilince önizleme göster
var fotoAlani = document.getElementById('foto');
if (fotoAlani) {
    fotoAlani.addEventListener('change', function () {
        var onizleme = document.getElementById('foto-onizleme');
        var dosya = fotoAlani.files[0];
        if (!dosya || !onizleme) return;

        if (dosya.size > 2 * 1024 * 1024) {
            alert('Fotoğraf en fazla 2 MB olabilir.');
            fotoAlani.value = '';
            return;
        }
        var okuyucu = new FileReader();
        okuyucu.onload = function (e) {
            onizleme.src = e.target.result;
            onizleme.hidden = false;
        };
        okuyucu.readAsDataURL(dosya);
    });
}

// Karakter sayacı (data-sayac="en_az" verilen textarea'lar için)
document.querySelectorAll('textarea[data-sayac]').forEach(function (alan) {
    var bilgi = document.createElement('small');
    alan.after(bilgi);
    function guncelle() {
        var enAz = parseInt(alan.dataset.sayac, 10);
        bilgi.textContent = alan.value.length + ' karakter (en az ' + enAz + ')';
        bilgi.style.color = alan.value.length < enAz ? '#b91c1c' : '#15803d';
    }
    alan.addEventListener('input', guncelle);
    guncelle();
});
