# Konfigurasi

Semua konfigurasi Shuriken ada di folder `config/`. Nilai default bisa di-override via CLI option.

---

## config/ai.php

Konfigurasi untuk semua AI provider.

### `image_count`

Jumlah gambar yang disertakan dalam prompt AI (bukan jumlah gambar yang di-scrape).

```php
'image_count' => 2,
```

### `tones`

Daftar gaya penulisan yang dipilih secara acak per generate:

```php
'tones' => ['personal', 'professional', 'engaging', 'black', 'white', 'asian', 'funny'],
```

### Token Provider

Token **tidak perlu diedit di file ini** — gunakan command `token`:

```bash
php shuriken token add openai sk-proj-...
php shuriken token add groq gsk_...
php shuriken token list
```

Token disimpan di `storage/tokens.json` dan dimuat otomatis saat startup. Bisa tambah lebih dari satu token per provider — Shuriken akan round-robin otomatis. Lihat referensi lengkap: [token](commands/token.md).

### Model per Provider

Default model untuk setiap provider:

```php
'openai_options'      => ['model' => 'gpt-4.1-mini', 'max_tokens' => 1000],
'gemini_options'      => ['model' => 'gemini-2.5-flash'],
'claude_options'      => ['model' => 'claude-haiku-4-5', 'max_tokens' => 1024],
'deepseek_options'    => ['model' => 'deepseek-chat'],
'groq_options'        => ['model' => 'llama-3.3-70b-versatile'],
'openrouter_options'  => ['model' => 'google/gemini-2.5-flash'],
'kimi_options'        => ['model' => 'moonshot-v1-8k'],
'grok_options'        => ['model' => 'grok-2-1212'],
'mistral_options'     => ['model' => 'mistral-small-latest'],
'together_options'    => ['model' => 'meta-llama/Llama-3-8b-instruct'],
'perplexity_options'  => ['model' => 'llama-3.1-sonar-small-128k-online'],
'fireworks_options'   => ['model' => 'accounts/fireworks/models/llama-v3p3-70b-instruct'],
'cerebras_options'    => ['model' => 'llama-3.3-70b'],
'siliconflow_options' => ['model' => 'Qwen/Qwen2.5-7B-Instruct'],
'novita_options'      => ['model' => 'meta-llama/llama-3.3-70b-instruct'],
```

### Provider Lokal

Tidak butuh token:

```php
'ollama_options'  => ['model' => 'llama3.2', 'base_url' => 'http://localhost:11434/v1'],
'lmstudio_options' => ['model' => 'local-model', 'base_url' => 'http://localhost:1234/v1'],
```

### CLI Provider

```php
'claude_cli_options' => ['command' => 'claude'],
'gemini_cli_options' => ['command' => 'gemini'],
```

Untuk Windows atau custom path, ubah `command`-nya:
```php
'claude_cli_options' => ['command' => 'C:\\Users\\user\\AppData\\Roaming\\npm\\claude.cmd'],
```

---

## config/shuriken.php

Konfigurasi default untuk `image:scrape`.

```php
return [
    'language'         => 'en',       // Kode bahasa
    'country'          => 'us',       // Kode negara
    'proxy'            => '',          // URL proxy (kosong = tidak pakai)
    'max'              => 1000,        // Maksimal keyword
    'preset'           => 'https://www.bing.com/images/search?q=',
    'keywords_txt'     => false,       // Baca keyword dari keyword.txt
    'keywords'         => false,       // Keyword dari CLI
    'trends'           => false,       // Gunakan Google Trends
    'append_query'     => '',          // Query tambahan di URL Bing
    'image_count'      => 35,          // Jumlah gambar per keyword
    'execute_javascript' => false,     // Jalankan JS (untuk image_count > 9)
    'concurrency'      => 5,           // Proses paralel
    'requestDelay'     => 0,           // Delay antar request (ms)
    'timeout'          => 30,          // Timeout per scrape (detik)
    'ai'               => false,       // Aktifkan AI content generation
    'provider'         => 'openai',    // Provider AI default
    'memory_db'        => false,       // Gunakan in-memory SQLite
    'prompt'           => 'image',     // Template prompt (image/recipe/list/review/faq/tutorial/comparison — atau custom)
    'no_expand'        => false,       // Nonaktifkan keyword expansion
    'suggest'          => false,       // Tambah keyword via Bing Suggest
    'model'            => null,        // Override model AI (null = pakai default)
];
```

Semua key di sini bisa di-override via CLI option. Contoh:
```bash
# Override dari CLI
php shuriken image:scrape --language=id --country=id --concurrency=20
```

---

## config/export.php

Konfigurasi default untuk `image:export`.

```php
return [
    'platform'               => 'wordpress',   // Platform export default
    'category'               => 'Uncategorized',
    'start'                  => Carbon::now()->subYear(), // 1 tahun lalu
    'end'                    => Carbon::now(),             // Hari ini
    'chunk'                  => 50000,          // Maksimal post per file
    'theme'                  => 'default',      // Tema HTML
    'site_name'              => '',             // Nama website (HTML)
    'description'            => '',             // Deskripsi website (HTML)
    'clear_post'             => false,          // Hapus exports/ sebelum export
];
```

---

## config/prompts.php

Template prompt yang dikirim ke AI. Tujuh built-in template tersedia — plus custom prompt via [`prompt add`](commands/prompt.html).

| Nama | Keterangan |
|---|---|
| `image` | Blog post standar dengan gambar — **default** |
| `recipe` | Resep dengan bahan (`ul`) dan langkah (`ol`) |
| `list` | Listicle 8-10 item dengan h2 + p |
| `review` | Ulasan produk — Pros, Cons, Verdict |
| `faq` | Tanya-jawab — min. 8 pertanyaan |
| `tutorial` | How-to dengan langkah "Step N: title" |
| `comparison` | Perbandingan aspek + Recommendation |
| `news` | Artikel berita — lead paragraph + konteks + implikasi |
| `roundup` | "Best of" affiliate — item bernomor + key features + top pick |
| `guide` | Panduan komprehensif — multi-section, min. 500 kata |

### Variabel Template

| Variabel | Nilai |
|---|---|
| `{{images_json}}` | JSON array gambar (URL + judul) |
| `{{keyword}}` | Keyword yang sedang diproses |
| `{{tone}}` | Gaya penulisan (acak dari `ai.tones`) |
| `{{language}}` | Nilai dari config `shuriken.language` |
| `{{country}}` | Nilai dari config `shuriken.country` |

---

## Urutan Prioritas Konfigurasi

CLI option > config file > nilai default hardcoded.

Contoh: `--language=id` di CLI akan override `'language' => 'en'` di `config/shuriken.php`.
