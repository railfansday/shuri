# Case Study: Auto-Blog Resep Masakan + AdSense via ARSAE

> **Studi kasus:** Bangun auto-blog resep masakan, deploy gratis, monetisasi dengan AdSense — tanpa perlu mendaftarkan site ke AdSense secara manual. Bisa untuk bahasa Indonesia, Turki, Jerman, atau bahasa lainnya.

---

## Overview

| | |
|---|---|
| **Niche** | Resep Masakan (Indonesia / internasional) |
| **Tujuan** | Passive income dari AdSense |
| **Tools** | [Shuriken](https://dojo.cc/shuriken) + Groq (gratis) + Cloudflare Pages + ARSAE |
| **Estimasi waktu** | 1–2 jam setup, lalu otomatis |

---

## Kenapa Resep Masakan?

- Volume keyword sangat besar — ribuan keyword long-tail aktif setiap hari
- Kompetisi rendah untuk keyword spesifik ("resep ayam goreng tepung crispy ala rumahan")
- Konten evergreen — tidak basi seperti konten berita
- Workflow 100% otomatis, tidak perlu keahlian khusus

---

## Step 1: Siapkan Keyword Resep

Gunakan **[Keywords Generator](https://dojo.cc/keyword-tools/keywords-generator)** dengan seed keyword resep:

1. Buka **[dojo.cc/keyword-tools/keywords-generator](https://dojo.cc/keyword-tools/keywords-generator)**
2. Masukkan seed: `resep`, `cara membuat`, `olahan ayam`, `masakan sederhana`
3. Export ke `keywords.txt`, letakkan di folder Shuriken

Contoh isi `keywords.txt`:

```
resep ayam goreng tepung crispy
cara membuat soto ayam sederhana
resep nasi goreng spesial
olahan daging sapi sederhana
resep kue brownies kukus
cara buat es kopi susu
resep tumis kangkung bawang putih
```

---

## Step 2: Generate Artikel

```bash
php shuriken image:scrape \
  --keywords_txt \
  --ai \
  --provider=groq \
  --prompt=recipe \
  --concurrency=3
```

**Penjelasan flag:**
- `--prompt=recipe` — format artikel resep dengan bahan & langkah memasak
- `--provider=groq` — gratis, cepat, cocok untuk konten massal

### Pilihan AI Gratis

Tidak harus Groq. Beberapa pilihan provider gratis atau berbiaya sangat rendah:

| Provider | Daftar di | Catatan |
|---|---|---|
| **Groq** | console.groq.com | Gratis, paling cepat |
| **Cerebras** | cloud.cerebras.ai | Gratis, sangat cepat |
| **OpenRouter** | openrouter.ai | Banyak model gratis tersedia |
| **Gemini** | aistudio.google.com | Free tier generous |
| **Ollama** | ollama.com | Gratis, lokal, tidak perlu internet |

```bash
# Tambah token sesuai pilihan
php shuriken token add groq gsk_...
php shuriken token add cerebras csk_...
php shuriken token add openrouter sk-or-...
php shuriken token add gemini AIza...
```

---

## Step 3: Export & Deploy

```bash
# Export HTML
php shuriken image:export \
  --platform=html \
  --theme=five \
  --site_name="Resep Nusantara" \
  --base_url=https://resep-nusantara.pages.dev
```

### Pilihan Platform Deploy Gratis

Tidak harus Cloudflare Pages. Semua platform berikut gratis:

| Platform | Perintah | URL hasil |
|---|---|---|
| **Cloudflare Pages** | `php shuriken site add resep cloudflare-pages {account_id} resep-nusantara {api_token}` | `resep-nusantara.pages.dev` |
| **Vercel** | `php shuriken site add resep vercel resep-nusantara {api_token}` | `resep-nusantara.vercel.app` |
| **Netlify** | `php shuriken site add resep netlify {site_id} {api_token}` | `{site_id}.netlify.app` |
| **GitHub Pages** | `php shuriken site add resep github-pages {owner} {repo} {token}` | `{owner}.github.io/{repo}` |
| **Surge** | `php shuriken site add resep surge resep-nusantara.surge.sh {token}` | `resep-nusantara.surge.sh` |

Setelah daftarkan site, gunakan flag `--site` agar sitemap pakai URL yang benar:

```bash
php shuriken site add resep cloudflare-pages {account_id} resep-nusantara {api_token}

php shuriken image:export --platform=html --theme=five \
  --site_name="Resep Nusantara" --site=resep

php shuriken site:deploy --site=resep
```

> Kalau subdomain sudah diambil orang lain, lihat [tips memilih nama subdomain](../commands/site-deploy.html#content-subdomain-sudah-diambil).

---

## Step 4: Monetisasi AdSense via ARSAE

Masalah umum AGC (Auto Generated Content): site baru susah di-approve AdSense karena dianggap low-quality atau belum punya cukup konten saat review.

**ARSAE** (*Anti Review Site & Ad Enabler*) mengatasi ini dengan cara: iklan AdSense "dikirim" dari 1 site yang sudah approved ke site lain yang belum — termasuk static HTML hasil Shuriken.

> **Syarat:** Minimal punya 1 site WordPress/PHP yang sudah approved AdSense dan tidak kena limit penayangan.

Setup:

1. Beli & install ARSAE di **[dojo.cc/arsae](https://dojo.cc/arsae)** — pasang di site yang sudah approved
2. Salin script ARSAE yang dihasilkan
3. Tempel ke slot iklan Shuriken:

```
templates/export/html/_ads/header.blade.php
```

4. Export ulang dan deploy:

```bash
php shuriken image:export --platform=html --theme=five --site=resep
php shuriken site:deploy --site=resep
```

Iklan AdSense tayang otomatis ketika pengunjung datang dari search engine (Google) atau media sosial (Facebook, Pinterest, Twitter, dll) — tanpa perlu mendaftarkan site resep ke AdSense.

---

## Step 5: Indexing

Daftarkan sitemap ke **[nerd.dojo.cc](https://nerd.dojo.cc)**:

```
https://resep-nusantara.pages.dev/sitemap.xml
```

Artikel long-tail resep cenderung cepat terindeks karena kompetisi rendah.

---

## Tidak Harus Bahasa Indonesia

Workflow ini berlaku untuk bahasa apapun. Shuriken scraping dari Bing yang mendukung multi-bahasa, dan AI provider akan generate konten sesuai bahasa keyword yang diberikan.

Tier 1 bukan cuma US/UK — Jerman, Belanda, Swedia, Norwegia, dan Prancis juga Tier 1 dengan RPM AdSense yang bisa lebih tinggi dari US untuk niche tertentu.

| Bahasa | Flag | Tier |
|---|---|---|
| Jerman | `--language=de --country=de` | Tier 1, RPM tinggi |
| Belanda | `--language=nl --country=nl` | Tier 1, RPM sangat tinggi |
| Swedia | `--language=sv --country=se` | Tier 1 |
| Norwegia | `--language=nb --country=no` | Tier 1 |
| Prancis | `--language=fr --country=fr` | Tier 1 |
| Spanyol (Spanyol) | `--language=es --country=es` | Tier 1 + bonus trafik Mexico/Argentina/Colombia |
| Portugis (Portugal) | `--language=pt --country=pt` | Tier 1 + bonus trafik Brazil (volume sangat besar) |
| Turki | `--language=tr --country=tr` | Tier 2, volume besar |
| Arab | `--language=ar --country=sa` | Tier 2, volume besar |

```bash
# Resep Jerman
php shuriken image:scrape --keywords_txt --ai --provider=groq \
  --prompt=recipe --language=de --country=de

# Resep Belanda
php shuriken image:scrape --keywords_txt --ai --provider=groq \
  --prompt=recipe --language=nl --country=nl

# Resep Turki
php shuriken image:scrape --keywords_txt --ai --provider=groq \
  --prompt=recipe --language=tr --country=tr
```

Isi `keywords.txt` dalam bahasa target masing-masing. AI provider akan otomatis generate konten sesuai bahasa keyword.

---

## Hasil yang Diharapkan

Mulai dengan **300–500 artikel** — cukup untuk membangun topical authority di niche resep.

Tambah bertahap setiap minggu seiring trafik mulai masuk. Dengan 200–500 artikel:

- Trafik organik mulai masuk dalam 2–6 minggu
- Keyword long-tail spesifik lebih mudah ranking dibanding keyword head
- Setiap klik iklan menghasilkan pendapatan pasif otomatis

### Batas Jumlah File per Platform

Perhatikan limit masing-masing platform hosting gratis:

| Platform | Limit file | Keterangan |
|---|---|---|
| **Cloudflare Pages** | 20.000 file | Per deployment |
| **Vercel** | 10.000 file | Per deployment |
| **Netlify** | Tidak terbatas | — |
| **GitHub Pages** | Tidak ada limit resmi | Repo max 1GB |
| **Surge** | Tidak terbatas | — |

Gunakan `--chunk` saat export untuk membatasi jumlah artikel per ZIP:

```bash
# Maksimal 500 artikel per deploy
php shuriken image:export --platform=html --theme=five --site=resep --chunk=500
```

Untuk Cloudflare Pages, sisakan ruang untuk file aset tema (CSS, JS, gambar) — aman di bawah 18.000 artikel per deployment.

---

## Tips

**Tambah keyword baru secara berkala** — update `keywords.txt` dengan keyword musiman setiap bulan (misal: `resep kue lebaran`, `menu buka puasa`), lalu jalankan ulang scraping. Tetap dalam niche resep agar topical authority terus terbangun.

**Banyak niche, banyak site** — workflow ini bisa diulang untuk niche lain (kue, minuman, masakan daerah) dengan effort minimal karena setup sudah terbukti.
