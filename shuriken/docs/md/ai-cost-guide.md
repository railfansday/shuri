# Panduan Biaya AI

Untuk produksi konten massal dengan biaya serendah mungkin, pilihan provider sangat menentukan. Berikut breakdown lengkapnya.

## Perbandingan Biaya (per 1 juta token)

> Harga perkiraan per Maret 2026. Cek halaman resmi masing-masing provider untuk harga terkini.

| Provider | Model Default | Input | Output | Estimasi per 1.000 artikel |
|---|---|---|---|---|
| **Ollama** | llama3.2 | Gratis | Gratis | Gratis |
| **Groq** | llama-3.3-70b-versatile | Gratis* | Gratis* | Gratis |
| **claude-cli** | (via subscription) | ~$20/bln flat | — | Gratis per artikel |
| **gemini-cli** | (via subscription) | ~$20/bln flat | — | Gratis per artikel |
| **Together AI** | Llama-3-8b-instruct | ~$0.10 | ~$0.10 | ~$0.30–$0.50 |
| **Mistral** | mistral-small-latest | ~$0.20 | ~$0.60 | ~$1–$2 |
| **Gemini** | gemini-2.5-flash | ~$0.10 | ~$0.40 | ~$0.80–$1.50 |
| **DeepSeek** | deepseek-chat | ~$0.28 | ~$0.42 | ~$1–$2 |
| **Groq (bayar)** | llama-3.3-70b | ~$0.59 | ~$0.79 | ~$1–$2 |
| **OpenRouter** | gemini-2.5-flash | ~$0.10 | ~$0.40 | ~$0.80–$1.50 |
| **Kimi** | moonshot-v1-8k | ~$0.20 | ~$2.00 | ~$2–$5 |
| **OpenAI** | gpt-4.1-mini | ~$0.40 | ~$1.60 | ~$2–$5 |
| **Perplexity** | sonar | ~$1.00 | ~$1.00 | ~$3–$5 |
| **Claude** | claude-haiku-4-5 | ~$1.00 | ~$5.00 | ~$6–$12 |
| **Grok** | grok-2-1212 | ~$2.00 | ~$10.00 | ~$15–$25 |

*Groq free tier ada rate limit (harian/per menit). Cukup untuk volume sedang.

---

## Tier Rekomendasi

### Tier 1 — Gratis (Budget: $0)

**Terbaik untuk: Eksperimen, development, volume kecil-menengah**

#### Ollama (Lokal)
- Install sekali, pakai selamanya tanpa biaya
- **Rekomendasi Utama**: Model **Gemma 4** (`gemma4:2b`) terbukti paling pintar dalam menulis dan menjaga format HTML meskipun dijalankan di laptop berspesifikasi pas-pasan ("laptop kentang").
- Model `llama3.2` (1B/3B) sangat kencang untuk artikel singkat
- Model `mistral` atau `llama3.1:8b` untuk kualitas maksimal jika komputer punya GPU/RAM memadai
- **Limitasi**: Kecepatan murni tergantung pada spesifikasi hardware Anda.

```bash
ollama pull gemma4:2b
php shuriken image:scrape --ai --provider=ollama --model=gemma4:2b --concurrency=3
```

#### Groq Free Tier
- API gratis dengan rate limit harian
- Model llama-3.3-70b-versatile — kualitas sangat baik untuk gratis
- **Limitasi**: ~14.400 token/menit, ~500.000 token/hari (cek docs Groq)

```bash
php shuriken image:scrape --ai --provider=groq --concurrency=5
```

#### Claude CLI / Gemini CLI
- Pakai subscription yang sudah ada ($20/bulan)
- Tidak ada biaya per token — cost flat
- Kualitas tertinggi (Claude) atau sangat baik (Gemini)
- **Limitasi**: Ada usage limit per hari, tidak cocok untuk volume sangat tinggi

Install dan login CLI terlebih dahulu (sekali saja):

```bash
# Claude CLI — butuh Claude Max/Pro subscription
npm install -g @anthropic-ai/claude-code
claude  # ikuti proses login di browser
```

```bash
# Gemini CLI — butuh Google One AI Premium subscription
npm install -g @google/gemini-cli
gemini  # ikuti proses login di browser
```

```bash
php shuriken image:scrape --ai --provider=claude-cli
php shuriken image:scrape --ai --provider=gemini-cli
```

