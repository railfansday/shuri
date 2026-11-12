# image:scrape

Scraping gambar dari Bing Images dan generate konten blog (opsional dengan AI).

```bash
php shuriken image:scrape [options]
```

---

## Semua Option

### Sumber Keyword

| Option | Default | Keterangan |
|---|---|---|
| `--keywords` | — | Keyword manual, pisah koma |
| `--keywords_txt` | false | Baca dari file `keyword.txt` |
| `--trends` | — | Keyword dari Google Trends (isi kode negara) |
| `--max` | 1000 | Maksimal keyword yang diproses |
| `--no_expand` | false | Nonaktifkan ekspansi keyword otomatis |
| `--suggest` | false | Tambah keyword via Bing Suggest |

### Lokasi & Bahasa

| Option | Default | Keterangan |
|---|---|---|
| `--language` | en | Kode bahasa (en, id, dll) |
| `--country` | us | Kode negara (us, id, dll) |

### Filter Gambar

| Option | Default | Keterangan |
|---|---|---|
| `--image_count` | 35 | Jumlah gambar per keyword (0-35) |
| `--preset` | URL Bing default | URL Bing untuk auto-set `append_query` dan `qft` |
| `--append_query` | — | Query tambahan di URL Bing |
| `--qft` | — | Custom filter Bing (`qft=filterui:...`) |

### Performa

| Option | Default | Keterangan |
|---|---|---|
| `--concurrency` | 5 | Proses paralel (semakin tinggi, semakin cepat) |
| `--requestDelay` | 0 | Jeda antar request (ms) |
| `--timeout` | 30 | Timeout per scrape (detik) |
| `--execute_javascript` | false | Jalankan JavaScript (lebih lambat, lebih akurat) |
| `--memory_db` | false | Gunakan database in-memory (lebih cepat) |

### AI Content Generation

| Option | Default | Keterangan |
|---|---|---|
| `--ai` | false | Aktifkan generate konten dengan AI |
| `--provider` | auto | Provider AI yang digunakan. Jika tidak diisi, otomatis memilih provider yang punya token tersedia. Kalau provider gagal (rate limit, dll), otomatis fallback ke provider berikutnya. |
| `--model` | — | Override model default |
| `--prompt` | image | Template prompt (image, recipe, list, review, faq, tutorial, comparison, news, roundup, guide) |

### Template Konten

| Option | Default | Keterangan |
|---|---|---|
| `--template` | `image` | Folder template untuk generate konten (`image`, `pfp`, atau custom) |

```bash
# Pakai template gallery pfp (tanpa AI)
php shuriken image:scrape --keywords_txt --template=pfp
```

### Gambar Kustom

| Option | Default | Keterangan |
|---|---|---|
| `--images_txt` | false | Gunakan `images.txt` sebagai sumber gambar, bukan Bing |
| `--images_per_post` | 5 | Jumlah gambar per post dari `images.txt` (dipilih acak) |

### Lainnya

| Option | Default | Keterangan |
|---|---|---|
| `--proxy` | — | Proxy server (`http://host:port`) |
| `--clear_data` | false | Hapus semua data sebelum scraping |

---

## Contoh Penggunaan

### Dasar

```bash
# Scrape 1 keyword
php shuriken image:scrape --keywords="resep ayam goreng"

# Scrape beberapa keyword sekaligus
php shuriken image:scrape --keywords="resep ayam,resep ikan,resep sapi"

# Dari file keyword.txt (satu keyword per baris)
php shuriken image:scrape --keywords_txt
```

### Dengan AI Content

```bash
# Pakai DeepSeek (murah)
php shuriken image:scrape --keywords="resep masak" --ai --provider=deepseek

# Pakai Groq (gratis, cepat)
php shuriken image:scrape --keywords="resep masak" --ai --provider=groq

# Override model
php shuriken image:scrape --ai --provider=openai --model=gpt-4o

# Template resep
php shuriken image:scrape --keywords="nasi goreng" --ai --provider=deepseek --prompt=recipe

# Template list/artikel
php shuriken image:scrape --keywords="tips diet" --ai --provider=groq --prompt=list

# Review produk
php shuriken image:scrape --keywords="laptop gaming" --ai --prompt=review

# FAQ
php shuriken image:scrape --keywords="tips diet" --ai --prompt=faq

# Tutorial/how-to
php shuriken image:scrape --keywords="cara membuat kue" --ai --prompt=tutorial

# Perbandingan
php shuriken image:scrape --keywords="iPhone vs Samsung" --ai --prompt=comparison

# Artikel berita (cocok dengan --trends)
php shuriken image:scrape --trends=id --ai --prompt=news

# Roundup affiliate
php shuriken image:scrape --keywords="best gaming chair" --ai --prompt=roundup

# Panduan komprehensif
php shuriken image:scrape --keywords="cara investasi saham" --ai --prompt=guide
```

