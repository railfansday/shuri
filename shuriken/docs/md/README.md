# Dokumentasi [Shuriken 7](https://dojo.cc/shuriken)

Shuriken adalah CLI tool untuk scraping gambar, generate konten blog dengan AI, dan export ke berbagai platform.

---

> 💰 **Ingin monetize traffic dari site hasil Shuriken?**
> Gunakan [**Adsterra**](https://beta.publishers.adsterra.com/referral/hdKsY5N1yr) — ad network yang cocok untuk site baru, tanpa minimum traffic. Tersedia banner, native ads, popunder, dan social bar. Lihat cara pasangnya di [Ads & Tracking](export-platforms.html#content-ads--tracking).

---

## Daftar Isi

### Memulai
- [Instalasi](installation.md) — Requirements, install, setup awal
- [Quick Start](quick-start.md) — Workflow dari nol sampai export

### AI & Providers
- [AI Providers](ai-providers.md) — Setup 19+ provider AI (OpenAI, Gemini, Claude, dll)
- [Panduan Biaya AI](ai-cost-guide.md) — Rekomendasi provider berdasarkan budget

### Export
- [Platform Export](export-platforms.md) — WordPress, Blogger, Hugo, CSV, Native, HTML
- [HTML Themes](html-themes.md) — 45 tema + opsi random

### Monetisasi
- [Panduan Adsterra & Integrasi Iklan](monetization.md) — Trik memasang slot ads dan optimasi revenue

### Referensi Command
- [image:scrape](commands/image-scrape.md) — Scraping gambar & generate konten
- [image:export](commands/image-export.md) — Export ke berbagai platform
- [clear:data](commands/clear-data.md) — Bersihkan database dan export
- [token](commands/token.md) — Kelola API token per provider
- [prompt](commands/prompt.md) — Kelola custom prompt template
- [site](commands/site.md) — Kelola publish destination
- [post:publish](commands/post-publish.md) — Publish post ke WordPress via API
- [site:deploy](commands/site-deploy.md) — Deploy HTML ke Netlify / Cloudflare Pages
- [mcp:serve](commands/mcp-serve.md) — MCP server untuk integrasi Claude Code & Gemini CLI

### Case Study
- [Promosi Bisnis Lokal (Agen Umroh)](case-study-bisnis-lokal.md) — Keyword lokal, template CTA, topical authority
- [Auto-Blog Resep + AdSense (ARSAE)](case-study-resep-adsense.md) — Blog resep masak, monetize AdSense tanpa daftar site
- [Affiliate Shopee via Indoleads](case-study-affiliate-shopee.md) — Konten produk + deeplink affiliate otomatis
- [Nickname Tier 1 + Adsterra](case-study-nickname-tier1.md) — Niche nickname multi-bahasa, traffic tier 1
- [Niche "Map Of" Tier 1](case-study-map-of.md) — Konten peta/lokasi, evergreen, traffic organik stabil
- [Niche PFP (Profile Picture)](case-study-pfp.md) — Gallery gambar tanpa AI, template kustom, Adsterra
- [Printable Worksheets](case-study-printable.md) — Aggregator edu worksheet, trafik Bing Tier 1, AdSense/Adsterra

### Lainnya
- [Custom Templates](custom-templates.md) — Buat template konten sendiri tanpa ubah kode inti
- [FAQ & Tips](faq.md) — Solusi umum, tips scraping, dan pertanyaan sering muncul
- [Konfigurasi Lengkap](configuration.md) — config/ai.php, shuriken.php, export.php

---

## Ringkasan Fitur

| Fitur | Detail |
|---|---|
| Sumber scraping | Bing Images |
| Custom images | `--images_txt` — pakai daftar URL sendiri dari `images.txt` |
| AI Provider | 19+ provider (15 API + 4 lokal/CLI) + custom OpenAI-compatible |
| Template prompt | 10 built-in (image, recipe, list, review, faq, tutorial, comparison, news, roundup, guide) + custom |
| Custom content template | `--template=nama` — folder `templates/nama/` dengan `content.blade.php` & `title.blade.php` |
| Deploy target | Netlify, Cloudflare Pages, Vercel, Surge, GitHub Pages |
| Format export | WordPress XML, Blogger XML, Hugo ZIP, CSV, SQLite, HTML ZIP |
| HTML Tema | 45 tema (11 keluarga × 4 variant + default) |
| Spintax | Ya — variasi teks otomatis `{opsi1\|opsi2}` |
| Token rotation | Ya — round-robin untuk rate limit |

## Perintah Cepat

```bash
# Pipeline otomatis penuh
php shuriken omakase

# Scrape + AI content
php shuriken image:scrape --keywords="resep masak" --ai --provider=deepseek

# Scrape tanpa AI, pakai custom template (misal PFP)
php shuriken image:scrape --keywords="anime pfp" --template=pfp

# Scrape pakai gambar dari images.txt (bukan Bing)
php shuriken image:scrape --keywords="keyword" --images_txt

# Export ke HTML
php shuriken image:export --platform=html --theme=five-dark

# Bersihkan semua data
php shuriken clear:data
```
