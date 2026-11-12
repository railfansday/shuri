# site

Kelola tujuan publish — tambah, hapus, lihat daftar, dan test koneksi ke WordPress atau platform deploy.

Kredensial disimpan di `storage/sites.json`.

---

## Subcommand

### `site list`

Tampilkan semua site yang sudah dikonfigurasi.

```bash
php shuriken site list
```

Output contoh:
```
Configured Sites
──────────────────────────────────────────────────
  myblog     [wordpress]        https://myblog.com
  mynetlify  [netlify]          site_id=abc123
  mycfpages  [cloudflare-pages] mysite.pages.dev
  myvercel   [vercel]           mysite.vercel.app
  mysurge    [surge]            mysite.surge.sh
  mygithub   [github-pages]     budisetiawan/myblog
```

---

### `site add`

Tambahkan site baru.

```bash
# WordPress
php shuriken site add {name} wordpress {url} {username} {password}

# Netlify
php shuriken site add {name} netlify {site_id} {api_token}

# Cloudflare Pages
php shuriken site add {name} cloudflare-pages {account_id} {project_name} {api_token}

# Vercel
php shuriken site add {name} vercel {project_name} {api_token} [team_id]

# Surge
php shuriken site add {name} surge {domain} {token}

# GitHub Pages
php shuriken site add {name} github-pages {owner} {repo} {token}
```

---

### `site remove`

Hapus site.

```bash
php shuriken site remove myblog
```

---

### `site test`

Test koneksi ke site.

```bash
php shuriken site test myblog
```

Output sukses:
```
Testing myblog [wordpress] https://myblog.com...
✓ Connected as: Admin User (312ms)
```

Output gagal:
```
✗ Failed (120ms): 401 Unauthorized
```

---

## Platform yang Didukung

| Platform | Flag | Command | Keterangan |
|---|---|---|---|
| WordPress | `wordpress` | `post:publish` | Publish per-post via REST API |
| Netlify | `netlify` | `site:deploy` | Deploy static site, 100GB/bln |
| Cloudflare Pages | `cloudflare-pages` | `site:deploy` | Deploy static site, bandwidth unlimited |
| Vercel | `vercel` | `site:deploy` | Deploy static site, 100GB/bln |
| Surge | `surge` | `site:deploy` | Deploy static site, unlimited deploy gratis |
| GitHub Pages | `github-pages` | `site:deploy` | Deploy static site, 100GB/bln |

---

## Contoh per Platform

### WordPress

```bash
php shuriken site add myblog wordpress https://myblog.com admin "xxxx xxxx xxxx xxxx xxxx xxxx"
```

> **Application Password** — bukan password login biasa. Generate di:
> WordPress Admin → **Users** → **Profile** → scroll ke bawah → **Application Passwords** → beri nama → **Add New Application Password**

---

### Netlify

```bash
php shuriken site add mynetlify netlify {site_id} {api_token}
```

---

### Cloudflare Pages

```bash
php shuriken site add mycfpages cloudflare-pages {account_id} {project_name} {api_token}
```

---

### Vercel

```bash
php shuriken site add myvercel vercel {project_name} {api_token}
# Dengan team ID (opsional):
php shuriken site add myvercel vercel {project_name} {api_token} {team_id}
```

---

### Surge

```bash
php shuriken site add mysurge surge mysite.surge.sh {token}
```

Untuk mendapatkan token:
```bash
npm install -g surge
surge login
surge token
```

---

### GitHub Pages

```bash
php shuriken site add mygithub github-pages {owner} {repo} {token}
# Contoh:
php shuriken site add myblog github-pages budisetiawan myblog-site ghp_xxxxxxxxxxxx
```

Token: GitHub → Settings → Developer settings → Personal access tokens → scope `repo`.

---

## File `storage/sites.json`

```json
{
  "myblog": {
    "platform": "wordpress",
    "url": "https://myblog.com",
    "username": "admin",
    "password": "xxxx xxxx xxxx xxxx xxxx xxxx"
  },
  "mynetlify": {
    "platform": "netlify",
    "site_id": "abc123",
    "api_token": "token123"
  },
  "mysurge": {
    "platform": "surge",
    "domain": "mysite.surge.sh",
    "token": "token123"
  },
  "mygithub": {
    "platform": "github-pages",
    "owner": "budisetiawan",
    "repo": "myblog-site",
    "token": "ghp_xxxxxxxxxxxx"
  }
}
```

---

## Workflow

```bash
# 1. Tambah site
php shuriken site add myblog wordpress https://myblog.com admin "app-password"

# 2. Test koneksi
php shuriken site test myblog

# 3. Publish posts ke WordPress
php shuriken post:publish --site=myblog

# 4. Deploy HTML ke hosting statis
php shuriken site:deploy --site=mynetlify --theme=five-dark
```

Lihat [post:publish](post-publish.html) dan [site:deploy](site-deploy.html) untuk opsi lengkap.
