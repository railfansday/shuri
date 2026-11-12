# token

Kelola token API untuk semua AI provider — tambah, hapus, lihat daftar, dan test koneksi langsung dari CLI.

Token disimpan di `storage/tokens.json` dan otomatis dimuat saat aplikasi berjalan.

---

## Subcommand

### `token list`

Tampilkan semua token yang sudah dikonfigurasi (dimask untuk keamanan).

```bash
php shuriken token list
```

Output contoh:
```
AI Token List
──────────────────────────────────────────────────
  openai       2 token(s)
    [0] sk-proj***
    [1] sk-proj***
  gemini       1 token(s)
    [0] AIzaSy***
  claude       no tokens
```

---

### `token add`

Tambahkan token baru untuk provider tertentu.

```bash
php shuriken token add {provider} {token}
```

Contoh:
```bash
php shuriken token add openai sk-proj-abc123
php shuriken token add gemini AIzaSyXXXXXXXX
php shuriken token add claude sk-ant-XXXXXXXXX
php shuriken token add deepseek sk-XXXXXXXXX
```

Beberapa token bisa ditambahkan untuk satu provider — Shuriken akan merotasinya secara round-robin saat scraping.

---

### `token remove`

Hapus token dari provider.

```bash
php shuriken token remove {provider} {token}
```

Contoh:
```bash
php shuriken token remove openai sk-proj-abc123
```

---

### `token test`

Test koneksi ke provider dengan melakukan API call nyata. Berguna untuk memastikan token valid sebelum mulai scraping.

```bash
# Test token tertentu
php shuriken token test {provider} {token}

# Test token pertama yang sudah disimpan
php shuriken token test {provider}
```

Contoh:
```bash
php shuriken token test openai sk-proj-abc123
php shuriken token test gemini
```

Output sukses:
```
Testing openai with token sk-proj***...
✓ Success (342ms): OK
```

Output gagal:
```
Testing openai with token sk-proj***...
✗ Failed (120ms): Incorrect API key provided
```

---

## Provider yang Didukung

| Provider | Nama |
|---|---|
| OpenAI | `openai` |
| Google Gemini | `gemini` |
| Anthropic Claude | `claude` |
| DeepSeek | `deepseek` |
| Groq | `groq` |
| OpenRouter | `openrouter` |
| Kimi (Moonshot) | `kimi` |
| Grok (xAI) | `grok` |
| Mistral | `mistral` |
| Together AI | `together` |
| Perplexity | `perplexity` |
| Fireworks AI | `fireworks` |
| Cerebras | `cerebras` |
| SiliconFlow | `siliconflow` |
| Novita AI | `novita` |

Provider lokal (Ollama, LM Studio) dan CLI (claude-cli, gemini-cli) tidak butuh token.

---

## File `storage/tokens.json`

Token disimpan dalam format JSON:

```json
{
  "openai": [
    "sk-proj-abc123",
    "sk-proj-xyz456"
  ],
  "gemini": [
    "AIzaSyXXXXXXXX"
  ]
}
```

File ini bisa diedit langsung, atau dikelola via perintah `token add/remove`.

---

## Prioritas Token

Token dari `storage/tokens.json` **diprioritaskan** di atas nilai default di `config/ai.php`. Ini memungkinkan konfigurasi token tanpa mengubah file config.

---

## Auto-fallback Provider

Kalau satu provider gagal (rate limit, quota habis, error jaringan) setelah beberapa kali retry, Shuriken otomatis mencoba provider berikutnya yang punya token — scraping tidak berhenti.

```
  -> GetContentFromGroqAI (Token: sk-gro***)
  ⚠ Provider groq failed: Rate limit exceeded
  → Trying next provider...
  -> GetContentFromGeminiAI (Token: AIzaSy***)
  ✓ Content generated
```

Urutan fallback mengikuti prioritas yang sama dengan auto-select. Semakin banyak provider yang dikonfigurasi, semakin tangguh scraping berjalan.

```bash
# Setup multi-provider untuk ketahanan maksimal
php shuriken token add groq sk-...       # cepat & gratis
php shuriken token add gemini AIza...    # fallback 1
php shuriken token add openai sk-...     # fallback 2
```

---

## Auto-select Provider

Setelah token ditambahkan, `--ai` otomatis memilih provider yang tersedia — tidak perlu selalu menulis `--provider`:

```bash
php shuriken token add gemini AIzaSy...

# Provider dipilih otomatis (gemini dalam contoh ini)
php shuriken image:scrape --ai --keywords="resep"
# Output: → No provider specified. Auto-selected: gemini
```

Urutan prioritas auto-select: `openai → gemini → claude → groq → deepseek → openrouter → kimi → grok → mistral → together → perplexity → ollama → lmstudio → claude-cli → gemini-cli`

---

## Rotasi Token Otomatis

Jika satu provider punya beberapa token, Shuriken akan merotasinya secara round-robin otomatis. Berguna untuk menghindari rate limit.

```bash
php shuriken token add openai sk-token-1
php shuriken token add openai sk-token-2
php shuriken token add openai sk-token-3

# Saat scraping, ketiga token dipakai bergantian
php shuriken image:scrape --ai --provider=openai --keywords="resep"
```
