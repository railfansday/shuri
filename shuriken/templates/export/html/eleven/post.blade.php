@extends('export.html.' .  config('export.theme') . '.layout')

@section('head')
<title>{{ $post->title }}</title>
@endsection

@section('header')
<h1 class="post-headline">{{ $post->title }}</h1>
<div class="post-divider"></div>
<div class="post-meta">
	@foreach($post->related as $r)
	<a href="{{ $r->slug }}.html">{{ $r->keyword->name }}</a>
	@endforeach
</div>
@endsection

@section('content')
<div class="post-body">
	{!! $post->content !!}
</div>
@endsection
