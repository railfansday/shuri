# AI Providers

Shuriken mendukung 18 AI provider untuk generate konten blog. Semua provider menggunakan format yang sama — cukup ganti `--provider` dan kelola token via CLI.

> **Cara mudah setup token:** Gunakan perintah [`token`](commands/token.html) untuk menambah, menghapus, dan test token langsung dari CLI tanpa perlu edit file config.
>
> ```bash
> php shuriken token add openai sk-proj-...
> php shuriken token test openai
> ```

## Daftar Provider

| Provider | Flag | Perlu Token | Tipe |
|---|---|---|---|
| OpenAI | `openai` | Ya | API |
| Gemini | `gemini` | Ya | API |
| Claude | `claude` | Ya | API |
| DeepSeek | `deepseek` | Ya | API (OpenAI-compatible) |
| Groq | `groq` | Ya | API (OpenAI-compatible) |
| OpenRouter | `openrouter` | Ya | API (OpenAI-compatible) |
| Kimi (Moonshot) | `kimi` | Ya | API (OpenAI-compatible) |
| Grok (xAI) | `grok` | Ya | API (OpenAI-compatible) |
| Mistral | `mistral` | Ya | API (OpenAI-compatible) |
| Together AI | `together` | Ya | API (OpenAI-compatible) |
| Perplexity | `perplexity` | Ya | API (OpenAI-compatible) |
| Fireworks AI | `fireworks` | Ya | API (OpenAI-compatible) |
| Cerebras | `cerebras` | Ya | API (OpenAI-compatible) |
| SiliconFlow | `siliconflow` | Ya | API (OpenAI-compatible) |
| Novita AI | `novita` | Ya | API (OpenAI-compatible) |
| Ollama | `ollama` | Tidak | Lokal |
| LM Studio | `lmstudio` | Tidak | Lokal |
| Claude CLI | `claude-cli` | Tidak | CLI (subscription) |
| Gemini CLI | `gemini-cli` | Tidak | CLI (subscription) |

---

## Setup per Provider

### OpenAI

```bash
# 1. Dapatkan API key di: platform.openai.com/api-keys
# 2. Tambahkan token:
php shuriken token add openai sk-proj-...

# Gunakan
php shuriken image:scrape --ai --provider=openai
php shuriken image:scrape --ai --provider=openai --model=gpt-4o
```

Model default: `gpt-4.1-mini`

---

### Gemini (Google)

```bash
# 1. Dapatkan API key di: aistudio.google.com/app/apikey
# 2. Tambahkan token:
php shuriken token add gemini AIzaSy...

# Gunakan
php shuriken image:scrape --ai --provider=gemini
```

Model default: `gemini-2.5-flash`

---

### Claude (Anthropic)

```bash
# 1. Dapatkan API key di: console.anthropic.com
# 2. Tambahkan token:
php shuriken token add claude sk-ant-...

# Gunakan
php shuriken image:scrape --ai --provider=claude
php shuriken image:scrape --ai --provider=claude --model=claude-sonnet-4-6
```

Model default: `claude-haiku-4-5`

---

### DeepSeek

```bash
# 1. Dapatkan API key di: platform.deepseek.com
# 2. Tambahkan token:
php shuriken token add deepseek sk-...

# Gunakan
php shuriken image:scrape --ai --provider=deepseek
```

Model default: `deepseek-chat`

---

### Groq

Ada free tier.

```bash
# 1. Dapatkan API key di: console.groq.com
# 2. Tambahkan token:
php shuriken token add groq gsk_...

# Gunakan
php shuriken image:scrape --ai --provider=groq
```

Model default: `llama-3.3-70b-versatile`

---

### OpenRouter

Akses 500+ model dengan 1 API key.

```bash
# 1. Dapatkan API key di: openrouter.ai/keys
# 2. Tambahkan token:
php shuriken token add openrouter sk-or-...

# Gunakan
php shuriken image:scrape --ai --provider=openrouter
php shuriken image:scrape --ai --provider=openrouter --model=anthropic/claude-3-haiku
```

Model default: `google/gemini-2.5-flash`

---

### Kimi (Moonshot AI)

```bash
# 1. Dapatkan API key di: platform.moonshot.cn
# 2. Tambahkan token:
php shuriken token add kimi sk-...

# Gunakan
php shuriken image:scrape --ai --provider=kimi
```

Model default: `moonshot-v1-8k`

---

### Grok (xAI)

```bash
# 1. Dapatkan API key di: console.x.ai
# 2. Tambahkan token:
php shuriken token add grok xai-...

# Gunakan
php shuriken image:scrape --ai --provider=grok
```

Model default: `grok-2-1212`

---

### Mistral

```bash
# 1. Dapatkan API key di: console.mistral.ai
# 2. Tambahkan token:
php shuriken token add mistral ...

# Gunakan
php shuriken image:scrape --ai --provider=mistral
```

Model default: `mistral-small-latest`

---

### Together AI

```bash
# 1. Dapatkan API key di: api.together.xyz
# 2. Tambahkan token:
php shuriken token add together ...

# Gunakan
php shuriken image:scrape --ai --provider=together
```

Model default: `meta-llama/Llama-3-8b-instruct`

---

### Perplexity

```bash
# 1. Dapatkan API key di: perplexity.ai/settings/api
# 2. Tambahkan token:
php shuriken token add perplexity pplx-...

# Gunakan
php shuriken image:scrape --ai --provider=perplexity
```

Model default: `llama-3.1-sonar-small-128k-online`

---

### Fireworks AI

