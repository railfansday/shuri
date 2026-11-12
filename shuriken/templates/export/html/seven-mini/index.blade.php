@extends('export.html.' .  config('export.theme') . '.layout')

@section('head')
<title>{{ config('export.site_name') }}</title>
@endsection

@section('content')
<div class="post-list">
	@foreach(collect($posts)->shuffle()->take(20) as $post)
	@php $image = $post->keyword->images->random()->toArray(); @endphp
	<div class="post-list-item">
		<div class="post-list-thumb">
			<a href="{{ $post->slug }}.html">
				<img src="{{ $image['thumbnail'] }}" alt="{{ $image['title'] }}" onerror="this.onerror=null;this.src='{{ $image['url'] }}';">
			</a>
		</div>
		<div class="post-list-body">
			<div class="post-list-title">
				<a href="{{ $post->slug }}.html">{{ $post->title }}</a>
			</div>
			<div class="post-list-excerpt">{{ $post->keyword->sentences(2) }}</div>
		</div>
	</div>
	@endforeach
</div>
@endsection
