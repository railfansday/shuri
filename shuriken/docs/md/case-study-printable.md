# Case Study: Niche Printable Worksheets — Trafik Bing Tier 1

> **Studi kasus:** Bangun site aggregator worksheet & printable edukatif, targetkan trafik dari Bing (US/UK), monetisasi dengan AdSense atau Adsterra — tanpa AI, cepat di-generate massal.

---

## Overview

| | |
|---|---|
| **Niche** | Printable worksheets, word search, coloring pages (edu) |
| **Target trafik** | Tier 1 — US, UK, AU, CA |
| **Tujuan** | Passive income dari AdSense / Adsterra |
| **Tools** | [Shuriken](https://dojo.cc/shuriken) + Cloudflare Pages |
| **Estimasi waktu** | < 1 jam setup |

---

## Kenapa Niche Ini?

- **Bing-friendly** — konten visual & simpel, terbukti mendapat trafik dari Bing image search
- **Audience tier 1** — guru, orang tua, dan homeschooler mayoritas dari US/UK
- **Evergreen** — worksheet dibutuhkan sepanjang tahun; tema seasonal (Olympics, Christmas, Halloween) booming setiap periode tertentu
- **Volume keyword besar** — ribuan kombinasi: grade level × subject × tema
- **Tidak butuh AI** — gambar preview worksheet sudah cukup sebagai konten

---

## Contoh Keyword

Niche ini punya struktur keyword yang mudah diperluas:

```
word search for kids
math worksheet grade 2
coloring pages animals
olympic word search printable
halloween worksheet kindergarten
multiplication worksheet grade 3
sight words worksheet kindergarten
alphabet tracing worksheet
addition worksheet for first grade
science worksheet grade 4
```

Gunakan **[Keywords Generator](https://dojo.cc/keyword-tools/keywords-generator)** dengan seed:
- `worksheet`, `printable`, `word search`, `coloring pages`, `activity sheets`

---

## Step 1: Siapkan Keyword

Buat `keywords.txt` dengan kombinasi subject × grade × tema:

```
math worksheet grade 1
math worksheet grade 2
math worksheet grade 3
reading comprehension grade 2
word search animals printable
word search for kids
coloring pages dinosaurs
coloring pages flowers
alphabet worksheet kindergarten
addition worksheet grade 1
subtraction worksheet grade 2
science worksheet grade 3
geography worksheet middle school
```

---

## Step 2: Scrape & Generate

Tidak perlu `--ai` — scrape gambar preview worksheet dari Bing:

```bash
php shuriken image:scrape \
  --keywords_txt \
  --language=en \
  --country=us \
  --image_count=9 \
  --concurrency=5
```

> Tidak perlu `--template` khusus — template `image` default sudah cocok untuk niche ini karena kontennya memang berbasis gambar.

---

## Step 3: Monetisasi

### Opsi A — AdSense

Pasang kode AdSense di `templates/export/html/_ads/`:

```
header.blade.php     ← auto ads atau banner atas
in_article.blade.php ← di antara gambar
```

AdSense cocok karena CPC edu keyword di US/UK tergolong tinggi.

### Opsi B — Adsterra

Kalau belum punya AdSense atau ingin approval lebih mudah, gunakan **[Adsterra](https://beta.publishers.adsterra.com/referral/hdKsY5N1yr)**:

| Format | Slot | Keterangan |
|---|---|---|
| **Social Bar** | `header.blade.php` | Tidak ganggu konten |
| **Native Banner** | `in_article.blade.php` | Di antara grid gambar |
| **Popunder** | `header.blade.php` | RPM tinggi |

---

## Step 4: Export & Deploy

```bash
# Daftarkan site
php shuriken site add worksheets cloudflare-pages {account_id} free-printable-worksheets {api_token}

# Export
php shuriken image:export \
  --platform=html \
  --theme=one \
  --site_name="Free Printable Worksheets" \
  --site=worksheets

# Deploy
php shuriken site:deploy --site=worksheets
```

> Kalau subdomain sudah diambil orang lain, lihat [tips memilih nama subdomain](../commands/site-deploy.html#content-subdomain-sudah-diambil).

---

## Step 5: Indexing

```
https://free-printable-worksheets.pages.dev/sitemap.xml
```

Daftarkan ke **[nerd.dojo.cc](https://nerd.dojo.cc)**.

Untuk niche ini, prioritaskan **Bing Webmaster Tools** — terbukti lebih cepat index dan mengirim trafik untuk konten printable.

---

## Hasil yang Diharapkan

Mulai dengan **300–500 artikel**, tambah bertahap per batch.

### Batas Jumlah File per Platform

| Platform | Limit file |
|---|---|
| **Cloudflare Pages** | 20.000 file per deployment |
| **Vercel** | 10.000 file per deployment |
| **Netlify** | Tidak terbatas |
| **GitHub Pages** | Repo max 1GB |
| **Surge** | Tidak terbatas |

```bash
php shuriken image:export --platform=html --theme=one --site=worksheets --chunk=500
```

Dengan trafik Tier 1:
- RPM AdSense untuk keyword edu US bisa mencapai $3–8
- Trafik Bing lebih konsisten untuk niche printable dibanding Google
- Tidak perlu AI → proses generate lebih cepat dan murah

---

## Tidak Harus Bahasa Inggris

Niche worksheet ada di hampir semua bahasa:

| Bahasa | Flag | Keyword | Catatan |
|---|---|---|---|
| Inggris | `--language=en --country=us` | `worksheet`, `printable`, `word search` | Pasar utama, CPC tertinggi |
| Jerman | `--language=de --country=de` | `arbeitsblatt`, `ausdrucken`, `rätsel` | Tier 1, edu market kuat |
| Prancis | `--language=fr --country=fr` | `fiche de travail`, `coloriage`, `mots mêlés` | Tier 1 |
| Spanyol | `--language=es --country=es` | `ficha de trabajo`, `hoja de ejercicios`, `sopa de letras` | Tier 1 + bonus trafik Mexico/Amerika Latin |
| Portugis | `--language=pt --country=pt` | `folha de exercícios`, `atividade para imprimir`, `caça-palavras` | Tier 1 + bonus trafik Brazil (volume besar) |

---

## Tips

**Fokus ke long-tail grade + subject** — `"addition worksheet grade 1"` lebih mudah rank dibanding `"math worksheet"` yang terlalu broad.

**Manfaatkan seasonal** — buat batch khusus tiap musim:
```
christmas coloring pages printable
halloween word search for kids
valentines day worksheet kindergarten
back to school worksheet grade 1
```

**Perluas ke niche serupa:**
```
flashcards printable     — "sight words flashcards printable"
sticker chart printable  — "reward chart for kids"
reading log printable    — "reading log template kids"
```
