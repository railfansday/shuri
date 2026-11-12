# Custom Content Templates

Shuriken menggunakan Blade templates untuk generate konten artikel. Selain template bawaan (`image`), kamu bisa membuat template sendiri untuk layout yang berbeda — gallery, listicle, tabel, atau format apapun.

---

## Cara Membuat Custom Template

### 1. Buat folder template baru

```bash
cp -r templates/image templates/nama-template
```

Ganti `nama-template` dengan nama bebas (huruf kecil, tanpa spasi).

### 2. Edit file yang dibutuhkan

Folder template berisi file-file berikut:

| File | Fungsi | Kolom DB |
|---|---|---|
| `content.blade.php` | Isi artikel | `post->content` |
| `title.blade.php` | Judul artikel | `post->title` |
| `category.blade.php` | Kategori | `post->category` |
| `tags.blade.php` | Tags | `post->tags` |
| `json_ld.blade.php` | Schema markup | `post->json_ld` |

Untuk custom layout, biasanya cukup edit `content.blade.php` dan `title.blade.php`. File lainnya bisa dibiarkan sama seperti template `image`.

### 3. Gunakan saat scraping

```bash
php shuriken image:scrape --keywords_txt --template=nama-template
```

---

## Variabel yang Tersedia

Semua variabel berikut bisa dipakai di dalam template:

| Variabel | Tipe | Keterangan |
|---|---|---|
| `$keyword` | `Keyword` | Keyword yang sedang diproses |
| `$keyword->name` | `string` | Nama keyword |
| `$keyword->images` | `Collection` | Semua gambar hasil scraping |
| `$keyword->sentences($n)` | `string` | N kalimat dari image desc/title |
| `$images` | `Collection` | Alias dari `$keyword->images` |
| `$post` | `Post` | Post yang sedang di-generate |
| `$post->ai_content` | `string` | Konten dari AI (kosong jika tidak pakai `--ai`) |
| `$post->ai_title` | `string` | Judul dari AI (kosong jika tidak pakai `--ai`) |
| `$post->title` | `string` | Judul artikel (diisi oleh `title.blade.php`) |
| `$post->published_at` | `Carbon` | Tanggal publikasi |

### Properti tiap image

```php
$image->url        // URL gambar full size
$image->thumbnail  // URL thumbnail
$image->title      // Judul gambar
$image->desc       // Deskripsi gambar
$image->domain     // Domain sumber
$image->width      // Lebar (pixel)
$image->height     // Tinggi (pixel)
```

---

## Spintax

Template mendukung spintax — teks dalam `{opsi1|opsi2|opsi3}` akan dipilih secara acak saat generate:

```blade
<p>Here {is|are} {a collection of|some of the best} {{ $keyword->name }} images.</p>
```

Hasilnya bisa: `"Here is a collection of aesthetic pfp images."` atau `"Here are some of the best aesthetic pfp images."`

---

## Contoh: Template Gallery

Cocok untuk niche visual seperti pfp, wallpaper, icon.

Simpan sebagai `templates/gallery/content.blade.php`:

```blade
<p>Here {is|are} {a collection of|some of the best} {{ $keyword->name }} {images|pictures|photos}
{you can use|available} {as|for} your {profile|wallpaper|background}.
We have {{ $images->count() }} {aesthetic|cool|unique} images {below|here}.</p>

<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:12px;margin:1.5rem 0">
@foreach($keyword->images->shuffle() as $image)
<div style="break-inside:avoid">
  <a href="{{ $image->url }}" target="_blank" rel="nofollow">
    <img alt="{{ $image->title ?: $keyword->name }}"
         src="{{ $image->url }}"
         width="100%"
         style="border-radius:8px;display:block;aspect-ratio:1;object-fit:cover"
         onerror="this.onerror=null;this.src='{{ $image->thumbnail }}';" />
  </a>
  @if($image->title)
  <small style="display:block;margin-top:4px;color:#666;font-size:.75rem">{{ $image->title }}</small>
  @endif
</div>
@endforeach
</div>

<p>{{ $keyword->sentences(2) }}</p>
```

Setup:

```bash
cp -r templates/image templates/gallery
# Ganti content.blade.php dengan kode di atas

php shuriken image:scrape --keywords_txt --template=gallery --image_count=9
```

---

## Contoh: Template Tabel Perbandingan

Cocok untuk niche review/spec produk.

Simpan sebagai `templates/compare/content.blade.php`:

```blade
<p>{Looking for|Searching for|Comparing} the best {{ $keyword->name }}?
Here {is|are} a {quick|detailed} comparison {to help you choose|overview}.</p>

<table style="width:100%;border-collapse:collapse;margin:1rem 0">
  <thead>
    <tr style="background:#1e1e2e;color:#fff">
      <th style="padding:.5rem .8rem;text-align:left">Image</th>
      <th style="padding:.5rem .8rem;text-align:left">Title</th>
      <th style="padding:.5rem .8rem;text-align:left">Source</th>
    </tr>
  </thead>
  <tbody>
@foreach($keyword->images as $image)
    <tr style="border-bottom:1px solid #e0e0e0">
      <td style="padding:.45rem .8rem">
        <img src="{{ $image->thumbnail }}" alt="{{ $image->title }}"
             style="width:80px;height:60px;object-fit:cover;border-radius:4px"
             onerror="this.onerror=null;this.src='{{ $image->url }}';" />
      </td>
      <td style="padding:.45rem .8rem">{{ $image->title }}</td>
      <td style="padding:.45rem .8rem"><small>{{ $image->domain }}</small></td>
    </tr>
@endforeach
  </tbody>
</table>

<p>{{ $keyword->sentences(3) }}</p>
```

---

## Tips

- **Jangan hapus `templates/image/`** — ini template default, dipakai kalau `--template` tidak diisi
- **Test dengan 1 keyword dulu** sebelum scraping massal: `--keywords="test keyword" --template=nama-template`
- **AI tetap bisa dikombinasikan** — kalau pakai `--ai`, `$post->ai_content` akan terisi dan bisa dipakai di template kustom sesuai kebutuhan