Lihat [AI Providers](ai-providers.md) untuk detail konfigurasi.

---

### Tier 2 — Murah (Budget: <$5 per 1.000 artikel)

**Terbaik untuk: Produksi massal, konten berkualitas cukup**

#### DeepSeek (Paling Direkomendasikan)
- Harga termurah di antara API berbayar
- Kualitas sangat baik untuk harganya
- Model `deepseek-chat` sudah cukup untuk artikel blog

```bash
php shuriken image:scrape --ai --provider=deepseek --concurrency=15
```

#### Gemini Flash (via Gemini API)
- Harga sangat kompetitif
- Kecepatan tinggi, cocok untuk volume besar
- Google infrastructure — uptime tinggi

```bash
php shuriken image:scrape --ai --provider=gemini --concurrency=10
```

#### Mistral Small
- Alternatif Eropa yang GDPR-compliant
- Kualitas solid, harga bersaing

```bash
php shuriken image:scrape --ai --provider=mistral --concurrency=10
```

---

### Tier 3 — Premium (Budget: Tidak terbatas)

**Terbaik untuk: Konten berkualitas tinggi, niche kompetitif**

#### Claude Haiku (via API)
- Kualitas tulisan terbaik
- Cocok untuk niche yang butuh konten sangat natural

```bash
php shuriken image:scrape --ai --provider=claude --model=claude-haiku-4-5
```

#### OpenAI GPT-4.1-mini
- Standar industri, kualitas konsisten
- Ekosistem terlengkap

```bash
php shuriken image:scrape --ai --provider=openai
```

---

## Strategi Produksi Massal

### Strategi A: Zero Cost (Gratis Total)

Gunakan Groq untuk produksi + Ollama sebagai fallback:

```bash
# Produksi utama dengan Groq (gratis, cepat)
php shuriken image:scrape \
    --trends=id \
    --max=500 \
    --ai \
    --provider=groq \
    --concurrency=10 \
    --memory_db

# Jika rate limit Groq tercapai, beralih ke Ollama
php shuriken image:scrape \
    --trends=id \
    --max=500 \
    --ai \
    --provider=ollama \
    --concurrency=3
```

### Strategi B: Budget Minimal (<$10/bulan)

DeepSeek untuk bulk, Claude CLI untuk konten premium:

```bash
# Artikel massal dengan DeepSeek
php shuriken image:scrape --trends=id --max=1000 --ai --provider=deepseek

# Artikel niche/kompetitif dengan Claude CLI (pakai subscription)
php shuriken image:scrape --keywords="keyword premium" --ai --provider=claude-cli
```

### Strategi C: Kualitas Maksimal

Gunakan OpenRouter untuk akses ke banyak model dengan 1 key:

```bash
# Model murah untuk draft
php shuriken image:scrape --ai --provider=openrouter \
    --model=google/gemini-2.5-flash

# Model premium untuk niche kompetitif
php shuriken image:scrape --ai --provider=openrouter \
    --model=anthropic/claude-3-5-haiku
```

---

## Tips Hemat Biaya

1. **Pakai `--memory_db`** untuk scraping cepat — kurangi overhead I/O, proses lebih efisien
2. **Pakai `--concurrency` tinggi dengan Groq** — Groq sangat cepat (ratusan token/detik)
3. **Rotasi multiple token** — daftarkan beberapa API key via `php shuriken token add` untuk menghindari rate limit tanpa harus upgrade plan
4. **Pakai prompt `list`** — lebih sedikit token digunakan vs prompt `image`
5. **Batasi `--image_count`** — semakin sedikit gambar, semakin pendek prompt, semakin hemat token

```bash
# Paling hemat token
php shuriken image:scrape --ai --provider=deepseek --prompt=list --image_count=5
```

---

## Kesimpulan Rekomendasi

| Kebutuhan | Provider Terbaik |
|---|---|
| Gratis, volume kecil | Groq free tier |
| Gratis, unlimited | Ollama (lokal) |
| Punya Claude/Gemini subscription | claude-cli / gemini-cli |
| Murah, volume besar | DeepSeek |
| Kualitas bagus, harga wajar | Gemini Flash |
| Kualitas terbaik | Claude Haiku via API |
| 1 key untuk banyak model | OpenRouter |
