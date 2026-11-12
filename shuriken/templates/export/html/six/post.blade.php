@extends('export.html.' .  config('export.theme') . '.layout')

@section('head')
<title>{{ $post->title }}</title>
@endsection

@section('content')
<h1 style="font-family:Merriweather,serif;font-size:26px;margin-bottom:16px;">{{ $post->title }}</h1>
<div class="navi" style="margin-bottom:20px;color:#888;font-size:13px;">
	{{ $post->keyword->sentences(2) }}
	<div style="margin-top:8px;">
	@foreach($post->related as $r)
		<a href="{{ $r->slug }}.html" style="background:#c0392b;color:#fff;padding:2px 10px;border-radius:10px;font-size:12px;margin:2px;display:inline-block;">{{ $r->keyword->name }}</a>
	@endforeach
	</div>
</div>
{!! $post->content !!}
@endsection
