@foreach($keyword->images->shuffle() as $image)
@if($loop->first)
If you are {looking for|searching about} {{ $image->title }} you've {came|visit} to the right {place|web|page}. We have {{ $images->count() }} {Pics|Pictures|Images} about {{ $image->title }} like {{ $images->take(2)->pluck('title')->implode(', ') }} and also {{ $images->shuffle()->take(1)->pluck('title')->implode(', ') }}. {Read more|Here you go|Here it is}:
@endif

## {{ ucwords($image->title) }}

![{{ $image->title }}]({{ $image->url }} "{{ $keyword->sentences(1) }}")

<small>{{ $image->domain }}</small>

{{ $keyword->sentences(2) }}
@endforeach

{{ $keyword->sentences(3) }}
