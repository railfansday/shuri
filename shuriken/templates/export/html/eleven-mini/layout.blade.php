<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<link href="https://fonts.googleapis.com/css2?family=Lora:wght@400;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
	<style type="text/css">
*{box-sizing:border-box;margin:0;padding:0}
body,html{font-family:Lora,Georgia,serif;font-size:15px;background:#fff;color:#1a1a1a}
img{max-width:100%;display:block}
a{text-decoration:none;color:inherit}
.site-header{background:#fff;border-bottom:3px solid #c0392b;padding:12px 16px}
.site-header .inner{max-width:580px;margin:0 auto;display:flex;align-items:center;justify-content:space-between}
.site-header .logo{font-size:20px;font-weight:700;color:#c0392b;font-family:Lora,serif}
.site-header nav{font-family:Inter,sans-serif}
.site-header nav a{font-size:11px;color:#777;margin-left:12px}
.site-header nav a:hover{color:#c0392b}
.container{max-width:580px;margin:0 auto;padding:24px 16px}
.article-list{}
.article-item{display:flex;gap:12px;padding:14px 0;border-bottom:1px solid #eee;align-items:flex-start}
.article-item:last-child{border-bottom:none}
.article-thumb{flex:0 0 90px;height:60px;overflow:hidden;border-radius:3px}
.article-thumb img{width:100%;height:100%;object-fit:cover}
.article-info{flex:1}
.article-title{font-size:15px;font-weight:700;line-height:1.35;margin-bottom:5px}
.article-title a:hover{color:#c0392b}
.article-excerpt{font-size:12px;color:#777;line-height:1.5;font-family:Inter,sans-serif;display:-webkit-box;-webkit-line-clamp:1;-webkit-box-orient:vertical;overflow:hidden}
.post-headline{font-size:26px;font-weight:700;line-height:1.3;margin-bottom:12px;color:#1a1a1a}
.post-divider{height:3px;width:50px;background:#c0392b;margin-bottom:16px}
.post-meta{font-family:Inter,sans-serif;font-size:11px;color:#999;margin-bottom:16px;display:flex;flex-wrap:wrap;gap:5px}
.post-meta a{background:#fdf2f1;color:#c0392b;padding:2px 8px;border-radius:12px}
.post-meta a:hover{background:#c0392b;color:#fff}
.post-body{line-height:1.8;color:#2d2d2d;font-size:15px}
.post-body p{margin-bottom:16px}
.post-body h2,.post-body h3{font-family:Lora,serif;margin:22px 0 8px;color:#1a1a1a}
.site-footer{border-top:1px solid #eee;text-align:center;padding:16px;font-size:12px;color:#bbb;font-family:Inter,sans-serif;margin-top:24px}
.site-footer a{margin:0 6px;color:#ccc}
.site-footer a:hover{color:#c0392b}
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
