# post:publish

Publish post dari database langsung ke WordPress via REST API — tanpa perlu export XML dan import manual.

---

## Penggunaan

```bash
php shuriken post:publish --site={name}
```

---

## Options

| Option | Default | Keterangan |
|---|---|---|
| `--site` | — | Alias site dari `storage/sites.json` (**wajib**) |
| `--limit` | 50 | Jumlah post per run |
| `--start` | — | Filter tanggal mulai (Y-m-d) |
| `--end` | — | Filter tanggal selesai (Y-m-d) |
| `--status` | publish | Status post di WordPress (`publish` / `draft`) |

---

## Contoh

```bash
# Publish 50 post terbaru
php shuriken post:publish --site=myblog

# Publish sebagai draft dulu untuk review
php shuriken post:publish --site=myblog --limit=10 --status=draft

# Publish post dari rentang tanggal tertentu
php shuriken post:publish --site=myblog --start=2024-01-01 --end=2024-06-30

# Publish batch besar
php shuriken post:publish --site=myblog --limit=500
```

---

## Output

```
Connecting to myblog [wordpress]...
  ✓ Connected as: Admin User

Publishing 50 posts to https://myblog.com...

 [1/50] Judul post pertama ✔
 [2/50] Judul post kedua ✔
 [3/50] Judul post ketiga ✗
    ✗ rest_post_invalid_id: ...
 ...

Done. 49 published, 1 failed.
```

---

## Setup Awal

```bash
# 1. Tambah site (sekali saja)
php shuriken site add myblog wordpress https://myblog.com admin "app-password"

# 2. Test koneksi
php shuriken site test myblog

# 3. Publish
php shuriken post:publish --site=myblog
```

Lihat [site](site.html) untuk cara mendapatkan Application Password WordPress.

---

## Catatan

- **Konten:** Menggunakan `ai_content` jika tersedia, fallback ke `content`
- **Kategori & tag** dibuat otomatis di WordPress jika belum ada
- **Featured image** belum didukung di versi ini — akan ditambahkan di versi berikutnya
- **Duplikat:** WordPress tidak mencegah post duplikat secara otomatis. Pastikan tidak menjalankan `post:publish` dua kali untuk post yang sama
