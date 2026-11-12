## v7.0.49 — 2026-06-10
- release: v7.0.49

## v7.0.48 — 2026-06-08
- `remove local providers from fallback provider list`

## v7.0.47 — 2026-06-03
- fix: delete existing tag before re-tagging on re-release

## v7.0.47 — 2026-06-03
- release: v7.0.47

## v7.0.47 — 2026-06-03
- fix: update CHANGELOG.md

## v7.0.46 — 2026-05-19
- **Fix:** Gambar hasil scrape tidak relevan dengan keyword — ditambahkan parameter `mkt` (contoh: `mkt=en-US`) ke URL Bing berdasarkan config `--language` dan `--country`, sehingga hasil tidak lagi dipengaruhi geolokasi IP server
- **Fix:** Spider kini mengirim browser User-Agent, Accept-Language, dan Accept header pada setiap request ke Bing — sebelumnya pakai Guzzle default UA yang terdeteksi sebagai bot

## v7.0.45 — 2026-05-01
- Update domain references from dojo.cx to dojo.cc

## v7.0.44 — 2026-04-17
- Add HtmlExporter unit tests and update composer autoload

## v7.0.43 — 2026-04-14
- Ensure exports directory exists before export starts

## v7.0.42 — 2026-04-14
- Add in-article ad slot injected after 2nd paragraph in HTML export

## v7.0.41 — 2026-04-14
- Suppress deprecated PHP warnings in bootstrap

## v7.0.40 — 2026-04-14
- Add ai_title field to posts with exporter and zip fixes

## v7.0.39 — 2026-04-09
- Update FAQ and rebuild docs

## v7.0.38 — 2026-04-08
- Add printable case study and rebuild docs

## v7.0.37 — 2026-04-08
- Use AI to generate release commit message via git diff stat

## v7.0.36 — 2026-04-08


## v7.0.35 — 2026-04-08


## v7.0.34 — 2026-04-08


## v7.0.33 — 2026-04-08


## v7.0.32 — 2026-04-08


## v7.0.31 — 2026-04-08


## v7.0.30 — 2026-04-08


## v7.0.29 — 2026-04-08


## v7.0.28 — 2026-04-08


## v7.0.27 — 2026-04-08


## v7.0.26 — 2026-04-08


## v7.0.25 — 2026-04-08


## v7.0.24 — 2026-04-08


## v7.0.23 — 2026-04-08


## v7.0.22 — 2026-04-08


## v7.0.21 — 2026-04-08


## v7.0.20 — 2026-04-08


## v7.0.19 — 2026-04-08


## v7.0.18 — 2026-04-08


## v7.0.17 — 2026-04-08


## v7.0.16 — 2026-04-08


## v7.0.15 — 2026-04-08


## v7.0.14 — 2026-04-08


## v7.0.13 — 2026-04-08


## v7.0.12 — 2026-04-08


## v7.0.11 — 2026-04-08


## v7.0.10 — 2026-04-08


## v7.0.9 — 2026-04-07


## v7.0.8 — 2026-04-07


## v7.0.7 — 2026-04-07


## v7.0.6 — 2026-04-07


## v7.0.5 — 2026-04-07


## v7.0.4 — 2026-04-07
- feat: Auto-derive sitemap base_url from site config via --site flag in image:export
- feat: Add --base_url option to image:export for correct sitemap URLs

## v7.0.3 — 2026-04-07
- feat: Support custom OpenAI-compatible providers via token add --base_url

## v7.0.2 — 2026-04-07


## v1.0.0 — 2026-04-07
- feat: Auto-increment patch version in release command if no version given
- feat: Add git tagging to release workflow
- feat: Add release command with auto-generated changelog from git log
- fix: Upgrade openai-php/client to fix OpenAI::factory() error on Groq and other compatible providers

