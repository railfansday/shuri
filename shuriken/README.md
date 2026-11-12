# Shuriken

## 7.0.0
- Feat: Resume scraping — otomatis lanjut dari keyword terakhir berdasarkan images
- Feat: Bing Suggest aktif by default, matikan dengan `--no_suggest`
- Fix: Keywords tidak melebihi `--max` (sebelumnya bisa overshoot)
- Fix: `scraped_at` tidak lagi jadi penentu resume — diganti cek keberadaan images
- Feat: Statistik keywords/images/posts ditampilkan saat scraping selesai
- Perf: Index `images.keyword_id` untuk query resume lebih cepat

## 6.1.0
- Feat: Gemini AI

## Usage
```
php shuriken image:scrape --keywords="resepi" --max=1000
php shuriken image:scrape --keywords="resepi" --max=1000 --ai --provider=gemini
php shuriken image:scrape --keywords="resepi" --max=1000 --no_suggest
```
