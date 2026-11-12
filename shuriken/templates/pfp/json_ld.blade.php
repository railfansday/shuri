@php
    $name = Faker\Factory::create()->name;
@endphp
<script type="application/ld+json">
{
  "@context": "https://schema.org/",
  "@type": "Article",
  "author": {
    "@type": "Person",
    "name": "{{ $name }}"
  },
  "headline": "{{ $post->title }}",
  "datePublished": "{{ $post->published_at->format('Y-m-d') }}",
  "image": "{{ $post->keyword->images->shuffle()->first()->url }}",
  "publisher": {
    "@type": "Organization",
    "name": "{{ $name }}",
    "logo": {
      "@type": "ImageObject",
      "url": "https://via.placeholder.com/512.png?text={{ $name[0] }}",
      "width": 512,
      "height": 512
    }
  }
}
</script>
