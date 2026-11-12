# Panduan Monetisasi (Adsterra)

Salah satu tujuan utama menggunakan Shuriken adalah untuk membangun *auto-blog* secara instan dan memonetisasinya. Tentu saja, Anda bisa memasang *ad network* apa pun (AdSense, PropellerAds, Mgid, dll). Namun, jaringan iklan (Ad Network) yang sangat kami sarankan untuk website baru atau blog "panas" hasil *generate* AI adalah **[Adsterra](https://beta.publishers.adsterra.com/referral/hdKsY5N1yr)**.

## Mengapa Adsterra?
- **Tanpa Syarat Minimal Traffic**: Begitu blog online, Anda langsung bisa mendaftarkannya dan iklan akan langsung tayang detik itu juga (No review period).
- **Format Iklan Beragam**: Sangat agresif menghasilkan konversi lewat popunder, social-bar (push), dan native banner.
- **Support CPA/CPM tinggi**: Sangat cocok jika Anda menargetkan keywords negara Tier 1 (Amerika, Eropa).

---

## 🛠️ Cara Pasang Script Iklan di Shuriken

Platform export HTML Shuriken sudah dirancang khusus agar Anda tidak perlu mengedit kode berulang-ulang di ratusan template. Anda cukup meletakkan *script* kode asli iklan ke dalam direktori khusus (`_ads/`).

### 1. Buka Direktori Ads 
Navigasi ke folder berikut di dalam instalasi Anda:
```text
templates/export/html/_ads/
```

### 2. Isi Script ke Slot yang Disediakan
Di dalam folder tersebut, biasanya akan ada *slot* di posisi template. Misalnya:
- `header.blade.php` (untuk Popunder atau Social Bar script yang biasanya diselipkan di `<head>`)
- `in_article.blade.php` (untuk Native Banner — otomatis disisipkan setelah paragraf ke-2 di semua artikel)
- `related.blade.php` (untuk banner di antara navigasi dan konten artikel)
- `footer.blade.php` (untuk script tracking atau popunder sebelum `</body>`)

**Contoh:** 
Buka file `templates/export/html/_ads/header.blade.php`, *paste* script Popunder dari Dashboard Adsterra Anda tepat ke dalam dokumen tersebut, pastikan klik *Save*.

### 3. Generate (atau Export Ulang)
AJAIB! Saat Anda melakukan `php shuriken image:export --platform=html`, kode script otomatis akan disuntikkan ke seluruh ribuan halaman artikel HTML yang ter-generate. Semua template Shuriken (Five, Galih, Shuriken, dll) semua mematuhi sistem *slot ads* ini.

---

## 💡 Best Practice & Tips Cuan

- **Jangan Terlalu Brutal**: Pengunjung (dan crawler Google) tidak suka spam layar penuh. Kombinasikan max 2 jenis iklan (misal: 1x Pop-under dan 1x Native banner).
- **Deploy Banyak "Pasukan"**: Jangan habiskan kekuatan pada 1 website. Shuriken dirancang untuk eksekusi massive. Pukul rata puluhan niche dengan target traffic organik long-tail.
- **Pantau Analytics & Matikan Niche Sepi**: Niche masak yang *spammy* mungkin traffic nya kecil. Cobalah ganti target *keywords* (trending) atau gunakan keyword yang berbeda (Otomotif, DIY, Tutorial).
