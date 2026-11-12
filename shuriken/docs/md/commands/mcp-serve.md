# mcp:serve

Jalankan Shuriken sebagai **MCP (Model Context Protocol) server** — memungkinkan AI CLI seperti Claude Code dan Gemini CLI memanggil Shuriken tools langsung via natural language, tanpa perlu hafal perintah.

---

## Penggunaan

```bash
php shuriken mcp:serve
```

Command ini tidak dijalankan manual — AI CLI yang spawn prosesnya secara otomatis setelah didaftarkan sekali.

---

## Tools yang Di-expose ke AI

| MCP Tool | Setara Command | Kegunaan |
|---|---|---|
| `scrape` | `image:scrape` | Scrape gambar + generate konten AI |
| `export` | `image:export --platform=html` | Export konten ke HTML |
| `deploy` | `site:deploy` | Deploy ke Netlify / Cloudflare / Vercel |
| `create_site` | scrape → export → deploy | Workflow lengkap satu langkah |
| `list_sites` | — | Lihat deployment targets yang tersedia |
| `list_tokens` | — | Lihat AI provider yang dikonfigurasi |
| `db_status` | — | Jumlah posts & gambar di database |
| `clear_data` | `clear:data` | Reset database dan exports |

---

## Setup di Claude Code

Pertama, cari tahu lokasi folder Shuriken di komputermu. Buka terminal, masuk ke folder Shuriken, lalu jalankan:

```bash
pwd
```

Perintah itu akan menampilkan path lengkap, misalnya `/Users/budi/Repos/shuriken-6`. Catat path tersebut — kamu akan memakainya di langkah berikutnya.

Lalu daftarkan Shuriken ke Claude Code (jalankan sekali saja):

```bash
claude mcp add shuriken -- php /Users/budi/Repos/shuriken-6/shuriken mcp:serve
```

> **Ganti** `/Users/budi/Repos/shuriken-6` dengan path hasil `pwd` tadi. Bagian `/shuriken` di akhir jangan dihapus — itu nama file-nya.

Verifikasi berhasil:

```bash
claude mcp list
# Seharusnya muncul: shuriken
```

Atau jika lebih suka edit file config langsung, buat file `.mcp.json` di root project:

```json
{
  "mcpServers": {
    "shuriken": {
      "command": "php",
      "args": ["/Users/budi/Repos/shuriken-6/shuriken", "mcp:serve"]
    }
  }
}
```

> **Ganti** `/Users/budi/Repos/shuriken-6/shuriken` dengan path lengkap ke file `shuriken` di komputermu.

---

## Setup di Gemini CLI

Sama seperti di atas — cari path dengan `pwd` dulu, lalu tambah ke `~/.gemini/settings.json`:

```json
{
  "mcpServers": {
    "shuriken": {
      "command": "php",
      "args": ["/Users/budi/Repos/shuriken-6/shuriken", "mcp:serve"]
    }
  }
}
```

> **Ganti** `/Users/budi/Repos/shuriken-6/shuriken` dengan path lengkap ke file `shuriken` di komputermu.

---

## Contoh Penggunaan

Setelah setup, cukup ketik di Claude Code / Gemini CLI:

> *"bikinkan situs html dengan niche map of the world, deploy ke myvercel"*

AI akan otomatis:
1. Call `list_sites` → cek `myvercel` tersedia
2. Call `list_tokens` → pilih AI provider yang aktif
3. Call `create_site` dengan `keywords="map of the world"`, `site="myvercel"`
4. Shuriken scrape → generate konten → export HTML → deploy
5. AI return: *"✓ Live at: https://my-project-abc123.vercel.app"*

Contoh lainnya:

> *"berapa post yang ada di database shuriken?"* → call `db_status`
>
> *"tampilkan site yang bisa dideploy"* → call `list_sites`
>
> *"scrape keyword resep ayam pakai groq"* → call `scrape`

---

## Protokol

MCP menggunakan **JSON-RPC 2.0 over stdio** (newline-delimited). AI CLI spawn `php shuriken mcp:serve` sebagai subprocess, lalu berkomunikasi via stdin/stdout. Debug log ditulis ke stderr (tidak mengganggu protokol).

```bash
# Test manual
echo '{"jsonrpc":"2.0","id":1,"method":"initialize","params":{"protocolVersion":"2024-11-05","capabilities":{},"clientInfo":{"name":"test","version":"1.0"}}}' | php shuriken mcp:serve
```

---

## Prasyarat

Site dan token harus sudah dikonfigurasi sebelum AI bisa memanggil tools yang membutuhkannya:

```bash
# Setup AI provider token
php shuriken token add groq {api_token}

# Setup deployment target
php shuriken site add mysite vercel {project_name} {api_token}
```

Lihat [token](token.md) dan [site](site.md) untuk detail setup.
