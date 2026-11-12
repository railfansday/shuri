# Case Study: Affiliate Shopee via Indoleads

> **Studi kasus:** Bangun site review/roundup produk, pasang deeplink Shopee via Indoleads, monetisasi dari komisi setiap pembelian — tanpa perlu trafik berbayar.

---

## Overview

| | |
|---|---|
| **Niche** | Review & roundup produk (elektronik, fashion, rumah tangga, dll) |
| **Tujuan** | Komisi affiliate dari Shopee |
| **Tools** | [Shuriken](https://dojo.cc/shuriken) + Groq + Cloudflare Pages + Indoleads |
| **Estimasi waktu** | 1–2 jam setup |

---

## Step 1: Daftar Indoleads & Aktifkan Shopee

1. Daftar di **[indoleads.com](https://indoleads.com/register/publisher//qy9tjHTlAcQgZHaa)**
2. Apply ke campaign **Shopee Indonesia**
3. Setelah approved, buka **Tools → Deeplink**
4. Centang **Deep link**, masukkan URL Shopee contoh, klik Generate
5. Catat format deeplink kamu — bentuknya:
   ```
   https://iz5.xyz/dpl/YOUR_ID?url=ENCODED_URL
   ```

> **Catatan:** Saat hover label "SEO" di Traffic Restrictions, yang dilarang adalah *websites-satellites, doorways, landing pages* — bukan content site biasa. Site artikel informatif hasil Shuriken masuk kategori **Websites** yang diizinkan.

---

## Step 2: Siapkan Keyword

Gunakan keyword yang punya **intent membeli** — bukan sekadar informasi:

```
hp samsung terbaik 2026
laptop gaming under 10 juta
rice cooker terbaik untuk keluarga
sepatu running pria murah
headphone bluetooth terbaik
rekomendasi skincare untuk kulit berminyak
```

Simpan ke `keywords.txt` di folder Shuriken.

---

## Step 3: Generate Artikel Roundup

```bash
php shuriken image:scrape \
  --keywords_txt \
  --ai \
  --provider=groq \
  --prompt=roundup \
  --concurrency=3
```

`--prompt=roundup` menghasilkan format "X produk terbaik" yang natural untuk sisipan link affiliate.

---

## Step 4: Sisipkan Deeplink ke Template

Buka template konten:

```
templates/image/content.blade.php
```

Tambahkan CTA dengan deeplink dinamis — URL Shopee search otomatis mengikuti keyword artikel:

```php
{{-- CTA Affiliate Shopee --}}
<div style="background:#f5fff5;border-left:4px solid #ee4d2d;padding:1rem 1.2rem;margin:1.5rem 0;border-radius:4px">
  <strong>🛒 Cari Produk Terbaik di Shopee</strong><br>
  Temukan pilihan {{ $keyword->name }} dengan harga terbaik.<br>
  <a href="https://iz5.xyz/dpl/YOUR_ID?url={{ urlencode('https://shopee.co.id/search?keyword=' . $keyword->name) }}"
     target="_blank"
     rel="nofollow"
     style="display:inline-block;margin-top:.5rem;background:#ee4d2d;color:#fff;padding:.4rem .9rem;border-radius:4px;text-decoration:none">
    Lihat di Shopee →
  </a>
</div>
```

Ganti `YOUR_ID` dengan deeplink ID dari dashboard Indoleads kamu.

---

## Step 5: Export & Deploy

```bash
# Daftarkan site
php shuriken site add review cloudflare-pages {account_id} review-produk {api_token}

# Export
php shuriken image:export \
  --platform=html \
  --theme=seven \
  --site_name="Review Produk Terbaik" \
  --site=review

# Deploy
php shuriken site:deploy --site=review
```

> Kalau subdomain sudah diambil orang lain, lihat [tips memilih nama subdomain](../commands/site-deploy.html#content-subdomain-sudah-diambil).

---

## Step 6: Indexing

```
https://review-produk.pages.dev/sitemap.xml
```

Daftarkan ke **[nerd.dojo.cc](https://nerd.dojo.cc)**.

---

## Hasil yang Diharapkan

- Komisi Shopee via Indoleads per transaksi yang berhasil
- Keyword intent beli lebih cepat konversi dibanding keyword informasional
- Mulai dengan 300–500 artikel, tambah bertahap

---

## Kalau Komisi Kurang Maksimal

Affiliate e-commerce bergantung pada konversi — kalau trafik masuk tapi klik ke Shopee rendah, tambahkan monetisasi sekunder:

**[Adsterra](https://beta.publishers.adsterra.com/referral/hdKsY5N1yr) Social Bar** — format iklan push notification yang ringan, tidak mengganggu layout, cocok dikombinasikan dengan affiliate:

1. Daftar di **[Adsterra](https://beta.publishers.adsterra.com/referral/hdKsY5N1yr)**
2. Ambil script Social Bar
3. Tempel di `templates/export/html/_ads/header.blade.php`
4. Export ulang

Dengan kombinasi affiliate + Social Bar, setiap pengunjung menghasilkan pendapatan — baik yang klik ke Shopee maupun yang tidak.

---

## FAQ

**Apakah bisa pakai Shopee Affiliate langsung tanpa Indoleads?**

Belum diketahui pasti. Program affiliate Shopee langsung tidak mendokumentasikan secara publik apakah deeplink ke halaman selain produk spesifik didukung. Kalau tidak didukung, link hanya bisa ke 1 produk statis yang sama di semua artikel — kurang relevan untuk konten dinamis hasil Shuriken.

Indoleads dipilih karena deeplink-nya jelas mendukung parameter `?url=` yang bisa diisi URL apapun termasuk halaman search Shopee. Kalau ingin coba Shopee affiliate langsung, silakan — dan kalau berhasil, bagikan pengalamannya di komunitas.
