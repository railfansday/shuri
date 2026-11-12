@extends('export.html.' .  config('export.theme') . '.layout')

@section('head')
<title>{{ $post->title }}</title>
@endsection

@section('header')
<h1 style="font-size:26px;font-weight:700;margin-bottom:10px;">{{ $post->title }}</h1>
<div class="navi">
	{{ $post->keyword->sentences(3) }}<br>
	@foreach($post->related as $r)
		<a class="badge" style="background:#e91e8c;color:#fff;padding:3px 8px;border-radius:12px;margin:2px;" href="{{ $r->slug }}.html">{{ $r->keyword->name }}</a>
	@endforeach
</div>
@endsection

@section('content')
<div style="margin-top:20px;">
	{!! $post->content !!}
</div>
@endsection
