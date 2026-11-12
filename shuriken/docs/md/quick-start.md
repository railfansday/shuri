# Quick Start

Panduan cepat dari nol sampai export konten.

## Workflow Otomatis (Paling Mudah)

```bash
php shuriken omakase
```

`omakase` menjalankan pipeline penuh secara otomatis: scrape dari Google Trends → generate konten → export ke semua 6 platform. Tidak butuh konfigurasi apapun.

---

## Workflow Manual

### Langkah 1: Scrape Konten

Token AI **tidak diperlukan** — scrape dan export berjalan tanpa AI menggunakan template bawaan.

**Dari keyword manual:**
```bash
php shuriken image:scrape --keywords="resep ayam,resep ikan"
```

**Dari file `keyword.txt`:**
```bash
php shuriken image:scrape --keywords_txt
```

**Dari Google Trends (otomatis trending):**
```bash
php shuriken image:scrape --trends=id
```

**Dengan AI content generation (opsional):**
```bash
php shuriken image:scrape --keywords="resep masak" --ai --provider=deepseek
```

**Performa tinggi (lebih cepat):**
```bash
php shuriken image:scrape --keywords="resep masak" --concurrency=20 --ai --provider=groq
```

> Ingin pakai AI? Setup token dulu:
> ```bash
> php shuriken token add deepseek sk-...
> php shuriken token add groq gsk_...
> ```
> Atau gunakan Ollama (gratis, lokal) tanpa token. Lihat [Panduan Biaya AI](ai-cost-guide.md).

---

### Langkah 2: Export

**Export ke WordPress:**
```bash
php shuriken image:export --platform=wordpress
```

**Export ke HTML dengan tema:**
```bash
php shuriken image:export --platform=html --theme=five-dark --site_name="Blog Resep"
```

**Export ke semua platform sekaligus:**
```bash
for platform in wordpress blogger hugo csv native html; do
    php shuriken image:export --platform=$platform
done
```

Hasil export tersimpan di folder `exports/`.

---

## Contoh Lengkap: Blog Resep Masakan

```bash
# 1. Scrape 50 keyword trending masakan Indonesia dengan AI
php shuriken image:scrape \
    --trends=id \
    --max=50 \
    --ai \
    --provider=deepseek \
    --prompt=recipe \
    --concurrency=10

# 2. Export ke WordPress
php shuriken image:export \
    --platform=wordpress \
    --category="Resep Masakan" \
    --start=2024-01-01

# 3. Export ke HTML dengan tema masonry gelap
php shuriken image:export \
    --platform=html \
    --theme=five-dark \
    --site_name="Resep Nusantara" \
    --description="Kumpulan resep masakan Indonesia"

# 4. Bersihkan untuk batch berikutnya
php shuriken clear:data
```

---

## Contoh: Produksi Massal dengan Biaya Minimum

```bash
# Pakai Groq (gratis, cepat)
php shuriken image:scrape \
    --trends=id \
    --max=200 \
    --ai \
    --provider=groq \
    --concurrency=20 \
    --memory_db

php shuriken image:export --platform=wordpress --chunk=1000
```

Lihat [Panduan Biaya AI](ai-cost-guide.md) untuk strategi lengkap.

---

## Publish Langsung via API

Alternatif dari export file — publish langsung ke WordPress atau deploy ke Netlify/Cloudflare Pages.

### Setup Site (Sekali Saja)

```bash
# WordPress
php shuriken site add myblog wordpress https://myblog.com admin "app-password"

# Netlify
php shuriken site add mysite netlify {site_id} {api_token}

# Cloudflare Pages
php shuriken site add mysite cloudflare-pages {account_id} {project_name} {api_token}

# Cek koneksi
php shuriken site test myblog
```

### Publish Posts ke WordPress

```bash
php shuriken post:publish --site=myblog
```

Filter opsional:

```bash
php shuriken post:publish --site=myblog --limit=100 --start=2024-01-01 --end=2024-12-31
```

### Deploy HTML ke Netlify / Cloudflare Pages

```bash
# Deploy dari ZIP yang sudah ada
php shuriken site:deploy --site=mysite

# Export HTML dulu lalu langsung deploy
php shuriken site:deploy --site=mysite --theme=five-dark
```

Lihat referensi lengkap: [site](commands/site.md) · [post:publish](commands/post-publish.md) · [site:deploy](commands/site-deploy.md)

---

## Hasil Export

```
exports/
├── wordpress-1.xml    ← Import ke WordPress via Tools > Import
├── blogger-1.xml      ← Import ke Blogger via Setelan > Kelola Blog
├── hugo-1.zip         ← Extract ke folder content/ Hugo
├── csv-1.csv          ← Buka di Excel / Google Sheets
├── abc12.sqlite       ← Database SQLite lengkap
└── html-1.zip         ← Unzip → website statis siap upload
```
