@extends('export.html.' .  config('export.theme') . '.layout')

@section('head')
<title>{{ $post->title }}</title>
@endsection

@section('header')
<div style="margin-bottom:20px;">
	<h1 style="font-size:24px;font-weight:700;margin-bottom:10px;">{{ $post->title }}</h1>
	<div class="navi" style="color:#888;font-size:12px;">
		{{ $post->keyword->sentences(2) }}<br>
		<div style="margin-top:8px;">
		@foreach($post->related as $r)
			<a href="{{ $r->slug }}.html" style="background:#333;color:#ddd;padding:2px 10px;border-radius:10px;font-size:11px;margin:2px;display:inline-block;">{{ $r->keyword->name }}</a>
		@endforeach
		</div>
	</div>
</div>
@endsection

@section('content')
<div style="color:#ccc;line-height:1.7;">
	{!! $post->content !!}
</div>
@endsection
