@extends('export.html.' .  config('export.theme') . '.layout')

@section('head')
<title>{{ config('export.site_name') }}</title>
@endsection

@section('content')
<div class="grid">
	@foreach(collect($posts)->shuffle()->take(24) as $post)
	@php $image = $post->keyword->images->random()->toArray(); @endphp
	<div class="grid-item">
		<a href="{{ $post->slug }}.html">
			<img src="{{ $image['url'] }}" alt="{{ $image['title'] }}" onerror="this.onerror=null;this.src='{{ $image['thumbnail'] }}';">
			<div class="overlay">
				<div class="overlay-title">{{ $post->title }}</div>
			</div>
		</a>
	</div>
	@endforeach
</div>
@endsection
