<p>Here {is|are} {a collection of|some of} the best {{ $keyword->name }} {images|pictures|photos} {you can use|available} {as|for} your profile picture. {Browse|Check out|Explore} our {collection|gallery} of {{ $images->count() }} {aesthetic|cool|unique} {pfp|profile pictures|images} {below|here}.</p>

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