Inferensi tercepat untuk open-source models (FireAttention engine).

```bash
# 1. Dapatkan API key di: fireworks.ai/account/api-keys
# 2. Tambahkan token:
php shuriken token add fireworks fw_...

# Gunakan
php shuriken image:scrape --ai --provider=fireworks
php shuriken image:scrape --ai --provider=fireworks --model=accounts/fireworks/models/mixtral-8x7b-instruct-hf
```

Model default: `accounts/fireworks/models/llama-v3p3-70b-instruct`

---

### Cerebras

Tercepat secara hardware — chip wafer-scale (~2000 tokens/detik). Ideal untuk job besar.

```bash
# 1. Dapatkan API key di: cloud.cerebras.ai
# 2. Tambahkan token:
php shuriken token add cerebras csk-...

# Gunakan
php shuriken image:scrape --ai --provider=cerebras
```

Model default: `llama-3.3-70b`

---

### SiliconFlow

Platform murah populer di Asia, free tier tersedia.

```bash
# 1. Dapatkan API key di: cloud.siliconflow.cn/account/ak
# 2. Tambahkan token:
php shuriken token add siliconflow sk-...

# Gunakan
php shuriken image:scrape --ai --provider=siliconflow
php shuriken image:scrape --ai --provider=siliconflow --model=deepseek-ai/DeepSeek-V3
```

Model default: `Qwen/Qwen2.5-7B-Instruct`

---

### Novita AI

200+ model tersedia, salah satu yang termurah.

```bash
# 1. Dapatkan API key di: novita.ai/settings/key-management
# 2. Tambahkan token:
php shuriken token add novita ...

# Gunakan
php shuriken image:scrape --ai --provider=novita
```

Model default: `meta-llama/llama-3.3-70b-instruct`

---

### Ollama (Lokal, Gratis)

Tidak perlu API key. Install Ollama dan jalankan model secara lokal. Sangat disarankan mencoba **Gemma 3** (misal: `gemma3:1b`) karena ringan dan cukup mumpuni untuk generate konten HTML.

> **💡 Tips untuk RAM Terbatas ("Laptop Kentang")**: Jika spesifikasi laptop pas-pasan (RAM 4GB - 8GB), gunakan model berukuran 1B - 2B parameters supaya tetap lancar dan tidak bikin ngelag. Rekomendasi model super ringan: `gemma3:1b`.

#### Install di macOS / Linux

```bash
curl -fsSL https://ollama.ai/install.sh | sh
ollama pull gemma3:1b

# Gunakan
php shuriken image:scrape --ai --provider=ollama --model=gemma3:1b
```

#### Install di Windows

Perintah `curl | sh` tidak jalan di Windows. Gunakan installer resmi:

1. Download installer di **ollama.com** → klik **Download for Windows**
2. Install seperti biasa (double click `.exe`)
3. Buka **Command Prompt baru** setelah install selesai
4. Jalankan:

```bash
ollama pull gemma3:1b
```

5. Lalu gunakan Shuriken seperti biasa:

```bash
php shuriken image:scrape --ai --provider=ollama --model=gemma3:1b
```

Model default: `llama3.2`

---

### LM Studio (Lokal, Gratis)

Tidak perlu API key. Download LM Studio, load model, aktifkan Local Server.

```bash
# Gunakan
php shuriken image:scrape --ai --provider=lmstudio
```

Download LM Studio di: lmstudio.ai · Model default: `local-model`

---

### Claude CLI (via Subscription)

Tidak perlu API key — pakai Claude Max/Pro subscription.

```bash
# 1. Install CLI (sekali saja)
npm install -g @anthropic-ai/claude-code

# 2. Login (sekali saja — ikuti proses di browser)
claude

# Gunakan
php shuriken image:scrape --ai --provider=claude-cli
```

---

### Gemini CLI (via Subscription)

Tidak perlu API key — pakai Google One AI Premium subscription.

```bash
# 1. Install CLI (sekali saja)
npm install -g @google/gemini-cli

# 2. Login (sekali saja — ikuti proses di browser)
gemini

# Gunakan
php shuriken image:scrape --ai --provider=gemini-cli
```

---

### Custom Provider (OpenAI-Compatible)

Punya server AI sendiri atau pakai layanan yang belum ada di daftar? Selama mendukung format OpenAI API, bisa langsung daftarkan sebagai custom provider.

```bash
# Daftarkan custom provider
php shuriken token add myserver sk-xxx --base_url=https://my-ai.com/v1 --model=my-model

# Gunakan
php shuriken image:scrape --ai --provider=myserver

# Cek daftar provider (custom ditandai [custom])
php shuriken token list
```

Custom provider otomatis masuk fallback rotation — kalau provider utama gagal, Shuriken akan mencoba custom provider berikutnya yang punya token aktif.

---

## Token Rotation (Multiple API Key)

Daftarkan beberapa token untuk mendistribusikan beban dan menghindari rate limit:

```php
'openai_tokens' => [
    'sk-proj-token1...',
    'sk-proj-token2...',
    'sk-proj-token3...',
],
```

Shuriken akan round-robin otomatis di antara token tersebut setiap request.

---

## Override Model via CLI

Gunakan `--model` untuk override model default tanpa edit config:

```bash
php shuriken image:scrape --ai --provider=openai --model=gpt-4o
php shuriken image:scrape --ai --provider=groq --model=mixtral-8x7b-32768
php shuriken image:scrape --ai --provider=openrouter --model=meta-llama/llama-3.1-70b-instruct
```

Jika `--model` tidak diset, digunakan default dari `config/ai.php`.
