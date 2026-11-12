<!doctype html>
<html lang="en">
<head>
	<!-- Required meta tags -->
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<!-- Bootstrap CSS -->
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
	<link href="https://fonts.googleapis.com/css?family=Raleway" rel="stylesheet">
	<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
	<style type="text/css">
.containers,img{max-width:100%}body,html{font-family:Raleway,sans-serif;font-size:14px;font-weight:400;color:#f1f2f3;background:#111}.header{padding-bottom:13px;margin-bottom:13px}.containers{margin:0 auto;padding:0;background-color:#fff}.navbar{margin-bottom:25px;background:#111!important;color:#f1f2f3;border-bottom:1px solid #212121}.navbar li:hover{background:#222}.navbar .navbar-brand{margin:0 20px 0 0;font-size:28px;padding:0;line-height:24px;max-width:70%}.row.content,.row.footer,.row.header{widthx:auto;max-widthx:100%}.row.footer{border-color:#212121;padding:30px 0;background:0 0}.content .col-sm-12{padding:0}.content .col-md-9s{margin-right:-25px}.content .col-md-3{padding-left:0}.posts-image{width:33%;display:inline-table}.posts-image-content{width:auto;margin:0 20px 25px 0;position:relative}.posts-image:nth-childs(3n) .posts-image-content{margin:0 0 25px!important}.posts-image img{width:100%;height:150px;object-fit:cover;object-position:center;margin-bottom:10px;-webkit-transition:.6s ease;transition:.6s ease}.posts-image:hover img{-webkit-transform:scale(1.08);transform:scale(1.08)}.list-group li{padding:0;background:#121212}.list-group li a{display:block;padding:8px}.widget{margin-bottom:20px}h3.widget-title{font-size:20px}a{color:#f42966;text-decoration:none}.footer{margin-top:21px;padding-top:13px;border-top:1px solid #eee}.footer a{margin:0 15px}.navi{margin:13px 0}.navi a{margin:5px 2px;font-size:95%}@media only screen and (min-width:0px) and (max-width:991px){.container{width:auto;max-width:100%}.navbar{padding:5px 0}.navbar .container{width:100%;margin:0 15px}}@media only screen and (min-width:0px) and (max-width:767px){.content .col-md-3{padding:15px}.posts-image{width:33%}.posts-image-content{margin:0 16px 25px 0}.posts-image:nth-child(3n) .posts-image-content{margin:0 0 25px}}@media only screen and (min-width:0px) and (max-width:640px){.posts-image{width:49.5%}.posts-image-content,.posts-image:nth-child(3n) .posts-image-content{margin:0 8px 25px 0}.posts-image:nth-child(2n) .posts-image-content{margin:0 0 25px 8px!important}}@media only screen and (min-width:0px) and (max-width:360px){.posts-image{width:100%}.posts-image-content,.posts-image:nth-child(2n) .posts-image-content,.posts-image:nth-child(3n) .posts-image-content{margin:0 0 25px!important}.posts-image-content img{height:auto}}
	</style>
	@yield('head')
	@include('export.html._ads.header')
</head>
<body>
	<main id="main">
		<nav class="navbar navbar-expand-lg navbar-dark bg-light ">
			<div class="container">
		  <a href="index.html" class="navbar-brand">{{ config('export.site_name') }}</a>
		  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
			<span class="navbar-toggler-icon"></span>
		  </button>
		  <div class="collapse navbar-collapse" id="navbarSupportedContent">
			<ul class="navbar-nav mr-auto">
				@foreach($pages as $page)
				<li class="nav-item"><a class="nav-link" href="{{ $page }}.html">{{ ucwords(str_replace('-', ' ', $page)) }}</a></li>
				@endforeach
			</ul>
			</div>
		  </div>
		</nav>
		<div class="container">
			<div class="row content">
				<div class="col-md-9">
					<div class="col-sm-12">

						@yield('header')
						@include('export.html._ads.related')

					</div>
					<div class="col-sm-12">
						@yield('content')
					</div>
				</div>
				<div class="col-md-3">
					<div class="col-sm-12 widget">
						<h3 class="widget-title">Random Posts</h3>
						<ul class="list-group">
						@foreach(\App\Post::whereNotNull('content')->inRandomOrder()->take(10)->get()->chunk(3) as $chunked)
								@foreach($chunked as $post)
									<li class="list-group-item"><a href="{{ $post->slug . '.html' }}">{{ ucwords($post->title) }}</a></li>
								@endforeach
						@endforeach
						</ul>
					</div>
					<div class="col-sm-12 widget">
						<!-- ads -->
					</div>
				</div>
			</div>
			<div class="row footer">
				<div class="col-md-12 text-center">
					@foreach($pages as $page)
					<a href="{{ $page }}.html">{{ ucwords(str_replace('-', ' ', $page)) }}</a>
					@endforeach

				</div>
			</div>
		</div>
		@include('export.html._ads.bar')
		@include('export.html._ads.footer')
	</main>
</body>
</html>
