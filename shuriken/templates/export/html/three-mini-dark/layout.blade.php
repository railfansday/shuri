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
.navbar,.posts-image h2 a,.posts-image-big h2 a,body,html{color:#f1f2f3}body,html{font-family:"Helvetica Neue",Helvetica,Arial,sans-serif;font-size:14px;font-weight:400;background:#111}img{max-width:100%}.header{padding-bottom:13px;margin-bottom:13px}.container{max-width:860px}.navbar{margin-bottom:25px;border-bottom:1px solid #222;background:#111!important}.navbar .navbar-brand{margin:0 20px 0 0;font-size:28px;padding:0;line-height:24px}.row.content,.row.footer,.row.header{widthx:auto;max-widthx:100%}.row.footer{padding:30px 0;background:0 0;border-color:#222}.content .col-sm-12{padding:0}.content .col-md-9s{margin-right:-25px}.posts-image{width:25%;display:block;float:left}.posts-image-content{width:auto;margin:0 15px 35px 0;position:relative}.posts-image:nth-child(5n) .posts-image-content{margin:0 0 35px!important}.posts-image img{width:100%;height:150px;object-fit:cover;object-position:center;margin-bottom:10px;-webkit-transition:.6s opacity;transition:.6s opacity}.posts-image:hover img{opacity:.8}.posts-image:hover h2{background:rgba(0,0,0,.7)}.posts-image h2{z-index:2;position:absolute;font-size:14px;bottom:2px;left:0;right:0;padding:5px;text-align:center;-webkit-transition:.6s opacity;transition:.6s background}.posts-image-big{display:block;width:100%}.posts-image-big .posts-image-content{margin:0 0 10px!important}.posts-image-big img{height:380px}.posts-image-big h2{text-align:left;padding-left:0;position:relative;font-size:30px;line-height:36px}.posts-image-big:hover h2{background:0 0}.posts-image-single .posts-image-content,.posts-image-single:nth-child(5n) .posts-image-content{margin:0 15px 15px 0!important}.posts-image-single p{line-height:18px!important;font-size:12px}.posts-images{clear:both}.list-group li{padding:0;background:#212121}.list-group li a{display:block;padding:8px}.widget{margin-bottom:20px}h3.widget-title{font-size:20px}a{color:#f42966;text-decoration:none}.footer{margin-top:21px;padding-top:13px;border-top:1px solid #eee}.footer a{margin:0 15px}.navi{margin:13px 0}.navi a{margin:5px 2px;font-size:95%}@media only screen and (min-width:0px) and (max-width:991px){.container{width:auto;max-width:100%}.navbar{padding:5px 0}.navbar .container{width:100%;margin:0 15px}}@media only screen and (min-width:0px) and (max-width:767px){.content .col-md-3{padding:15px}}@media only screen and (min-width:481px) and (max-width:640px){.posts-image img{height:90px}.posts-image-big img{height:320px}.posts-image-single{width:33.3%}.posts-image-single:nth-child(3n) .posts-image-content{margin-right:0!important}}@media only screen and (min-width:0px) and (max-width:480px){.posts-image img{height:80px}.posts-image-big img{height:240px}.posts-image-single{width:100%}.posts-image-single .posts-image-content{margin:0!important}.posts-image-single img{height:auto}}
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
				<div class="col-md-12">
					<div class="col-sm-12">

						@yield('header')
						@include('export.html._ads.related')

					</div>
					<div class="col-sm-12">
						@yield('content')
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
