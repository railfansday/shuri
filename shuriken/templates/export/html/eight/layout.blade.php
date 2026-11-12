<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<link href="https://fonts.googleapis.com/css?family=Poppins:400,600,700" rel="stylesheet">
	<style type="text/css">
*{box-sizing:border-box;margin:0;padding:0}
body,html{font-family:Poppins,sans-serif;font-size:14px;background:#111;color:#fff}
img{max-width:100%;display:block}
a{text-decoration:none;color:inherit}
.site-header{text-align:center;padding:24px 20px 16px;border-bottom:1px solid #222}
.site-header a{font-size:26px;font-weight:700;color:#fff;letter-spacing:-0.5px}
.site-header nav{margin-top:10px}
.site-header nav a{font-size:11px;letter-spacing:2px;color:#888;margin:0 10px;text-transform:uppercase}
.container{max-width:1280px;margin:0 auto;padding:24px 16px}
.grid{display:grid;grid-template-columns:repeat(4,1fr);gap:6px}
.grid-item{position:relative;overflow:hidden;aspect-ratio:1/1}
.grid-item img{width:100%;height:100%;object-fit:cover;transition:transform .4s ease}
.grid-item:hover img{transform:scale(1.06)}
.grid-item .overlay{position:absolute;bottom:0;left:0;right:0;padding:14px 12px 10px;background:linear-gradient(transparent,rgba(0,0,0,.75));opacity:0;transition:opacity .3s}
.grid-item:hover .overlay{opacity:1}
.grid-item .overlay-title{font-size:13px;font-weight:600;color:#fff;line-height:1.3}
.site-footer{text-align:center;padding:20px 0;border-top:1px solid #222;font-size:12px;color:#555;margin-top:16px}
.site-footer a{margin:0 10px;color:#666}
.navi{margin:12px 0}
.navi a{margin:3px 4px;font-size:12px;color:#aaa}
@media(max-width:900px){.grid{grid-template-columns:repeat(3,1fr)}}
@media(max-width:600px){.grid{grid-template-columns:repeat(2,1fr)}}
	</style>
	@yield('head')
	@include('export.html._ads.header')
</head>
<body>
	<header class="site-header">
		<a href="index.html">{{ config('export.site_name') }}</a>
		<nav>
			@foreach($pages as $page)
			<a href="{{ $page }}.html">{{ ucwords(str_replace('-', ' ', $page)) }}</a>
			@endforeach
		</nav>
	</header>
	<div class="container">
		@yield('header')
		@include('export.html._ads.related')
		@yield('content')
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
