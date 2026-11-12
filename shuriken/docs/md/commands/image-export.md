# image:export

Export konten dari database ke berbagai platform.

```bash
php shuriken image:export [options]
```

---

## Semua Option

| Option | Default | Keterangan |
|---|---|---|
| `--platform` | `wordpress` | Target platform export |
| `--category` | Uncategorized | Kategori untuk semua post |
| `--start` | 1 tahun lalu | Tanggal publikasi awal (format Y-m-d) |
| `--end` | Hari ini | Tanggal publikasi akhir (format Y-m-d) |
| `--chunk` | 50000 | Maksimal post per file |
| `--theme` | `default` | Tema HTML (hanya untuk `--platform=html`) |
| `--site_name` | Keyword pertama | Nama site (hanya untuk `--platform=html`) |
| `--description` | Judul post pertama | Deskripsi site (hanya untuk `--platform=html`) |
| `--clear_post` | false | Hapus folder `exports/` sebelum export |
| `--base_url` | — | Base URL untuk sitemap (contoh: `https://mysite.com`) |
| `--site` | — | Alias site — auto-deteksi `base_url` dari config site |
| `--images_txt` | false | Override thumbnail dengan gambar dari `images.txt` (hanya tema) |
| `--images_per_post` | 5 | Jumlah gambar dari `images.txt` per post |

---

## Platform yang Tersedia

```
wordpress   — XML untuk import ke WordPress
blogger     — XML untuk import ke Blogger
hugo        — ZIP berisi file Markdown untuk Hugo
csv         — CSV spreadsheet
native      — Database SQLite
html        — Website statis dalam ZIP
```

---

## Contoh Penggunaan

### Export Dasar

```bash
# WordPress
php shuriken image:export --platform=wordpress

# Blogger
php shuriken image:export --platform=blogger

# Hugo
php shuriken image:export --platform=hugo

# CSV
php shuriken image:export --platform=csv

# SQLite
php shuriken image:export --platform=native

# HTML dengan tema default
php shuriken image:export --platform=html
```

### Export HTML dengan Tema

```bash
# Tema masonry gelap
php shuriken image:export --platform=html --theme=five-dark

# Tema magazine
php shuriken image:export --platform=html --theme=six

# Tema overlay hitam
php shuriken image:export --platform=html --theme=eight

# Tema acak (random setiap kali)
php shuriken image:export --platform=html --theme=random

# Dengan nama site dan deskripsi
php shuriken image:export --platform=html \
    --theme=seven-dark \
    --site_name="Blog Resep Indonesia" \
    --description="Kumpulan resep masakan nusantara"
```

### Atur Rentang Tanggal

```bash
# Post tersebar di tahun 2024
php shuriken image:export --platform=wordpress \
    --start=2024-01-01 \
    --end=2024-12-31

# Post tersebar di 6 bulan terakhir
php shuriken image:export --platform=wordpress \
    --start=2024-07-01 \
    --end=2025-01-01
```

### Atur Kategori

```bash
php shuriken image:export --platform=wordpress --category="Resep Masakan"
php shuriken image:export --platform=blogger --category="Technology"
```

### Export dengan Chunk

```bash
# Maksimal 1000 post per file (akan buat wordpress-1.xml, wordpress-2.xml, dst)
php shuriken image:export --platform=wordpress --chunk=1000

# Maksimal 500 post per ZIP HTML
php shuriken image:export --platform=html --chunk=500 --theme=five-dark
```

### Bersihkan Export Lama

```bash
# Hapus semua file di exports/ lalu export ulang
php shuriken image:export --platform=wordpress --clear_post
```

---

## Export ke Semua Platform Sekaligus

```bash
for platform in wordpress blogger hugo csv native; do
    php shuriken image:export --platform=$platform
done

# HTML dengan semua variant tema
for theme in five-dark six seven-dark eight; do
    php shuriken image:export --platform=html --theme=$theme
done
```

---

## Output File

Semua file tersimpan di `exports/`:

```
exports/
├── wordpress-1.xml
├── wordpress-2.xml      (jika chunk terlampaui)
├── blogger-1.xml
├── hugo-1.zip
├── csv-1.csv
├── abc12.sqlite         (nama random)
├── html-1.zip
└── html-2.zip
```

Lihat [Platform Export](../export-platforms.md) untuk detail format masing-masing.
Lihat [HTML Themes](../html-themes.md) untuk daftar 33 tema yang tersedia.
