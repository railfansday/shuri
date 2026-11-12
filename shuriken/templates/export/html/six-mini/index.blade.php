@extends('export.html.' .  config('export.theme') . '.layout')

@section('head')
<title>{{ config('export.site_name') }}</title>
@endsection

@section('content')
@php $shuffled = collect($posts)->shuffle(); @endphp

{{-- Hero: first post --}}
@if($shuffled->count() > 0)
@php $hero = $shuffled->first(); $heroImg = $hero->keyword->images->random()->toArray(); @endphp
<div class="hero mb-4">
	<a href="{{ $hero->slug }}.html">
		<img src="{{ $heroImg['url'] }}" alt="{{ $heroImg['title'] }}" onerror="this.onerror=null;this.src='{{ $heroImg['thumbnail'] }}';">
		<div class="hero-caption">
			<h2><a href="{{ $hero->slug }}.html">{{ $hero->title }}</a></h2>
		</div>
	</a>
</div>
@endif

{{-- Grid: remaining posts --}}
<h4 class="section-title">Latest</h4>
<div class="row">
	@foreach($shuffled->skip(1)->take(15) as $post)
	@php $image = $post->keyword->images->random()->toArray(); @endphp
	<div class="col-sm-6 col-md-4">
		<div class="card">
			<a href="{{ $post->slug }}.html">
				<img src="{{ $image['thumbnail'] }}" alt="{{ $image['title'] }}" onerror="this.onerror=null;this.src='{{ $image['url'] }}';">
			</a>
			<div class="card-body">
				<p class="card-title"><a href="{{ $post->slug }}.html">{{ $post->title }}</a></p>
			</div>
		</div>
	</div>
	@endforeach
</div>
@endsection
