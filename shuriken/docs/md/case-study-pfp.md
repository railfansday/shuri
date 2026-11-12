# Case Study: Niche PFP (Profile Picture) — Trafik Tier 1 + Adsterra

> **Studi kasus:** Bangun site galeri profile picture, targetkan trafik global Tier 1, monetisasi dengan Adsterra — tanpa perlu AI, cukup template gallery khusus.

---

## Overview

| | |
|---|---|
| **Niche** | PFP / Profile Picture (aesthetic, anime, gaming, dll) |
| **Target trafik** | Global, Tier 1 (US, UK, AU, CA) |
| **Tujuan** | Passive income dari Adsterra |
| **Tools** | [Shuriken](https://dojo.cc/shuriken) + Cloudflare Pages + [Adsterra](https://beta.publishers.adsterra.com/referral/hdKsY5N1yr) |
| **Estimasi waktu** | < 1 jam setup |

---

## Kenapa Niche Ini?

- Volume keyword sangat besar — "aesthetic pfp", "anime pfp", "dark pfp", "pfp for [platform]"
- Konten utamanya gambar — Shuriken scrape gambar dari Bing yang sudah relevan
- **Tidak butuh AI** — template gallery khusus sudah cukup, hemat kuota API
- Trafik Tier 1 global, banyak dari sosmed dan image search
- Closely related ke niche nickname — bisa deploy ke domain yang sama

---

## Step 1: Siapkan Keyword

Gunakan **[Keywords Generator](https://dojo.cc/keyword-tools/keywords-generator)** dengan seed `pfp`, `profile picture`, `profile pic`:

Contoh `keywords.txt`:

```
aesthetic pfp
anime pfp for girls
dark aesthetic pfp
cute pfp for discord
pfp for instagram
matching pfp for best friends
anime pfp boy
aesthetic pfp pink
cool pfp for gaming
pfp no face aesthetic
cottagecore pfp
y2k aesthetic pfp
black and white pfp
soft aesthetic pfp
pfp ideas for tiktok
```

---

## Step 2: Scrape & Generate

Tidak perlu `--ai` — cukup scrape gambar dan gunakan template gallery khusus:

```bash
php shuriken image:scrape \
  --keywords_txt \
  --template=pfp \
  --language=en \
  --country=us \
  --image_count=9 \
  --concurrency=5
```

**Penjelasan flag:**
- `--template=pfp` — gunakan template gallery khusus pfp, bukan template artikel biasa
- `--image_count=9` — 9 gambar per keyword (tidak butuh `--execute_javascript`)
- Tidak ada `--ai` — hemat, cepat, tidak butuh token

---

## Step 3: Pasang Iklan Adsterra

Daftar di **[Adsterra](https://beta.publishers.adsterra.com/referral/hdKsY5N1yr)** — langsung approved tanpa review period.

Format yang cocok untuk niche visual:

| Format | Slot | Keterangan |
|---|---|---|
| **Social Bar** | `header.blade.php` | RPM tinggi, tidak ganggu gallery |
| **Popunder** | `header.blade.php` | Trigger saat halaman dibuka |
| **Native Banner** | `in_article.blade.php` | Di antara grid gambar |

```
templates/export/html/_ads/header.blade.php
templates/export/html/_ads/in_article.blade.php
```

---

## Step 4: Export & Deploy

### Pilihan Platform Deploy Gratis

| Platform | URL hasil |
|---|---|
| **Cloudflare Pages** | `aesthetic-pfp.pages.dev` |
| **Vercel** | `aesthetic-pfp.vercel.app` |
| **Netlify** | `aesthetic-pfp.netlify.app` |
| **GitHub Pages** | `{owner}.github.io/{repo}` |
| **Surge** | `aesthetic-pfp.surge.sh` |

```bash
# Daftarkan site
php shuriken site add pfp cloudflare-pages {account_id} aesthetic-pfp {api_token}

# Export
php shuriken image:export \
  --platform=html \
  --theme=eight \
  --site_name="Aesthetic PFP" \
  --site=pfp

# Deploy
php shuriken site:deploy --site=pfp
```

> Kalau subdomain sudah diambil orang lain, lihat [tips memilih nama subdomain](../commands/site-deploy.html#content-subdomain-sudah-diambil).

---

## Step 5: Indexing

```
https://aesthetic-pfp.pages.dev/sitemap.xml
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
php shuriken image:export --platform=html --theme=eight --site=pfp --chunk=500
```

Dengan trafik Tier 1:
- Trafik dari Google Image Search dan Pinterest bisa signifikan
- RPM Adsterra lebih tinggi dibanding trafik Indonesia
- Proses scraping lebih cepat karena tidak pakai AI

---

## Tidak Harus Bahasa Inggris

| Bahasa | Flag |
|---|---|
| Jerman | `--language=de --country=de` |
| Prancis | `--language=fr --country=fr` |
| Belanda | `--language=nl --country=nl` |
| Spanyol (Spanyol) | `--language=es --country=es` | Tier 1 + bonus trafik Mexico/Argentina/Colombia |
| Portugis (Portugal) | `--language=pt --country=pt` | Tier 1 + bonus trafik Brazil (volume sangat besar) |

---

## Template Kustom

Template `pfp` yang dipakai di case study ini adalah contoh dari **custom content template** — kamu bisa membuat template sendiri untuk layout apapun tanpa mengubah kode inti Shuriken.

Pelajari cara membuat custom template: [Custom Content Templates](../custom-templates.html)

---

## Tips

**Gabungkan dengan niche nickname** — site pfp dan nickname targetkan audience yang sama (gamer, pengguna sosmed). Bisa deploy keduanya ke domain berbeda dari 1 workflow.

**Perluas ke niche serupa:**
```
wallpaper aesthetic     — "aesthetic wallpaper [tema]"
banner for youtube      — "youtube banner [tema]"
icon for discord        — "discord server icon [tema]"
```
