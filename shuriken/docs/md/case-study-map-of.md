# Case Study: Niche "Map Of" — Trafik Tier 1 + Adsterra

> **Studi kasus:** Bangun site konten informatif dengan keyword "map of", targetkan trafik global Tier 1, monetisasi dengan Adsterra.

---

## Overview

| | |
|---|---|
| **Niche** | "Map of [tempat/topik]" |
| **Target trafik** | Global, Tier 1 (US, UK, AU, CA) |
| **Tujuan** | Passive income dari Adsterra |
| **Tools** | [Shuriken](https://dojo.cc/shuriken) + Groq + Cloudflare Pages + [Adsterra](https://beta.publishers.adsterra.com/referral/hdKsY5N1yr) |
| **Estimasi waktu** | 1–2 jam setup |

---

## Kenapa Niche Ini?

Keyword "map of X" terdengar seperti butuh peta visual, tapi kenyataannya banyak pencari yang puas dengan artikel informatif tentang topik tersebut — apalagi untuk longtail yang sangat spesifik.

- Volume keyword global sangat besar — ribuan variasi longtail aktif
- Kompetisi rendah untuk keyword spesifik
- Shuriken otomatis scrape gambar relevan dari Bing (hasil pencarian "map of X" banyak mengandung gambar peta)
- Trafik Tier 1 — sebagian besar pencarian datang dari US, UK, AU
- Evergreen — tidak ada expired date

---

## Step 1: Siapkan Keyword

Gunakan **[Keywords Generator](https://dojo.cc/keyword-tools/keywords-generator)** dengan seed `map of`:

Contoh `keywords.txt`:

```
map of Southeast Asia countries
map of ancient Rome roads
map of US states and capitals
map of Middle Earth
map of human body systems
map of African countries and capitals
map of South America rivers
map of Europe before World War 1
map of the silk road
map of ocean currents
map of world time zones
map of ancient Egypt
map of Australia states
map of major mountain ranges
map of trade routes medieval
```

> **Tip:** Keyword dengan konteks sejarah, geografi, dan sains cenderung paling mudah ranking karena konten edukatif.

---

## Step 2: Generate Artikel

```bash
php shuriken image:scrape \
  --keywords_txt \
  --ai \
  --provider=groq \
  --prompt=guide \
  --language=en \
  --country=us \
  --concurrency=3
```

**Penjelasan flag:**
- `--prompt=guide` — format artikel informatif/edukatif yang cocok untuk topik geografi
- `--language=en --country=us` — konten bahasa Inggris, gambar dari index Bing US
- Gambar yang di-scrape Bing untuk keyword "map of X" secara natural banyak mengandung gambar peta

### Pilihan AI Gratis

| Provider | Daftar di | Catatan |
|---|---|---|
| **Groq** | console.groq.com | Gratis, paling cepat |
| **Cerebras** | cloud.cerebras.ai | Gratis, sangat cepat |
| **OpenRouter** | openrouter.ai | Banyak model gratis tersedia |
| **Gemini** | aistudio.google.com | Free tier generous |
| **Ollama** | ollama.com | Gratis, lokal, tidak perlu internet |

---

## Step 3: Pasang Iklan Adsterra

Daftar di **[Adsterra](https://beta.publishers.adsterra.com/referral/hdKsY5N1yr)** — langsung approved tanpa review period.

Rekomendasi format untuk niche ini:

| Format | Slot | Keterangan |
|---|---|---|
| **Social Bar** | `header.blade.php` | RPM tinggi, tidak ganggu layout |
| **Native Banner** | `in_article.blade.php` | Menyatu dengan konten artikel |

```
templates/export/html/_ads/header.blade.php
templates/export/html/_ads/in_article.blade.php
```

---

## Step 4: Export & Deploy

### Pilihan Platform Deploy Gratis

| Platform | URL hasil |
|---|---|
| **Cloudflare Pages** | `mapof-info.pages.dev` |
| **Vercel** | `mapof-info.vercel.app` |
| **Netlify** | `mapof-info.netlify.app` |
| **GitHub Pages** | `{owner}.github.io/{repo}` |
| **Surge** | `mapof-info.surge.sh` |

```bash
# Daftarkan site
php shuriken site add mapof cloudflare-pages {account_id} mapof-info {api_token}

# Export
php shuriken image:export \
  --platform=html \
  --theme=six \
  --site_name="Map Of — Geography & History" \
  --site=mapof

# Deploy
php shuriken site:deploy --site=mapof
```

> Kalau subdomain sudah diambil orang lain, lihat [tips memilih nama subdomain](../commands/site-deploy.html#content-subdomain-sudah-diambil).

---

## Step 5: Indexing

```
https://mapof-info.pages.dev/sitemap.xml
```

Daftarkan ke **[nerd.dojo.cc](https://nerd.dojo.cc)**.

---

## Hasil yang Diharapkan

Mulai dengan **300–500 artikel**, tambah bertahap.

### Batas Jumlah File per Platform

| Platform | Limit file |
|---|---|
| **Cloudflare Pages** | 20.000 file per deployment |
| **Vercel** | 10.000 file per deployment |
| **Netlify** | Tidak terbatas |
| **GitHub Pages** | Repo max 1GB |
| **Surge** | Tidak terbatas |

```bash
php shuriken image:export --platform=html --theme=six --site=mapof --chunk=500
```

Dengan trafik Tier 1:
- RPM Adsterra lebih tinggi dari trafik Indonesia
- Keyword edukatif/geografi cenderung cepat terindeks
- Trafik dari Pinterest dan image search bisa jadi bonus karena banyak gambar peta di artikel

---

## Tidak Harus Bahasa Inggris

Tier 1 bukan cuma US/UK. Pasar Eropa seperti Jerman, Belanda, dan Swedia juga Tier 1 dengan RPM Adsterra yang kompetitif.

| Bahasa | Flag |
|---|---|
| Jerman | `--language=de --country=de` |
| Prancis | `--language=fr --country=fr` |
| Belanda | `--language=nl --country=nl` |
| Swedia | `--language=sv --country=se` |
| Norwegia | `--language=nb --country=no` |
| Spanyol (Spanyol) | `--language=es --country=es` | Tier 1 + bonus trafik Mexico/Argentina/Colombia |
| Portugis (Portugal) | `--language=pt --country=pt` | Tier 1 + bonus trafik Brazil (volume sangat besar) |

Contoh untuk pasar Jerman (keyword "Karte von X"):

```bash
php shuriken image:scrape \
  --keywords_txt \
  --ai \
  --provider=groq \
  --prompt=guide \
  --language=de \
  --country=de \
  --concurrency=3
```

Isi `keywords.txt` dengan keyword dalam bahasa target. AI provider akan otomatis generate konten sesuai bahasa keyword.

---

## Tips

**Perluas ke niche serupa** — keyword pattern yang sama berlaku untuk:

```
diagram of [topik]        — diagram of human heart, diagram of solar system
chart of [topik]          — chart of periodic elements, chart of world currencies
timeline of [topik]       — timeline of World War 2, timeline of Roman Empire
flag of [negara]          — flag of all countries, flag of African nations
```

Semua punya karakteristik yang sama: volume besar, longtail rendah kompetisi, trafik Tier 1.
