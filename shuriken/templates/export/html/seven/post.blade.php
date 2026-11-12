@extends('export.html.' .  config('export.theme') . '.layout')

@section('head')
<title>{{ $post->title }}</title>
@endsection

@section('header')
<h1 style="font-size:26px;font-weight:700;margin-bottom:10px;line-height:1.3;">{{ $post->title }}</h1>
<div class="navi" style="color:#888;font-size:13px;margin-bottom:20px;">
	{{ $post->keyword->sentences(2) }}<br>
	<div style="margin-top:6px;">
	@foreach($post->related as $r)
		<a href="{{ $r->slug }}.html" style="color:#1a73e8;margin-right:10px;font-size:12px;">{{ $r->keyword->name }}</a>
	@endforeach
	</div>
</div>
@endsection

@section('content')
{!! $post->content !!}
@endsection
