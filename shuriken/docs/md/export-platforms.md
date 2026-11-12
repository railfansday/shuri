# Platform Export

Shuriken mendukung 6 format export. Semua hasil tersimpan di folder `exports/`.

## Ringkasan

| Platform | Flag | Format | Langsung Import? |
|---|---|---|---|
| WordPress | `wordpress` | XML | Ya |
| Blogger | `blogger` | XML | Ya |
| Hugo | `hugo` | ZIP (Markdown) | Ya |
| CSV | `csv` | CSV | Ya (Excel/Sheets) |
| Native | `native` | SQLite | Migrasi DB |
| HTML | `html` | ZIP (Website) | Upload ke hosting |

---

## WordPress

```bash
php shuriken image:export --platform=wordpress
```

**Output:** `exports/wordpress-1.xml`

**Format:** WordPress Extended RSS (WXR) — format standar WordPress import.

**Cara import:**
1. Buka WordPress Admin → Tools → Import
2. Pilih "WordPress"
3. Upload file `wordpress-1.xml`
4. Assign authors dan import

**Yang diexport:**
- Judul post
- Konten HTML
- Kategori
- Tag
- Tanggal publikasi (tersebar di rentang `--start` sampai `--end`)
- Status: published

---

## Blogger

```bash
php shuriken image:export --platform=blogger
```

**Output:** `exports/blogger-1.xml`

**Format:** Atom XML — format standar Blogger import.

**Cara import:**
1. Buka Blogger → Settings → Manage blog
2. Import content → pilih file XML
3. Tunggu proses selesai

---

## Hugo

```bash
php shuriken image:export --platform=hugo
```

**Output:** `exports/hugo-1.zip`

**Format:** ZIP berisi file `.md` dengan Hugo frontmatter.

**Struktur ZIP:**
```
hugo-1.zip
├── judul-post-1.md
├── judul-post-2.md
└── ...
```

**Frontmatter contoh:**
```yaml
---
title: "Judul Post"
date: 2024-03-15
categories: ["Resep"]
tags: ["masakan", "indonesia"]
---

Konten dalam format Markdown...
```

**Cara pakai:**
1. Extract ZIP ke folder `content/posts/` di Hugo project
2. Jalankan `hugo serve`

---

## CSV

```bash
php shuriken image:export --platform=csv
```

**Output:** `exports/csv-1.csv`

**Format:** CSV dengan header kolom.

**Kolom yang tersedia:**
- `id`, `keyword_id`
- `title`, `slug`
- `content` (HTML)
- `markdown`
- `category`
- `tags` (JSON array)
- `published_at`
- `created_at`, `updated_at`

**Cara pakai:**
- Buka di Excel atau Google Sheets
- Import ke CMS custom
- Analisis data dengan Python/pandas

---

## Native (SQLite)

```bash
php shuriken image:export --platform=native
```

**Output:** `exports/abc12.sqlite` (nama random 5 karakter)

**Format:** File database SQLite lengkap dengan semua tabel dan relasi.

**Tabel yang ada:**
- `keywords` — keyword yang di-scrape
- `images` — gambar hasil scraping
- `posts` — konten blog yang dihasilkan

**Cara pakai:**
- Buka dengan DB Browser for SQLite
- Import ke MySQL/PostgreSQL dengan tool migrasi
- Dipakai sebagai database untuk aplikasi lain

---

## HTML (Static Website)

```bash
php shuriken image:export --platform=html --theme=five-dark
```

**Output:** `exports/html-1.zip`

**Format:** ZIP berisi website statis lengkap siap upload.

**Isi ZIP:**
```
html-1.zip
├── index.html          — Halaman utama (grid semua post)
├── judul-post-1.html   — Halaman per post
├── judul-post-2.html
├── sitemap.xml         — Sitemap untuk SEO
├── contact.html
├── copyright.html
├── dmca.html
└── privacy-policy.html
```

**Option tambahan:**

| Option | Default | Keterangan |
|---|---|---|
| `--theme` | `default` | Pilih tema (33 tersedia) |
| `--site_name` | Keyword pertama | Nama website di header |
| `--description` | Judul post pertama | Deskripsi/tagline |

**Cara deploy:**
1. Extract ZIP
2. Upload semua file ke hosting (cPanel, Netlify, Vercel, GitHub Pages, dll)
3. Tidak perlu PHP atau database — murni HTML/CSS

Lihat [HTML Themes](html-themes.md) untuk daftar lengkap 33 tema.

### Ads & Tracking

> 💰 Rekomendasi ad network: [**Adsterra**](https://beta.publishers.adsterra.com/referral/hdKsY5N1yr) — cocok untuk site baru, tanpa minimum traffic. Tersedia banner, native ads, popunder, dan social bar.

Shuriken menyediakan beberapa slot di folder `templates/export/html/_ads/`. Edit file yang sesuai, lalu export ulang — kode akan masuk ke semua halaman secara otomatis.

| File | Posisi di halaman | Cocok untuk |
|---|---|---|
| `header.blade.php` | Di dalam `<head>` | Adsense auto ads, GTM, meta ads pixel |
| `in_article.blade.php` | Di tengah artikel (setelah paragraf ke-2) | Native Banner, Adsterra in-article |
| `related.blade.php` | Di antara navigasi dan konten | Banner display, native ads |
| `bar.blade.php` | Sebelum `</body>` | Sticky bar, interstitial |
| `footer.blade.php` | Sebelum `</body>` | Stats (GA, Statcounter, Histats), popunder, social bar |

**Contoh: Google Analytics**

Edit `footer.blade.php`:
```html
<script async src="https://www.googletagmanager.com/gtag/js?id=G-XXXXXXX"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', 'G-XXXXXXX');
</script>
```

**Contoh: Adsterra Popunder / Social Bar**

Edit `footer.blade.php`, tempel script yang diberikan Adsterra:
```html
<script src="//your-adsterra-script-url" ...></script>
```

**Contoh: Adsterra Native Banner (in-article)**

Edit `in_article.blade.php`, tempel kode Native Banner dari dashboard Adsterra:
```html
<div id="adsterra-banner">
  <script>...</script>
</div>
```

Iklan akan otomatis muncul di tengah artikel setelah paragraf ke-2 di semua halaman.

---

## Option Umum

Semua platform mendukung option berikut:

| Option | Default | Keterangan |
|---|---|---|
| `--category` | Uncategorized | Kategori untuk semua post |
| `--start` | 1 tahun lalu | Tanggal mulai publikasi (Y-m-d) |
| `--end` | Hari ini | Tanggal akhir publikasi (Y-m-d) |
| `--chunk` | 50000 | Maksimal post per file |
| `--clear_post` | false | Hapus folder `exports/` sebelum export |

### Tentang `--chunk`

Jika post lebih dari `--chunk`, Shuriken akan membuat multiple file:
- `wordpress-1.xml`, `wordpress-2.xml`, dst.
- `html-1.zip`, `html-2.zip`, dst.

```bash
# Export dengan 1000 post per file
php shuriken image:export --platform=wordpress --chunk=1000
```

### Tentang Rentang Tanggal

Post akan tersebar secara acak di antara `--start` dan `--end`:

```bash
# Post tersebar di tahun 2024
php shuriken image:export --platform=wordpress \
    --start=2024-01-01 \
    --end=2024-12-31
```
