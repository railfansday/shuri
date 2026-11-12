<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<link href="https://fonts.googleapis.com/css?family=Poppins:400,600,700" rel="stylesheet">
	<style type="text/css">
*{box-sizing:border-box;margin:0;padding:0}
body,html{font-family:Poppins,sans-serif;font-size:14px;background:#fff;color:#111}
img{max-width:100%;display:block}
a{text-decoration:none;color:inherit}
.site-header{background:#fff;border-bottom:3px solid #ff6b35;padding:12px 16px}
.site-header .inner{max-width:900px;margin:0 auto;display:flex;align-items:center;justify-content:space-between}
.site-header .logo{font-size:20px;font-weight:700;color:#ff6b35}
.site-header nav a{font-size:11px;color:#888;margin-left:12px;text-transform:uppercase;letter-spacing:1px}
.site-header nav a:hover{color:#ff6b35}
.container{max-width:900px;margin:0 auto;padding:20px 16px}
.tile-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:4px}
.tile{position:relative;overflow:hidden;aspect-ratio:4/3}
.tile img{width:100%;height:100%;object-fit:cover;transition:transform .4s ease}
.tile:hover img{transform:scale(1.07)}
.tile-overlay{position:absolute;inset:0;background:rgba(0,0,0,.55);opacity:0;transition:opacity .3s;display:flex;align-items:flex-end;padding:14px}
.tile:hover .tile-overlay{opacity:1}
.tile-title{font-size:13px;font-weight:600;color:#fff;line-height:1.4}
.tile-accent{position:absolute;top:0;left:0;right:0;height:3px;background:#ff6b35;transform:scaleX(0);transform-origin:left;transition:transform .3s}
.tile:hover .tile-accent{transform:scaleX(1)}
.hero-image{width:100%;max-height:360px;object-fit:cover;border-radius:4px;margin-bottom:20px}
.post-content-wrap{max-width:100%;margin:0 auto}
.post-content-wrap h1{font-size:22px;font-weight:700;margin-bottom:14px;line-height:1.3}
.post-tags{display:flex;flex-wrap:wrap;gap:5px;margin-bottom:16px}
.post-tags a{font-size:11px;background:#fff3ee;color:#ff6b35;padding:2px 9px;border-radius:12px;border:1px solid #ffd0bc}
.post-tags a:hover{background:#ff6b35;color:#fff}
.post-body{line-height:1.7;color:#333;font-size:14px}
.post-body p{margin-bottom:12px}
.site-footer{text-align:center;padding:16px;border-top:1px solid #eee;font-size:12px;color:#bbb;margin-top:20px}
.site-footer a{margin:0 6px;color:#ccc}
.site-footer a:hover{color:#ff6b35}
@media(max-width:600px){.tile-grid{grid-template-columns:1fr}}
	</style>
	@yield('head')
	@include('export.html._ads.header')
</head>
<body>
	<header class="site-header">
		<div class="inner">
			<a href="index.html" class="logo">{{ config('export.site_name') }}</a>
			<nav>
				@foreach($pages as $page)
				<a href="{{ $page }}.html">{{ ucwords(str_replace('-', ' ', $page)) }}</a>
				@endforeach
			</nav>
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
