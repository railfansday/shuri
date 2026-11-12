# Case Study: Niche Nickname & Username — Trafik Tier 1 + Adsterra

> **Studi kasus:** Bangun site kumpulan nickname/username untuk gamer dan pengguna sosmed, targetkan trafik dari negara Tier 1 (US, UK, CA, AU), monetisasi dengan Adsterra.

---

## Overview

| | |
|---|---|
| **Niche** | Nickname & username ideas (gaming, sosmed) |
| **Target trafik** | US, UK, CA, AU (Tier 1) |
| **Tujuan** | Passive income dari Adsterra (RPM Tier 1 jauh lebih tinggi) |
| **Tools** | [Shuriken](https://dojo.cc/shuriken) + Groq + Cloudflare Pages + [Adsterra](https://beta.publishers.adsterra.com/referral/hdKsY5N1yr) |
| **Estimasi waktu** | 1–2 jam setup |

---

## Kenapa Niche Ini?

- Volume keyword global sangat besar — jutaan pencarian tiap bulan
- Kompetisi longtail rendah: "cute Roblox usernames for girls 2026" hampir tidak ada artikel khusus
- RPM Adsterra Tier 1 bisa 5–15x lebih tinggi dibanding trafik Indonesia
- Konten evergreen — orang selalu butuh nama baru untuk akun baru
- Format list sangat cocok untuk `--prompt=list`

---

## Step 1: Siapkan Keyword

Buat `keywords.txt` dengan keyword berbahasa Inggris, fokus ke intent "cari nama/username":

```
cool Minecraft usernames
aesthetic Instagram names for girls
funny Discord server names
gaming clan names ideas
cute Roblox usernames not taken
dark aesthetic usernames
TikTok username ideas 2026
best gaming names for boys
unique usernames for YouTube channel
good names for Valorant players
fantasy username ideas
short cool usernames
aesthetic usernames with stars
sweaty Fortnite names
aesthetic cottagecore usernames
```

> **Butuh lebih banyak keyword?** Gunakan **[Keywords Generator](https://dojo.cc/keyword-tools/keywords-generator)** — masukkan seed seperti `usernames`, `nicknames`, `gaming names`, hasilnya ratusan keyword longtail siap pakai.

> **Tip:** Makin spesifik keyword, makin mudah ranking. "cute aesthetic usernames for Instagram 2026" lebih mudah dibanding "cool usernames".

---

## Step 2: Generate Artikel

```bash
php shuriken image:scrape \
  --keywords_txt \
  --ai \
  --provider=groq \
  --prompt=list \
  --language=en \
  --country=us \
  --concurrency=3
```

**Penjelasan flag:**
- `--prompt=list` — format "50 best nicknames for..." yang cocok untuk niche ini
- `--language=en --country=us` — Bing scrape gambar dari index US, konten AI di-generate dalam bahasa Inggris
- `--provider=groq` — gratis, cepat

### Pilihan AI Gratis

| Provider | Daftar di | Catatan |
|---|---|---|
| **Groq** | console.groq.com | Gratis, paling cepat |
| **Cerebras** | cloud.cerebras.ai | Gratis, sangat cepat |
| **OpenRouter** | openrouter.ai | Banyak model gratis tersedia |
| **Gemini** | aistudio.google.com | Free tier generous |
| **Ollama** | ollama.com | Gratis, lokal, tidak perlu internet |

```bash
php shuriken token add groq gsk_...
php shuriken token add cerebras csk_...
php shuriken token add openrouter sk-or-...
php shuriken token add gemini AIza...
```

---

## Step 3: Pasang Iklan Adsterra

Daftar di **[Adsterra](https://beta.publishers.adsterra.com/referral/hdKsY5N1yr)** (gratis, langsung approved tanpa review period).

Rekomendasi format iklan untuk niche ini:

| Format | Slot | Keterangan |
|---|---|---|
| **Social Bar** | `header.blade.php` | Push notification style, RPM tinggi, tidak ganggu layout |
| **Native Banner** | `in_article.blade.php` | Menyatu dengan konten list, CTR bagus |
| **Popunder** | `header.blade.php` | RPM tertinggi, trigger saat halaman dibuka |

Tempel script di slot yang tersedia:

```
templates/export/html/_ads/header.blade.php
templates/export/html/_ads/in_article.blade.php
```

---

## Step 4: Export & Deploy

### Pilihan Platform Deploy Gratis

| Platform | Perintah | URL hasil |
|---|---|---|
| **Cloudflare Pages** | `php shuriken site add nick cloudflare-pages {account_id} nickname-ideas {api_token}` | `nickname-ideas.pages.dev` |
| **Vercel** | `php shuriken site add nick vercel nickname-ideas {api_token}` | `nickname-ideas.vercel.app` |
| **Netlify** | `php shuriken site add nick netlify {site_id} {api_token}` | `{site_id}.netlify.app` |
| **GitHub Pages** | `php shuriken site add nick github-pages {owner} {repo} {token}` | `{owner}.github.io/{repo}` |
| **Surge** | `php shuriken site add nick surge nickname-ideas.surge.sh {token}` | `nickname-ideas.surge.sh` |

```bash
# Daftarkan site (pilih salah satu platform di atas)
php shuriken site add nick cloudflare-pages {account_id} nickname-ideas {api_token}

# Export
php shuriken image:export \
  --platform=html \
  --theme=six \
  --site_name="Nickname Ideas" \
  --site=nick

# Deploy
php shuriken site:deploy --site=nick
```

Site live di `https://nickname-ideas.pages.dev`.

> Kalau subdomain sudah diambil orang lain, lihat [tips memilih nama subdomain](../commands/site-deploy.html#content-subdomain-sudah-diambil).

---

## Step 5: Indexing

```
https://nickname-ideas.pages.dev/sitemap.xml
```

Daftarkan ke **[nerd.dojo.cc](https://nerd.dojo.cc)**.

---

## Hasil yang Diharapkan

Mulai dengan **300–500 artikel** — cukup untuk membangun topical authority. Tambah bertahap setiap minggu.

### Batas Jumlah File per Platform

| Platform | Limit file | Keterangan |
|---|---|---|
| **Cloudflare Pages** | 20.000 file | Per deployment |
| **Vercel** | 10.000 file | Per deployment |
| **Netlify** | Tidak terbatas | — |
| **GitHub Pages** | Tidak ada limit resmi | Repo max 1GB |
| **Surge** | Tidak terbatas | — |

```bash
# Batasi jumlah artikel per deploy
php shuriken image:export --platform=html --theme=six --site=nick --chunk=500
```

Dengan trafik Tier 1:

- RPM Adsterra Social Bar: $1–5 per 1000 pageview (vs $0.1–0.5 untuk trafik Indonesia)
- Keyword gaming longtail cenderung cepat terindeks — kompetisi rendah
- Trafik dari Pinterest dan Reddit bisa jadi bonus (format list sangat shareable)

---

## Tidak Harus Bahasa Inggris

Tier 1 bukan cuma US/UK. Jerman, Prancis, Belanda, Swedia, Norwegia, dan Denmark juga Tier 1 — RPM Adsterra bahkan bisa lebih tinggi dari US untuk beberapa niche.

| Bahasa | Negara | Flag |
|---|---|---|
| Jerman | `--language=de --country=de` | Tier 1, RPM tinggi |
| Prancis | `--language=fr --country=fr` | Tier 1 |
| Belanda | `--language=nl --country=nl` | Tier 1, RPM sangat tinggi |
| Swedia | `--language=sv --country=se` | Tier 1 |
| Norwegia | `--language=nb --country=no` | Tier 1 |
| Spanyol (Spanyol) | `--language=es --country=es` | Tier 1 + bonus trafik Mexico/Argentina/Colombia |
| Portugis (Portugal) | `--language=pt --country=pt` | Tier 1 + bonus trafik Brazil (volume sangat besar) |

Contoh untuk pasar Jerman:

```bash
# keywords.txt dalam bahasa Jerman
# coole Minecraft Nutzernamen
# aesthetic Instagram Namen für Mädchen
# lustige Discord Servernamen

php shuriken image:scrape \
  --keywords_txt \
  --ai \
  --provider=groq \
  --prompt=list \
  --language=de \
  --country=de \
  --concurrency=3
```

Isi `keywords.txt` dalam bahasa target. AI provider akan otomatis generate konten sesuai bahasa keyword.

---

## Tips

**Perbanyak variasi platform** — tambahkan keyword untuk platform baru yang sedang tren:

```
usernames for Threads app
cool Bluesky handles
aesthetic Twitch channel names
```

**Scale ke niche serupa** — workflow yang sama berlaku untuk:
- "quotes about [topic]" — Tier 1, volume besar
- "aesthetic words for usernames" — longtail tak ada habisnya
- "words that mean [something]" — trafik kamus informal
