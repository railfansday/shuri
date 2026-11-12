@extends('export.html.' .  config('export.theme') . '.layout')

@section('head')
<title>{{ $post->title }}</title>
@endsection

@section('header')
<div class="article-header">
	<h1 class="article-title">{{ $post->title }}</h1>
	<div class="article-tags">
		@foreach($post->related as $r)
		<a href="{{ $r->slug }}.html" class="tag">{{ $r->keyword->name }}</a>
		@endforeach
	</div>
</div>
@endsection

@section('content')
<div class="article-body">
	{!! $post->content !!}
</div>
@endsection
