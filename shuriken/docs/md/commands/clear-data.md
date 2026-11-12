# clear:data

Hapus semua data — database dan folder exports.

```bash
php shuriken clear:data
```

---

## Yang Dihapus

1. **Database** — Reset tabel `keywords`, `images`, `posts`, `answers` (structure tetap ada, data dihapus)
2. **Folder `exports/`** — Semua file XML, ZIP, CSV, SQLite hasil export dihapus

---

## Kapan Digunakan

- Setelah selesai satu batch dan ingin mulai batch baru dari nol
- Ketika storage mulai penuh
- Sebelum scraping topik baru yang tidak ada kaitannya dengan data lama

---

## Alternatif: Bersihkan di Awal Scraping

Daripada menjalankan `clear:data` manual, bisa pakai flag langsung:

```bash
# Hapus data lama sebelum scraping baru
php shuriken image:scrape --keywords="resep" --clear_data

# Hapus exports sebelum export baru
php shuriken image:export --platform=wordpress --clear_post
```

| Flag | Command | Yang Dihapus |
|---|---|---|
| `--clear_data` | `image:scrape` | Database (keywords, images, posts) |
| `--clear_post` | `image:export` | Folder `exports/` |
| `clear:data` | — | Keduanya |

---

## Peringatan

Perintah ini **tidak bisa di-undo**. Pastikan file export sudah disimpan/dipindahkan sebelum menjalankan `clear:data`.
