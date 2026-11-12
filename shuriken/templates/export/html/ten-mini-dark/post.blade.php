@extends('export.html.' .  config('export.theme') . '.layout')

@section('head')
<title>{{ $post->title }}</title>
@endsection

@section('content')
<div class="post-content-wrap">
	@php $image = $post->keyword->images->random()->toArray(); @endphp
	<img src="{{ $image['url'] }}" alt="{{ $image['title'] }}" class="hero-image" onerror="this.onerror=null;this.src='{{ $image['thumbnail'] }}';">
	<h1>{{ $post->title }}</h1>
	<div class="post-tags">
		@foreach($post->related as $r)
		<a href="{{ $r->slug }}.html">{{ $r->keyword->name }}</a>
		@endforeach
	</div>
	<div class="post-body">
		{!! $post->content !!}
	</div>
</div>
@endsection
