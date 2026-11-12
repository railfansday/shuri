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
.posts-gallery,.posts-image,.posts-picture{display:block}.posts-gallery img,.posts-image img{object-fit:cover;object-position:center}body,html{font-family:"Source Sans Pro",sans-serif;font-size:14px;font-weight:400;background:#f1f2f3}img{max-width:100%}.container{max-width:1024px}.navbar{margin-bottom:25px;border-bottom:1px solid #eee;background:#0d9e90!important;-webkit-box-shadow:0 0 25px -8px rgba(0,0,0,.75);-moz-box-shadow:0 0 25px -8px rgba(0,0,0,.75);box-shadow:0 0 25px -8px rgba(0,0,0,.75)}.posts-image-content,.widget{-webkit-box-shadow:0 0 25px -8px rgba(0,0,0,.75);-moz-box-shadow:0 0 25px -8px rgba(0,0,0,.75)}.navbar .navbar-brand{margin:0 20px 0 0;font-size:28px;padding:0;line-height:24px}.row.content,.row.footer,.row.header{widthx:auto;max-widthx:100%}.row.footer{padding:30px 0;background:0 0;border-color:#eee}.content .col-sm-12{padding:0}.content .col-md-9s{margin-right:-25px}.posts-image{width:50%;float:left}.posts-image-content{width:auto;margin:0 15px 35px 0;position:relative;background:#fff;border:1px solid #eee;box-shadow:0 0 25px -8px rgba(0,0,0,.75);-webkit-border-radius:5px;-moz-border-radius:5px;border-radius:5px}.posts-image img{width:100%;height:250px;margin-bottom:5px;-webkit-transition:.6s opacity;transition:.6s opacity;-webkit-border-radius:5px 5px 0 0;-moz-border-radius:5px 5px 0 0;border-radius:5px 5px 0 0}.posts-single,.widget{border:1px solid #eee}.posts-image:hover img{opacity:.8}.posts-image:hovers h2{background:rgba(0,0,0,.7)}.posts-image h2{z-index:2;font-size:20px;padding:5px 10px;text-align:left;-webkit-transition:.6s opacity;transition:.6s background}.posts-picture,.posts-picture img{text-align:center}.posts-image h2 a{color:#111}.posts-image-single .posts-image-content,.posts-image-single:nth-child(5n) .posts-image-content{margin:0 15px 15px 0!important}.posts-images{clear:both}.widget{background:#fff;box-shadow:0 0 25px -8px rgba(0,0,0,.75);-webkit-border-radius:5px;-moz-border-radius:5px;border-radius:5px;margin-bottom:20px}.list-group{border:none}.list-group li{padding:0;border:none}.list-group li a{display:block;padding:8px}h3.widget-title{font-size:20px;padding:10px 10px 5px}a{color:#0d9e90;text-decoration:none}.footer{margin-top:21px;padding-top:13px;border-top:1px solid #eee}.footer a{margin:0 15px}.navi{margin:13px 0}.navi a{margin:5px 2px;font-size:95%}.posts-single{background:#fff;padding:15px;-webkit-box-shadow:0 0 25px -8px rgba(0,0,0,.75);-moz-box-shadow:0 0 25px -8px rgba(0,0,0,.75);box-shadow:0 0 25px -8px rgba(0,0,0,.75);-webkit-border-radius:5px 5px 0 0;-moz-border-radius:5px 5px 0 0;border-radius:5px 5px 0 0}.posts-singles{border:1px solid #eee;background:#fff;margin-top:-10px;border-top:0;padding:0 15px 15px;-webkit-box-shadow:0 8px 31px -9px rgba(0,0,0,.75);-moz-box-shadow:0 8px 31px -9px rgba(0,0,0,.75);box-shadow:0 8px 31px -9px rgba(0,0,0,.75);-webkit-border-radius:0 0 5px 5px;-moz-border-radius:0 0 5px 5px;border-radius:0 0 5px 5px}.posts-gallery{width:20%;float:left}.posts-gallery-content{border:1px solid #fff;width:auto;max-width:100%}.posts-gallery img{width:100%;height:110px}@media only screen and (min-width:0px) and (max-width:991px){.container{width:auto;max-width:100%}.navbar{padding:5px 0}.navbar .container{width:100%;margin:0 15px}}@media only screen and (min-width:0px) and (max-width:767px){.content .col-md-3{padding:15px}}@media only screen and (min-width:481px) and (max-width:640px){.posts-gallery{width:33.3%}}@media only screen and (min-width:0px) and (max-width:480px){.posts-image{width:100%}.posts-image .posts-image-content{margin-right:0}.posts-gallery{width:50%}}
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
			<ul class="navbar-nav ml-auto">
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
						@foreach(App\Post::inRandomOrder()->take(10)->get()->chunk(3) as $chunked)
								@foreach($chunked as $post)
									<li class="list-group-item"><a href="{{ $post->slug }}.html">{{ $post->title }}</a></li>
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
