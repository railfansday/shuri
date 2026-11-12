# site:deploy

Deploy HTML export langsung ke Netlify, Cloudflare Pages, atau Vercel — hosting gratis dengan CDN global.

---

## Penggunaan

```bash
php shuriken site:deploy --site={name}
```

---

## Options

| Option | Default | Keterangan |
|---|---|---|
| `--site` | — | Alias site dari `storage/sites.json` (**wajib**) |
| `--theme` | — | Export HTML dengan tema ini sebelum deploy |
| `--zip` | exports/html-1.zip | Path ZIP yang akan di-deploy |

---

## Contoh

```bash
# Deploy dari ZIP yang sudah ada
php shuriken image:export --platform=html --theme=five-dark
php shuriken site:deploy --site=mynetlify

# Export + deploy sekaligus
php shuriken site:deploy --site=mynetlify --theme=five-dark

# Deploy ke Cloudflare Pages
php shuriken site:deploy --site=mycfpages --theme=one-dark

# Deploy ZIP tertentu
php shuriken site:deploy --site=mynetlify --zip=exports/html-2.zip
```

---

## Output

```
Deploying mynetlify [netlify] (3.2 MB)...
  ✓ Live at: https://my-site.netlify.app (1840ms)
```

---

## Setup Platform

### Netlify

**Cara mendapatkan credentials:**

1. Login ke [netlify.com](https://netlify.com) → **Add new site** → **Deploy manually**
2. Ambil **Site ID**: Site Settings → General → Site ID
3. Ambil **API Token**: User Settings → Applications → Personal access tokens → **New access token**

```bash
php shuriken site add mysite netlify {site_id} {api_token}
php shuriken site test mysite
```

---

### Cloudflare Pages

**Cara mendapatkan credentials:**

1. Login ke [dash.cloudflare.com](https://dash.cloudflare.com) → **Workers & Pages** → **Create** → **Pages** → **Upload assets**
2. Buat project baru (Direct Upload) → catat nama project
3. Ambil **Account ID**: sidebar kanan dashboard
4. Ambil **API Token**: My Profile → API Tokens → **Create Token** → pilih template *Edit Cloudflare Workers* atau buat custom dengan permission `Pages:Edit`

```bash
php shuriken site add mysite cloudflare-pages {account_id} {project_name} {api_token}
php shuriken site test mysite
```

> **Batas halaman:** Cloudflare Pages gratis maksimal **20.000 file per deploy**. Jika jumlah post melebihi ~19.000 (ditambah halaman statis + sitemap), deploy akan gagal. Gunakan Netlify atau GitHub Pages sebagai alternatif untuk site dengan konten sangat banyak.

---

### Vercel

**Cara mendapatkan credentials:**

1. Login ke [vercel.com](https://vercel.com) → **Settings** → **Tokens** → **Create**
2. Catat nama project dari dashboard Vercel (atau buat project baru via **Add New Project**)
3. Untuk team/org account: ambil **Team ID** dari Settings → General → Team ID

```bash
php shuriken site add mysite vercel {project_name} {api_token}
# Dengan team ID (opsional):
php shuriken site add mysite vercel {project_name} {api_token} {team_id}
php shuriken site test mysite
```

---

## Platform yang Didukung

| Platform | Biaya | CDN | Bandwidth | Catatan SEO |
|---|---|---|---|---|
| Netlify | Gratis | Global | 100 GB/bln | — |
| Cloudflare Pages | Gratis | Global (lebih cepat) | Unlimited | Maks **20.000 file per deploy** |
| Vercel | Gratis | Global | 100 GB/bln | — |
| Surge | Gratis (unlimited deploy) | CDN | Unlimited | Subdomain `.surge.sh` di-noindex; butuh custom domain agar terindeks |
| GitHub Pages | Gratis | Global | 100 GB/bln | Mudah diindeks; **1 repo per akun** — jangan buat banyak repo untuk mass deploy, risiko banned |

---

### Surge

**Cara mendapatkan token:**

1. Install Surge CLI (butuh Node.js): `npm install -g surge`
2. Login atau buat akun: `surge login`
3. Ambil token: `surge token`
4. Daftarkan domain (gratis): cukup deploy sekali via CLI → `surge ./dist mysite.surge.sh`

```bash
php shuriken site add mysite surge mysite.surge.sh {token}
php shuriken site test mysite
```

> **Catatan SEO:** Subdomain `*.surge.sh` secara default ber-tag `noindex` di Google. Jika ingin terindeks mesin pencari, daftarkan custom domain dan arahkan ke Surge. Lihat dokumentasi Surge untuk setup custom domain.

---

### GitHub Pages

**Cara mendapatkan token:**

1. Login ke [github.com](https://github.com) → **Settings** → **Developer settings** → **Personal access tokens** → **Tokens (classic)**
2. Klik **Generate new token** → centang scope `repo` → **Generate**
3. Siapkan repository yang sudah ada (atau buat baru)
4. Aktifkan GitHub Pages: di repo → **Settings** → **Pages** → **Source**: pilih branch `gh-pages`

```bash
php shuriken site add mysite github-pages {owner} {repo} {token}
# Contoh:
php shuriken site add myblog github-pages budisetiawan myblog-site ghp_xxxxxxxxxxxx
php shuriken site test myblog
```

URL hasil deploy: `https://{owner}.github.io/{repo}`
Khusus repo bernama `{owner}.github.io`: `https://{owner}.github.io`

> **Catatan SEO:** GitHub Pages diindeks dengan baik oleh Google. Namun GitHub memiliki kebijakan ketat — **gunakan maksimal 1 repo per akun/organisasi** untuk deploy via Shuriken. Mass deploy ke banyak repo dari 1 akun berisiko akun diblokir.

---

## Subdomain Sudah Diambil?

Subdomain gratis seperti `*.pages.dev`, `*.vercel.app`, dan `*.netlify.app` dipakai jutaan orang — nama yang pendek dan mudah diingat biasanya sudah tidak tersedia.

**Solusi:**

1. **Tambah suffix/prefix** — `nickname-hub`, `best-nicknames-2026`, `nicknames-pro`, `mynameideas`
2. **Coba platform lain** — tiap platform punya pool subdomain berbeda; kalau `nama.pages.dev` penuh, coba `nama.netlify.app` atau `nama.vercel.app`
3. **Custom domain** — solusi paling bersih. Beli domain (bisa dari Namecheap, Niagahoster, dll), arahkan ke platform pilihan. Domain `.xyz` atau `.online` bisa dapat < $2/tahun

> Custom domain juga lebih baik untuk SEO dan branding dibanding subdomain gratisan.

---

## Tracking & Stats

Untuk menambahkan kode tracking (Google Analytics, Statcounter, Histats, dll), edit dulu `templates/export/html/_ads/footer.blade.php` sebelum export. Lihat [Platform Export → HTML](../export-platforms.md) untuk detailnya.

---

## Workflow Lengkap

```bash
# 1. Setup site (sekali saja)
php shuriken site add myblog netlify abc123-site-id token123

# 2. Test koneksi
php shuriken site test myblog

# 3. Scrape + generate konten
php shuriken image:scrape --trends=id --ai --provider=groq

# 4. Export + deploy sekaligus
php shuriken site:deploy --site=myblog --theme=five-dark
# → ✓ Live at: https://my-site.netlify.app
```
