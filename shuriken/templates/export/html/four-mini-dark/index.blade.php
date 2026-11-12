@extends('export.html.' .  config('export.theme') . '.layout')

@section('head')
<title>{{ config('export.site_name') }}</title>
@endsection

@section('bg')
@endsection

@section('content')
	@if(isset($_GET['img']))
	<div class="row">
		<div class="col-md-12 text-center">
			<p><a href="{{ $_GET['img'] }}" class="btn btn-outline-primary" download="image">Download Image</a></p>
			<p><img src="{{ $_GET['img'] }}" alt="" class="img-fluid"></p>

		</div>
	</div>
	<div class="mt-3"></div>
	@endif

		@foreach(\App\Post::whereNotNull('content')->inRandomOrder()->take(18)->get()->chunk(3) as $chunked)
				<div class="clearfix"></div>
				@foreach($chunked as $n => $post)

					<div class="posts-image">
						<div class="posts-image-content">
							<a href="{{ $post->slug . '.html' }}">
								<img src="{{ $post->keyword->images->random()->url }}" alt="{{ $post->title }}" class="img-fluid" onerror="this.onerror=null;this.src='{{ $post->keyword->images->random()->thumbnail }}';">
							</a>
							<h2><a href="{{ $post->slug . '.html' }}">
								{{ $post->title }}
							</a></h2>
						</div>
					</div>
				@endforeach
		@endforeach

@endsection
