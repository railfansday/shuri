{!! $post->ai_content !!}
@if(!$post->ai_content)
@foreach($keyword->images->shuffle() as $image)
    @if($loop->first)
        <p>If you are {looking for|searching about} {{ $image->title }} you've {came|visit} to the right {place|web|page}. We have {{ $images->count() }} {Pics|Pictures|Images} about {{ $image->title }} like {{ $images->take(2)->pluck('title')->implode(', ') }} and also {{ $images->shuffle()->take(1)->pluck('title')->implode(', ') }}. {Read more|Here you go|Here it is}:</p>
    @endif

    <h2>{{ ucwords($image->title) }}</h2>

    <img alt="{{ $image->title }}" src="{{ $image->url }}" width="100%" onerror="this.onerror=null;this.src='{{ $image->thumbnail }}';" />

    <small>{{ $image->domain }}</small>
    <p>{{ $image->desc }}</p>
@endforeach

<p>{{ $keyword->sentences(3) }}</p>
@else
@foreach($keyword->images->shuffle() as $image)
    @if(!str_contains($post->ai_content, $image->url) && !str_contains($post->ai_content, $image->thumbnail))
    <h2>{{ ucwords($image->title) }}</h2>
    <img alt="{{ $image->title }}" src="{{ $image->url }}" width="100%" onerror="this.onerror=null;this.src='{{ $image->thumbnail }}';" />
    <p>{{ $image->desc }}</p>
    @endif
@endforeach
@endif
