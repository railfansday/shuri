@extends('export.html.' .  config('export.theme') . '.layout')

@section('head')
<title>{{ config('export.site_name') }}</title>
@endsection

@section('content')
<div class="masonry">
	@foreach(collect($posts)->shuffle()->take(24) as $post)
		@php $image = $post->keyword->images->random()->toArray(); @endphp
		<div class="masonry-item">
			<a href="{{ $post->slug }}.html">
				<img alt="{{ $image['title'] }}" src="{{ $image['thumbnail'] }}" onerror="this.onerror=null;this.src='{{ $image['url'] }}';">
			</a>
			<div class="caption">
				<a href="{{ $post->slug }}.html">{{ $post->title }}</a>
			</div>
		</div>
	@endforeach
</div>
@endsection
