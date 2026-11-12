# Instalasi [Shuriken 7](https://dojo.cc/shuriken)

## Requirements

| Requirement | Versi Minimum |
|---|---|
| PHP | 8.0+ |
| Composer | 2.x |
| Extension PHP | `curl`, `zlib`, `sqlite3` |
| Node.js (opsional) | Untuk `--execute_javascript` via Browsershot |

> **Rekomendasi Utama: Gunakan Laravel Herd**
>
> Jika PC belum terinstall PHP, **jangan dibikin ribet!** Cukup download dan install **[Laravel Herd](https://herd.laravel.com)**. Ini adalah cara termudah (*One-Click Install*) untuk menjalankan PHP di Windows dan Mac tanpa konfigurasi kerumitan *Environment Variables*. Install sekali, langsung siap pakai.

## Instalasi

### 1. Download & Extract

Login ke member area di **https://v4.dojo.cc**, download file rilis ZIP terbaru (misal: `shuriken-7.0.1.zip`).

**Extract ZIP:**
- **Windows** — klik kanan file ZIP → *Extract All* → pilih folder tujuan.
- **Mac** — double-click file ZIP, folder `shuriken-7` akan diextract otomatis.

### 2. Buka Terminal di Folder Shuriken

Agar perintah CLI Shuriken jalan, ente harus membuka Terminal atau PowerShell **tepat di dalam folder Shuriken** hasil extract tadi.

- **Windows** — Buka folder `shuriken-7`. Tahan tombol `Shift` lalu klik kanan di area putih yang kosong → pilih **"Open PowerShell window here"** atau **"Open in Terminal"**.
- **Mac** — Klik kanan ikon folder `shuriken-7` → pilih **"Services"** (jika ada) lalu **"New Terminal at Folder"**. Atau cara paling pasti: buka aplikasi Terminal, ketik `cd ` (pakai spasi), lalu _drag & drop_ folder `shuriken-7` ke dalam jendela terminal lalu tekan Enter.
- **Pengguna Laravel Herd** — Setelah instalasi Herd selesai, jalankan aplikasinya. Cukup klik ikon Herd di *Menu Bar/System Tray* lalu pilih ikon Terminal (atau langsung jalankan terminal bawaan laptop, otomatis PHP bawaan Herd sudah nyambung).

### 2. Konfigurasi AI (Opsional)

Tambahkan token AI provider via CLI:

```bash
php shuriken token add openai sk-proj-...
php shuriken token add gemini AIzaSy...
```

Lihat [AI Providers](ai-providers.md) untuk panduan lengkap semua provider.

## Verifikasi Instalasi

```bash
php shuriken --version
php shuriken list
```

## Menjalankan dengan JavaScript Support

Beberapa fitur seperti `--execute_javascript` membutuhkan Puppeteer via Browsershot:

```bash
npm install puppeteer
```

Atau jika menggunakan Google Chrome/Chromium yang sudah terinstall, cukup atur path-nya di konfigurasi Browsershot.

## Struktur Direktori

```
shuriken-7/
├── app/
│   ├── Commands/          — Command CLI
│   └── Exporters/         — Logic export per platform
├── config/
│   ├── ai.php             — Konfigurasi AI provider & token
│   ├── shuriken.php       — Konfigurasi scraping
│   ├── export.php         — Konfigurasi export
│   └── prompts.php        — Template prompt AI
├── database/
│   └── database.sqlite    — Database utama (dibuat saat migrate)
├── exports/               — Output file hasil export
├── templates/             — Blade template untuk konten & HTML export
├── docs/                  — Dokumentasi ini
└── shuriken               — File executable CLI
```

## Update

Download versi terbaru dari member area **https://v4.dojo.cc**, extract, selesai.
