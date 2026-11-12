@extends('export.html.' .  config('export.theme') . '.layout')

@section('head')
<title>{{ $post->title }}</title>
@endsection

@section('bg')
{{ $post->keyword->images->random()->url }}
@endsection

@section('header')
<div class="posts-single">
	<h1>{{ $post->title }}</h1>

	<div class="navi text-left">
        <p>{{ $post->keyword->sentences(3) }} <br>
            @foreach($post->related as $r)
                <a class="badge badge-{{ collect(['primary', 'secondary', 'success', 'info', 'danger', 'warning', 'light', 'dark'])->random() }}" href="{{ $r->slug }}.html">{{ $r->keyword->name }}</a>
            @endforeach
        </p>
	</div>

@endsection

@section('content')
{!! $post->content !!}
@endsection
