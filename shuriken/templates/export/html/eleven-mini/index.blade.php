@extends('export.html.' .  config('export.theme') . '.layout')

@section('head')
<title>{{ config('export.site_name') }}</title>
@endsection

@section('content')
<div class="article-list">
	@foreach(collect($posts)->shuffle()->take(20) as $post)
	@php $image = $post->keyword->images->random()->toArray(); @endphp
	<div class="article-item">
		<a href="{{ $post->slug }}.html" class="article-thumb">
			<img src="{{ $image['url'] }}" alt="{{ $image['title'] }}" onerror="this.onerror=null;this.src='{{ $image['thumbnail'] }}';">
		</a>
		<div class="article-info">
			<div class="article-title"><a href="{{ $post->slug }}.html">{{ $post->title }}</a></div>
			<div class="article-excerpt">{{ $post->keyword->sentences(1) }}</div>
		</div>
	</div>
	@endforeach
</div>
@endsection
