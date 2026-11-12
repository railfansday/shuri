<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<link href="https://fonts.googleapis.com/css2?family=Lora:wght@400;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
	<style type="text/css">
*{box-sizing:border-box;margin:0;padding:0}
body,html{font-family:Lora,Georgia,serif;font-size:16px;background:#1a1a2e;color:#e8e8f0}
img{max-width:100%;display:block}
a{text-decoration:none;color:inherit}
.site-header{background:#16213e;border-bottom:3px solid #e74c3c;padding:16px 20px}
.site-header .inner{max-width:760px;margin:0 auto;display:flex;align-items:center;justify-content:space-between}
.site-header .logo{font-size:22px;font-weight:700;color:#e74c3c;font-family:Lora,serif}
.site-header nav{font-family:Inter,sans-serif}
.site-header nav a{font-size:12px;color:#7a8099;margin-left:14px}
.site-header nav a:hover{color:#e74c3c}
.container{max-width:760px;margin:0 auto;padding:28px 16px}
.article-list{}
.article-item{display:flex;gap:16px;padding:16px 0;border-bottom:1px solid #2d3561;align-items:flex-start}
.article-item:last-child{border-bottom:none}
.article-thumb{flex:0 0 120px;height:80px;overflow:hidden;border-radius:3px}
.article-thumb img{width:100%;height:100%;object-fit:cover}
.article-info{flex:1}
.article-title{font-size:17px;font-weight:700;line-height:1.35;margin-bottom:6px;color:#e8e8f0}
.article-title a:hover{color:#e74c3c}
.article-excerpt{font-size:13px;color:#6b7280;line-height:1.5;font-family:Inter,sans-serif;display:-webkit-box;-webkit-line-clamp:1;-webkit-box-orient:vertical;overflow:hidden}
.post-headline{font-size:30px;font-weight:700;line-height:1.3;margin-bottom:14px;color:#e8e8f0}
.post-divider{height:3px;width:60px;background:#e74c3c;margin-bottom:20px}
.post-meta{font-family:Inter,sans-serif;font-size:12px;color:#6b7280;margin-bottom:20px;display:flex;flex-wrap:wrap;gap:6px}
.post-meta a{background:#2d1a1a;color:#e74c3c;padding:2px 9px;border-radius:12px}
.post-meta a:hover{background:#e74c3c;color:#fff}
.post-body{line-height:1.8;color:#c8c8d8;font-size:16px}
.post-body p{margin-bottom:18px}
.post-body h2,.post-body h3{font-family:Lora,serif;margin:28px 0 10px;color:#e8e8f0}
.site-footer{border-top:1px solid #2d3561;text-align:center;padding:20px;font-size:12px;color:#3d4466;font-family:Inter,sans-serif;margin-top:32px}
.site-footer a{margin:0 8px;color:#4a5580}
.site-footer a:hover{color:#e74c3c}
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
