# HTML Themes

Shuriken menyediakan 45 tema untuk export HTML. Setiap tema menghasilkan website statis yang siap upload.

## Cara Pakai

```bash
php shuriken image:export --platform=html --theme=five-dark
php shuriken image:export --platform=html --theme=random
php shuriken image:export --platform=html --theme=eight-mini --site_name="Foto Blog"
```

## Opsi Tambahan

| Option | Default | Keterangan |
|---|---|---|
| `--theme` | `default` | Nama tema |
| `--site_name` | Keyword pertama | Nama website |
| `--description` | Judul post pertama | Deskripsi website |

## Tema Random

```bash
php shuriken image:export --platform=html --theme=random
```

Setiap kali dijalankan, tema dipilih secara acak dari 45 tema yang tersedia. Cocok untuk menghasilkan variasi website.

---

## Keluarga Tema

### Default

![Preview tema Default](img/themes/default.png)

| Tema | Deskripsi |
|---|---|
| `default` | Tema dasar — grid sederhana, warna putih |

---

### One — Kartu Bertumpuk

![Preview tema One](img/themes/one.png)

Layout kartu (card grid) dengan gambar penuh dan teks di bawahnya. Font: Georgia (serif). Cocok untuk blog foto atau travel.

| Tema | Background | Teks |
|---|---|---|
| `one` | Putih | Gelap |
| `one-dark` | Abu gelap (#1a1a1a) | Terang |
| `one-mini` | Putih | Gelap, kompak |
| `one-mini-dark` | Abu gelap | Terang, kompak |

---

### Two — Minimal Grid

![Preview tema Two](img/themes/two.png)

Grid bersih dengan hover efek sederhana. Font: system-ui. Gaya modern dan minimalis.

| Tema | Background | Teks |
|---|---|---|
| `two` | Putih | Gelap |
| `two-dark` | Hitam (#111) | Terang |
| `two-mini` | Putih | Gelap, kompak |
| `two-mini-dark` | Hitam | Terang, kompak |

---

### Three — Bootstrap Grid

![Preview tema Three](img/themes/three.png)

Menggunakan Bootstrap 4. Kartu dengan shadow, rounded corners. Gaya yang familiar dan profesional.

| Tema | Background | Teks |
|---|---|---|
| `three` | Abu muda (#f8f9fa) | Gelap |
| `three-dark` | Gelap | Terang |
| `three-mini` | Abu muda | Gelap, kompak |
| `three-mini-dark` | Gelap | Terang, kompak |

---

### Four — Pinterest Style

![Preview tema Four](img/themes/four.png)

Grid masonry dengan kolom berbeda tinggi (CSS columns). Font: Nunito. Cocok untuk galeri foto beragam ukuran.

| Tema | Background | Teks |
|---|---|---|
| `four` | Putih | Gelap |
| `four-dark` | Hitam (#0d0d0d) | Terang |
| `four-mini` | Putih | Gelap, kompak |
| `four-mini-dark` | Hitam | Terang, kompak |

---

### Five — Masonry Modern

![Preview tema Five](img/themes/five.png)

Masonry layout menggunakan CSS columns dengan gap bersih. Font: Nunito. Tanpa sidebar — konten penuh.

| Tema | Background | Teks |
|---|---|---|
| `five` | Putih | Gelap |
| `five-dark` | Abu sangat gelap (#111) | Terang |
| `five-mini` | Putih | Gelap, kompak |
| `five-mini-dark` | Abu sangat gelap | Terang, kompak |

---

### Six — Magazine

![Preview tema Six](img/themes/six.png)

Layout majalah: post pertama tampil besar sebagai hero, diikuti grid + sidebar. Font: Merriweather (serif). Aksen merah.

| Tema | Background | Teks |
|---|---|---|
| `six` | Putih | Gelap, serif |
| `six-dark` | Hitam (#0a0a0a) | Terang, serif |
| `six-mini` | Putih | Gelap, kompak |
| `six-mini-dark` | Hitam | Terang, kompak |

---

### Seven — List Horizontal

![Preview tema Seven](img/themes/seven.png)

Layout daftar: thumbnail di kiri, teks di kanan. Font: Inter. Bersih dan mudah dibaca. Mirip artikel berita.

| Tema | Background | Teks |
|---|---|---|
| `seven` | Putih (#fafafa) | Gelap |
| `seven-dark` | Hitam (#111) | Terang |
| `seven-mini` | Putih | Gelap, kompak |
| `seven-mini-dark` | Hitam | Terang, kompak |

---

### Eight — Overlay Dark

![Preview tema Eight](img/themes/eight.png)

CSS Grid dengan rasio 1:1 (kotak). Saat hover, judul muncul dari bawah dengan efek overlay gradient. Font: Poppins. Gaya Instagram/galeri modern.

| Tema | Background | Teks |
|---|---|---|
| `eight` | Hitam (#111) | Putih (overlay) |
| `eight-dark` | Hitam pekat (#000) | Putih (overlay) |
| `eight-mini` | Hitam (#111) | Putih, 3 kolom |
| `eight-mini-dark` | Hitam pekat (#000) | Putih, 3 kolom |

---

### Nine — Sidebar Blog

![Preview tema Nine](img/themes/nine.png)

Layout 2 kolom: sidebar kiri (daftar 10 post terbaru) + konten utama di kanan. Font: Georgia (serif) + Arial (UI). Gaya blog personal yang rapi.

| Tema | Background | Aksen |
|---|---|---|
| `nine` | Abu muda (#f8f9fa) | Biru (#2c7be5) |
| `nine-dark` | Gelap (#0f1117) | Biru terang (#5b9cf6) |
| `nine-mini` | Abu muda, 800px | Biru (#2c7be5) |
| `nine-mini-dark` | Gelap, 800px | Biru terang (#5b9cf6) |

---

### Ten — Tiled Gallery

![Preview tema Ten](img/themes/ten.png)

Grid tiles besar 3 kolom dengan rasio 4:3. Hover menampilkan judul via overlay. Image-first, minimal text. Font: Poppins. Aksen oranye.

| Tema | Background | Aksen |
|---|---|---|
| `ten` | Putih | Oranye (#ff6b35) |
| `ten-dark` | Hitam (#111) | Oranye (#ff8c5a) |
| `ten-mini` | Putih, 900px, 2 kolom | Oranye (#ff6b35) |
| `ten-mini-dark` | Hitam, 900px, 2 kolom | Oranye (#ff8c5a) |

---

### Eleven — News Article

![Preview tema Eleven](img/themes/eleven.png)

Halaman artikel bergaya media online. Kolom sempit (760px) untuk readability. Font: Lora (serif) + Inter. Aksen merah. Index: daftar artikel dengan thumbnail kecil.

| Tema | Background | Aksen |
|---|---|---|
| `eleven` | Putih | Merah (#c0392b) |
| `eleven-dark` | Navy gelap (#1a1a2e) | Merah (#e74c3c) |
| `eleven-mini` | Putih, 580px | Merah (#c0392b) |
| `eleven-mini-dark` | Navy gelap, 580px | Merah (#e74c3c) |

---

## Perbedaan Regular vs Mini

| Varian | Lebar Max | Kolom | Cocok untuk |
|---|---|---|---|
| Regular | 1280px | 4 kolom | Desktop |
| Mini | 900px | 3 kolom | Mobile-first, compact |

Semua tema responsive — otomatis menyesuaikan di layar kecil.

---

## Konten yang Dihasilkan per Export

Setiap ZIP hasil export berisi:
- `index.html` — Halaman utama dengan daftar semua post
- `{slug}.html` — Satu file per post
- `sitemap.xml` — Sitemap untuk SEO
- `contact.html`, `copyright.html`, `dmca.html`, `privacy-policy.html` — Halaman statis

---

## Daftar Lengkap 45 Tema

```
default
eight        eight-dark        eight-mini        eight-mini-dark
eleven       eleven-dark       eleven-mini       eleven-mini-dark
five         five-dark         five-mini         five-mini-dark
four         four-dark         four-mini         four-mini-dark
nine         nine-dark         nine-mini         nine-mini-dark
one          one-dark          one-mini          one-mini-dark
seven        seven-dark        seven-mini        seven-mini-dark
six          six-dark          six-mini          six-mini-dark
ten          ten-dark          ten-mini          ten-mini-dark
three        three-dark        three-mini        three-mini-dark
two          two-dark          two-mini          two-mini-dark
```
