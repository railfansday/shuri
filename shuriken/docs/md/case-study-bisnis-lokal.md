# Case Study: Promosi Bisnis Lokal dengan Shuriken

> **Studi kasus:** Agen Umroh ingin mendapatkan leads organik dari Google tanpa bayar iklan. Solusinya: bangun *topical authority* lewat ratusan artikel informatif tentang umroh, selipkan CTA di setiap artikel, deploy gratis, dan daftarkan ke indexing.

---

## Overview

| | |
|---|---|
| **Niche** | Agen Umroh / Travel Religi |
| **Tujuan** | Lead generation organik (WhatsApp / form pendaftaran) |
| **Tools** | [Shuriken](https://dojo.cc/shuriken) + Groq (gratis) + Cloudflare Pages + nerd.dojo.cc |
| **Estimasi waktu** | 1–2 jam setup, lalu otomatis |

---

## Step 1: Riset Keyword Umroh

Gunakan **Keyword Generator** dari dojo.cc untuk menemukan ratusan keyword long-tail seputar umroh.

1. Buka **[dojo.cc/keyword-tools/keywords-generator](https://dojo.cc/keyword-tools/keywords-generator)**
2. Masukkan seed keyword: `umroh`, `biaya umroh`, `paket umroh`
3. Export hasilnya ke `keywords.txt`
4. Letakkan file di folder Shuriken:

```bash
# Contoh isi keywords.txt
biaya umroh 2026
paket umroh ramadan murah
syarat umroh untuk lansia
umroh pertama kali tips
perlengkapan umroh wanita
jadwal umroh bulan januari
umroh plus turki berapa hari
cara daftar umroh online
```

---

## Step 2: Edit Template — Selipkan Promosi Agen

Sebelum scraping, edit template agar setiap artikel mengandung CTA promosi agen Anda.

Buka file template konten:

```
templates/image/content.blade.php
```

Tambahkan blok promosi di tengah artikel, misalnya setelah paragraf pertama:

```html
<!-- CTA Agen Umroh -->
<div style="background:#f0f7ff;border-left:4px solid #1a73e8;padding:1rem 1.2rem;margin:1.5rem 0;border-radius:4px">
  <strong>🕌 Rencanakan Umroh Anda Bersama Kami</strong><br>
  Kami menyediakan paket umroh terpercaya, berangkat setiap bulan.<br>
  <a href="https://wa.me/6281234567890?text=Halo,%20saya%20ingin%20info%20paket%20umroh"
     style="display:inline-block;margin-top:.5rem;background:#25d366;color:#fff;padding:.4rem .9rem;border-radius:4px;text-decoration:none">
    💬 Hubungi via WhatsApp
  </a>
</div>
```

> Ganti `6281234567890` dengan nomor WhatsApp agen Anda.

---

## Step 3: Generate Artikel Topical Authority

Jalankan scraping menggunakan `keywords.txt` yang sudah disiapkan:

```bash
php shuriken image:scrape \
  --keywords_txt \
  --ai \
  --provider=groq \
  --prompt=faq \
  --concurrency=3
```

**Penjelasan flag:**
- `--keywords_txt` — baca keyword dari `keywords.txt`
- `--ai` — generate konten dengan AI
- `--provider=groq` — pakai Groq (gratis, cepat)
- `--prompt=faq` — format FAQ cocok untuk konten informatif umroh
- `--concurrency=3` — 3 keyword paralel (aman untuk rate limit Groq)

Kalau belum punya token Groq:

```bash
# Daftar gratis di console.groq.com
php shuriken token add groq gsk_...
```

### Opsional: Pakai Foto Sendiri

Kalau punya koleksi foto umroh sendiri (Kabah, Madinah, jamaah, dll), bisa dipakai sebagai gambar artikel — tanpa scraping dari Bing.

Buat file `images.txt` di folder Shuriken, satu URL per baris:

```
https://agenumroh.com/foto/kabah-1.jpg
https://agenumroh.com/foto/madinah-senja.jpg
https://agenumroh.com/foto/jamaah-ihram.jpg
```

Lalu tambahkan flag `--images_txt`:

```bash
php shuriken image:scrape \
  --keywords_txt \
  --ai \
  --provider=groq \
  --prompt=faq \
  --concurrency=3 \
  --images_txt
```

> **Catatan:** Gambar sendiri lebih aman secara copyright, tapi kemungkinan ranking di Google Image Search lebih kecil dibanding gambar yang diambil dari Bing.

---

## Step 4: Daftarkan Site, Export & Deploy ke Cloudflare Pages

Daftarkan site **sebelum export** agar sitemap otomatis menggunakan URL yang benar:

```bash
# Daftarkan site (sekali saja)
# Dapatkan account_id dan api_token di: dash.cloudflare.com → Workers & Pages → API
php shuriken site add umroh cloudflare-pages {account_id} umroh-agen {api_token}
```

> URL site Anda sudah bisa diprediksi: `https://umroh-agen.pages.dev` — karena Cloudflare Pages selalu format `{project_name}.pages.dev`.

Export dengan flag `--site` agar sitemap otomatis pakai URL yang benar:

```bash
php shuriken image:export \
  --platform=html \
  --theme=five-light \
  --site_name="Agen Umroh Terpercaya" \
  --site=umroh
```

Deploy:

```bash
php shuriken site:deploy --site=umroh
```

Site Anda akan live di `https://umroh-agen.pages.dev` — **gratis, tanpa batas bandwidth.**

> Kalau subdomain sudah diambil orang lain, lihat [tips memilih nama subdomain](../commands/site-deploy.html#content-subdomain-sudah-diambil).

---

## Step 5: Indexing via nerd.dojo.cc

Agar artikel cepat terindeks Google, daftarkan sitemap ke indexing tool:

1. Buka **[nerd.dojo.cc](https://nerd.dojo.cc)**
2. Masukkan URL sitemap: `https://umroh-agen.pages.dev/sitemap.xml`
3. Submit — tool akan ping Google untuk mempercepat crawling

> Shuriken otomatis membuat `sitemap.xml` saat export HTML. Pastikan flag `--sitemap` aktif (default: aktif).

---

## Variasi: Sudah Punya Website WordPress?

Kalau agen sudah punya website WordPress, tidak perlu deploy ke hosting baru. Shuriken bisa publish artikel langsung ke WordPress via REST API — tanpa masuk dashboard, tanpa import manual.

### Setup (sekali saja)

Aktifkan **Application Password** di WordPress:
1. Masuk ke WordPress → **Users → Profile**
2. Scroll ke bawah → **Application Passwords**
3. Beri nama (misal: `Shuriken`) → klik **Add New Application Password**
4. Salin password yang muncul

Daftarkan site:

```bash
php shuriken site add umroh wordpress https://agenumroh.com admin "xxxx xxxx xxxx xxxx"
```

### Workflow

Sama seperti sebelumnya sampai Step 3, lalu ganti Step 4 & 5 dengan:

```bash
# Publish langsung ke WordPress
php shuriken post:publish --site=umroh
```

Artikel langsung masuk WordPress sebagai draft atau published (sesuai config). Tidak perlu export, tidak perlu deploy.

### Indexing

WordPress sudah punya sitemap bawaan (`/sitemap.xml` atau via plugin Yoast/Rank Math). Daftarkan langsung ke [nerd.dojo.cc](https://nerd.dojo.cc):

```
https://agenumroh.com/sitemap.xml
```

---

## Hasil yang Diharapkan

Dengan 50–200 artikel topical authority tentang umroh:

- Google mengenali site sebagai **sumber otoritatif** untuk keyword umroh
- Artikel long-tail mulai ranking dalam 2–8 minggu
- Setiap artikel mengandung CTA → pengunjung organik langsung bisa kontak agen via WhatsApp

---

## Tips Optimasi

**Ganti nomor WhatsApp secara dinamis** — edit template agar tiap artikel punya pesan WA yang relevan dengan keyword:

```php
{{-- templates/image/content.blade.php --}}
<a href="https://wa.me/6281234567890?text=Halo+saya+ingin+info+{{ urlencode($keyword->name) }}">
  Tanya soal "{{ $keyword->name }}"
</a>
```

**Pakai custom domain** — arahkan domain agen (misal `agenumroh.com`) ke Cloudflare Pages untuk tampilan lebih profesional.

**Update konten berkala** — tambahkan keyword baru ke `keywords.txt` tiap bulan (misal: keyword musiman seperti `umroh ramadan 2026`, `umroh akhir tahun`) lalu jalankan ulang scraping. Tetap dalam niche agar topical authority terus terbangun.
