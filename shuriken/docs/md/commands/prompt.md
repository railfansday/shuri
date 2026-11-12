# prompt

Kelola custom prompt template — tambah, hapus, dan lihat daftar prompt yang tersedia.

Prompt custom disimpan di `storage/prompts.json` dan otomatis tersedia saat scraping.

---

## Subcommand

### `prompt list`

Tampilkan semua prompt — built-in dan custom.

```bash
php shuriken prompt list
```

Output contoh:
```
Built-in Prompts
──────────────────────────────────────────────────
  image
  recipe
  list
  review
  faq
  tutorial
  comparison

Custom Prompts
──────────────────────────────────────────────────
  properti   Write a property listing post about...
  otomotif   Write a car review post about...

Usage: php shuriken image:scrape --ai --prompt={name}
```

---

### `prompt add`

Tambahkan custom prompt baru.

```bash
php shuriken prompt add {name} {template}
```

- `name` — slug huruf kecil, boleh pakai tanda hubung (contoh: `properti`, `my-niche`)
- `template` — teks prompt, boleh menggunakan variabel template

Contoh:
```bash
php shuriken prompt add properti "Write a property listing post about {{keyword}} in {{tone}} tone, language {{language}}_{{country}}, HTML format. Start with an introductory p. For each feature use an h2, img with alt tag, and p. End with a Contact h2. Do not include h1. Minimum 400 words."
```

Jika nama sudah ada, prompt akan di-overwrite.

---

### `prompt remove`

Hapus custom prompt.

```bash
php shuriken prompt remove {name}
```

Contoh:
```bash
php shuriken prompt remove properti
```

Prompt built-in tidak bisa dihapus.

---

## Variabel Template

Gunakan variabel berikut dalam template prompt:

| Variabel | Nilai |
|---|---|
| `{{keyword}}` | Keyword yang sedang diproses |
| `{{images_json}}` | JSON array gambar (URL + judul) |
| `{{tone}}` | Gaya penulisan acak dari `ai.tones` |
| `{{language}}` | Kode bahasa (contoh: `id`, `en`) |
| `{{country}}` | Kode negara (contoh: `id`, `us`) |

---

## Prompt Built-in

### `image` — Blog post standar dengan gambar *(default)*

```
intro p
└── per gambar:
    ├── h2 (heading terkait topik)
    ├── img (dengan alt tag deskriptif)
    └── p
```

### `recipe` — Resep masakan

```
intro p
└── per section:
    ├── h2
    ├── img (dengan alt tag)
    ├── p (deskripsi)
    ├── ul (bahan-bahan)
    └── ol (langkah memasak)
```

### `list` — Listicle

```
intro p
└── 8–10 item:
    ├── h2 (nomor + judul item)
    └── p (penjelasan)
```

### `review` — Ulasan produk

```
intro p
├── h2: Pros
│   └── ul
├── h2: Cons
│   └── ul
└── h2: Verdict
    └── p
```

### `faq` — Tanya jawab

```
intro p
└── min. 8 pertanyaan:
    ├── h2 (pertanyaan)
    └── p (jawaban)
```

### `tutorial` — Panduan langkah demi langkah

```
intro p (apa yang akan dipelajari)
└── per langkah:
    ├── h2: "Step N: [judul]"
    ├── p (penjelasan)
    └── img (opsional, dengan alt tag)
```

### `comparison` — Perbandingan

```
intro p
├── per opsi/aspek:
│   ├── h2
│   ├── p
│   └── img (opsional)
└── h2: Recommendation
    └── p
```

### `news` — Artikel berita

```
lead p (fakta utama)
└── per section:
    ├── h2 (konteks / detail / implikasi)
    ├── p
    └── img (opsional)
```

### `roundup` — "Best of" / affiliate

```
intro p
├── per item:
│   ├── h2 (nomor + nama)
│   ├── img
│   ├── p (deskripsi)
│   └── ul (key features)
└── h2: Recommendation
    └── p (top pick)
```

### `guide` — Panduan komprehensif

```
intro p (apa yang akan dipelajari)
├── per aspek:
│   ├── h2
│   ├── p (penjelasan detail)
│   └── img (opsional)
└── h2: Conclusion
    └── p
```

---

## Penggunaan di image:scrape

```bash
# Pakai built-in prompt
php shuriken image:scrape --ai --prompt=review

# Pakai custom prompt
php shuriken image:scrape --ai --prompt=properti
```