### Dari Google Trends

```bash
# Trending di Indonesia
php shuriken image:scrape --trends=id

# Trending di USA
php shuriken image:scrape --trends=us

# Trending + AI (max 100 keyword)
php shuriken image:scrape --trends=id --max=100 --ai --provider=groq
```

### Performa Tinggi

```bash
# 20 proses paralel, in-memory DB
php shuriken image:scrape --trends=id --max=500 \
    --ai --provider=groq \
    --concurrency=20 \
    --memory_db

# Dengan proxy
php shuriken image:scrape --keywords="test" --proxy=http://127.0.0.1:8080
```

### Filter Gambar Khusus

```bash
# Hanya gambar resolusi tinggi (4K)
php shuriken image:scrape --keywords="wallpaper" \
    --qft="filterui:imagesize-large"

# Dari URL Bing yang sudah difilter (copy dari browser)
php shuriken image:scrape --keywords="wallpaper" \
    --preset="https://www.bing.com/images/search?q=wallpaper&qft=filterui:photo-photo"
```

### Gambar Sendiri (images.txt)

```bash
# Buat images.txt di root project (satu URL per baris):
# https://example.com/foto-umroh-1.jpg
# https://example.com/foto-umroh-2.jpg

# Scrape dengan gambar dari images.txt
php shuriken image:scrape --keywords_txt --images_txt

# Dengan AI content + 8 gambar per post
php shuriken image:scrape --keywords_txt --ai --provider=groq \
    --images_txt --images_per_post=8
```

> Caption gambar diambil otomatis dari nama file URL (e.g. `foto-umroh-1.jpg` → "Foto Umroh 1").

### Bersih Sebelum Scraping

```bash
# Hapus data lama, mulai dari nol
php shuriken image:scrape --trends=id --clear_data --ai --provider=deepseek
```

---

## File `keyword.txt`

Buat file `keyword.txt` di root project, satu keyword per baris:

```
resep ayam goreng
resep nasi goreng
resep mie goreng
cara membuat kue
```

> **Butuh inspirasi keyword?** Gunakan [Keywords Generator](https://dojo.cc/keyword-tools/keywords-generator) — tool gratis untuk generate ratusan keyword sekaligus. Hasilnya bisa langsung disalin ke `keyword.txt`.

Kemudian jalankan:

```bash
php shuriken image:scrape --keywords_txt
```

---

## Tentang `--image_count`

- Nilai 0–9: Tidak butuh `--execute_javascript`
- Nilai 10–35: **Butuh** `--execute_javascript` (Bing memuat gambar tambahan via JS)

```bash
# Cepat, tanpa JS
php shuriken image:scrape --keywords="foto" --image_count=9

# Lengkap, dengan JS (lebih lambat)
php shuriken image:scrape --keywords="foto" --image_count=35 --execute_javascript
```

---

## Tentang Keyword Expansion

Secara default, Shuriken juga menyimpan related keywords yang ditemukan saat scraping. Ini memperbanyak data secara organik.

Untuk menonaktifkan:
```bash
php shuriken image:scrape --keywords="test" --no_expand
```

---

## Kode Negara untuk `--trends`

Beberapa kode yang umum digunakan:

| Kode | Negara |
|---|---|
| `id` | Indonesia |
| `us` | Amerika Serikat |
| `sg` | Singapura |
| `my` | Malaysia |
| `gb` | Inggris |
| `au` | Australia |
| `in` | India |
| `ph` | Filipina |

Untuk daftar lengkap, cek dojo.cc/keyword-tools/trending-keywords.
