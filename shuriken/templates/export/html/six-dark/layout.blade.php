<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
	<link href="https://fonts.googleapis.com/css?family=Merriweather:400,700|Source+Sans+Pro:400,600" rel="stylesheet">
	<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
	<style type="text/css">
*{box-sizing:border-box}
body,html{font-family:"Source Sans Pro",sans-serif;font-size:15px;background:#141414;color:#ddd}
img{max-width:100%}
a{text-decoration:none;color:#e74c3c}
a:hover{opacity:.85}
.navbar{background:#1a1a1a!important;padding:10px 0;margin-bottom:0;border-bottom:2px solid #e74c3c}
.navbar-brand{font-family:Merriweather,serif;font-size:22px;color:#fff!important;font-weight:700}
.navbar .nav-link{color:rgba(255,255,255,.75)!important;font-size:13px}
.hero{position:relative;margin-bottom:30px;background:#000;overflow:hidden}
.hero img{width:100%;height:460px;object-fit:cover;opacity:.7;display:block}
.hero-caption{position:absolute;bottom:0;left:0;right:0;padding:30px;background:linear-gradient(transparent,rgba(0,0,0,.85))}
.hero-caption h2{font-family:Merriweather,serif;font-size:28px;color:#fff;margin:0;line-height:1.3}
.hero-caption a{color:#fff}
.section-title{font-family:Merriweather,serif;font-size:18px;border-bottom:3px solid #e74c3c;padding-bottom:8px;margin-bottom:20px;color:#eee}
.card{border:none;border-radius:4px;overflow:hidden;box-shadow:0 1px 6px rgba(0,0,0,.4);margin-bottom:20px;background:#1e1e1e}
.card img{width:100%;height:180px;object-fit:cover}
.card-body{padding:12px 14px}
.card-title{font-size:14px;font-weight:600;margin:0;line-height:1.4}
.card-title a{color:#ddd}
.card-title a:hover{color:#e74c3c}
.widget{background:#1e1e1e;border-radius:4px;padding:16px;box-shadow:0 1px 4px rgba(0,0,0,.3);margin-bottom:20px}
.widget h5{font-family:Merriweather,serif;font-size:15px;border-bottom:2px solid #e74c3c;padding-bottom:6px;margin-bottom:12px;color:#eee}
.widget ul{list-style:none;padding:0;margin:0}
.widget ul li{padding:6px 0;border-bottom:1px solid #2a2a2a;font-size:13px}
.widget ul li a{color:#bbb}
.site-footer{background:#0d0d0d;color:#666;text-align:center;padding:24px 0;margin-top:40px;font-size:13px}
.site-footer a{color:#888;margin:0 12px}
.navi{margin:10px 0}
.navi a{font-size:13px;margin:3px 4px;display:inline-block}
@media(max-width:768px){.hero img{height:260px}.hero-caption h2{font-size:18px}}
	</style>
	@yield('head')
	@include('export.html._ads.header')
</head>
<body>
	<nav class="navbar navbar-expand-lg navbar-dark">
		<div class="container">
			<a href="index.html" class="navbar-brand">{{ config('export.site_name') }}</a>
			<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#nav">
				<span class="navbar-toggler-icon"></span>
			</button>
			<div class="collapse navbar-collapse" id="nav">
				<ul class="navbar-nav ml-auto">
					@foreach($pages as $page)
					<li class="nav-item"><a class="nav-link" href="{{ $page }}.html">{{ ucwords(str_replace('-', ' ', $page)) }}</a></li>
					@endforeach
				</ul>
			</div>
		</div>
	</nav>
	<div class="container" style="margin-top:24px;">
		@yield('header')
		@include('export.html._ads.related')
		<div class="row">
			<div class="col-md-9">
				@yield('content')
			</div>
			<div class="col-md-3">
				<div class="widget">
					<h5>Recent Posts</h5>
					<ul>
						@foreach(\App\Post::whereNotNull('content')->inRandomOrder()->take(10)->get() as $p)
						<li><a href="{{ $p->slug }}.html">{{ $p->title }}</a></li>
						@endforeach
					</ul>
				</div>
			</div>
		</div>
	</div>
	<footer class="site-footer">
		@foreach($pages as $page)
		<a href="{{ $page }}.html">{{ ucwords(str_replace('-', ' ', $page)) }}</a>
		@endforeach
	</footer>
	@include('export.html._ads.bar')
	@include('export.html._ads.footer')
</body>
</html>
