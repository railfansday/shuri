<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<link href="https://fonts.googleapis.com/css?family=Nunito:400,700" rel="stylesheet">
	<style type="text/css">
*{box-sizing:border-box;margin:0;padding:0}
body,html{font-family:Nunito,sans-serif;font-size:14px;background:#f5f5f5;color:#222}
img{max-width:100%;display:block}
a{color:#e91e8c;text-decoration:none}
a:hover{opacity:.8}
.site-header{background:#fff;border-bottom:2px solid #e91e8c;padding:18px 0;text-align:center;margin-bottom:24px}
.site-header a{font-size:32px;font-weight:700;color:#222;letter-spacing:-1px}
.site-header p{font-size:11px;letter-spacing:6px;color:#999;margin-top:4px}
.container{max-width:1200px;margin:0 auto;padding:0 20px}
.masonry{columns:4 250px;column-gap:16px}
.masonry-item{break-inside:avoid;margin-bottom:16px;background:#fff;border-radius:6px;overflow:hidden;box-shadow:0 1px 4px rgba(0,0,0,.1);transition:box-shadow .2s}
.masonry-item:hover{box-shadow:0 4px 16px rgba(0,0,0,.18)}
.masonry-item img{width:100%;height:auto}
.masonry-item .caption{padding:10px 12px;font-size:13px;color:#444}
.masonry-item .caption a{color:#222;font-weight:700}
.masonry-item .caption a:hover{color:#e91e8c}
.site-footer{text-align:center;padding:30px 0;margin-top:40px;border-top:1px solid #ddd;font-size:13px}
.site-footer a{margin:0 12px;color:#666}
.navi{margin:13px 0}
.navi a{margin:4px 3px;font-size:93%;display:inline-block}
@media(max-width:600px){.masonry{columns:2 150px}}
	</style>
	@yield('head')
	@include('export.html._ads.header')
</head>
<body>
	<header class="site-header">
		<div class="container">
			<a href="index.html">{{ config('export.site_name') }}</a>
			<p>Inspiration Gallery</p>
		</div>
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
