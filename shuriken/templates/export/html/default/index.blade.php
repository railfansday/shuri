@extends('export.html.' .  config('export.theme') . '.layout')

@section('content')
<section>
    @foreach($posts as $post)
        <aside>
            @php
            $image = $post->keyword->images->random()->toArray();
            @endphp
            <a href="{{ $image['url'] }}" target="_blank"><img alt="{{ $image['title'] }}" src="{{ $image['thumbnail'] }}" width="100%" onerror="this.onerror=null;this.src='{{ $image['url'] }}';"></a>
            <h3><a href="{{ $post->slug }}.html">{{ $post->title }}</a></h3>
        </aside>
    @endforeach
</section>
@endsection
