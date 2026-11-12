# Fitur Tersembunyi & Perintah Ekstra

Di balik antarmuka yang terlihat *simple*, faktanya CLI Shuriken menyimpan beberapa instruksi *powerful* yang siap dimaksimalkan untuk skenario khusus Anda. Beberapa hal di bawah ini mungkin jarang terlihat oleh pengguna standar.

---

## 1. Pipeline Instan dengan `omakase` 🍣
Di dunia restoran Jepang, *Omakase* berarti "Saya serahkan semuanya pada Koki". Betul sekali, perintah `php shuriken omakase` adalah tombol ajaib di mana Shuriken akan mengeksekusi operasi otomatis penuh dari A-Z tanpa Anda harus mengetik perintah berkali-kali.

Perintah ini akan secara mandiri:
1. Menarik `keywords` yang ada dari database (atau Google Trends).
2. Mengeksekusi scraping gambar (`image:scrape`) secara berulang sampai stok kata kunci habis atau menyentuh volume ekstrim.
3. Menjalankan *expand/broaden keyword* organik sehingga *niche* Anda menyebar dan mencari ekor panjang (long-tail) target sendiri.
4. (Opsional) Langsung menyiapkan database yang siap *dipanen*.

Jika layar Terminal Anda mendadak banjir progress layaknya di film fiksi ilmiah usai memanggil `php shuriken omakase`, silakan langsung diseduh kopinya sembari menunggu keajaiban selesai.

---

# FAQ & Troubleshooting (Kendala Umum)

## Q1: Program Mengalami Error *"Database is locked"* (SQLite)
**Penyebab:** SQLite adalah database enteng berbasis file (bukan servis Server). Kalau *concurrency* (proses paralel) diatur terlalu tinggi secara brutal (misal `--concurrency=100`) sedangkan *harddisk/CPU* Anda berjalan kewalahan, file database tak akan sanggup menampung antrian save secara kilat.
**Solusi:**
1. Hentikan (CTRL+C) proses CLI. 
2. Turunkan argumen angka: `--concurrency=5` atau maksimal 10-15 saja jika laptop Anda bukan kelas i9/M3 Max.
3. Ketik perintah `php shuriken clear:data` (Awas ini mereset total seluruh database dari nol).

## Q2: Hasil Generasi AI Ngaco / Menceramahi (AI Slop)
**Keluhan:** "Kenapa tiap prompt hasil generate teksnya selalu dimulai dengan kalimat *'Tentu, saya akan membantu Anda membuat artikel ini...'* atau *'Berikut adalah HTML mentahnya...'*? Ini merusak struktur index web saya!"
**Penyebab:** Masalah kronis dari *Large Language Models (LLMs)* karena mereka diajari dengan gaya *chat-bot* sopan.
**Solusi Shuriken 7:**
Kabar gembira, di patch terbaru sistem Prompt Template Engine Shuriken 7 (_config/prompts.php_) kami telah menyuntikkan instruksi khusus **"CRITICAL: Output ONLY raw content"** secara global untuk membunuh fenomena ini! Jika ada AI yang membandel (biasanya model aneh tak dikenal), gantilah kembali provider ke model yang lebih patuh, misal: **Ollama Gemma 4**, **DeepSeek**, atau minimal **OpenAI GPT-4**.

## Q3: Error *"OpenAI::factory() does not exist"* atau Error pada Groq / Provider Lain
**Penyebab:** Versi library `openai-php/client` yang ada di paket lama tidak kompatibel dengan provider selain OpenAI (Groq, Mistral, dll).
**Solusi:** Jalankan perintah berikut di folder Shuriken:
```bash
composer update openai-php/client
```
Ini hanya memperbarui library AI client tanpa menyentuh dependency lainnya.

---

## Q4: Banyak "Deprecation Notice" saat `composer install` (Windows/Laragon)

**Gejala:** Muncul banyak pesan seperti:
```
Deprecated: Return type of ... should either be compatible with ...
Deprecation Notice: Using ${var} in strings is deprecated ...
```

**Penyebab:** Versi Composer yang terinstall di Laragon sudah lama dan tidak kompatibel dengan PHP 8.1+. Ini bukan error dari Shuriken.

**Solusi:** Update Composer ke versi terbaru:
```bash
composer self-update
```
Setelah update, jalankan ulang `composer install`. Deprecation notice akan hilang.

---

## Q5: Kuota API Cepat Ludes?
**Solusi:** 
Cukup ketik perintah CLI pendaftaran token sebanyak mungkin dari 1 layanan AI, misalnya:
`php shuriken token add groq token_pertama`
`php shuriken token add groq token_kedua_akun_adik`
`php shuriken token add groq token_ketiga`

Mesin Rotator Internal (`load-balancer`) dari Shuriken akan menggunakan fitur *Round-Robin*. Beban panggilan akan dioper layaknya sistem estafet gilir berganti guna menghindari deteksi batas *rate limit* IP/Akun!
