<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<link href="https://fonts.googleapis.com/css?family=Inter:400,600,700" rel="stylesheet">
	<style type="text/css">
*{box-sizing:border-box;margin:0;padding:0}
body,html{font-family:Inter,sans-serif;font-size:15px;background:#0f0f0f;color:#e0e0e0}
img{max-width:100%;display:block}
a{text-decoration:none;color:#4da3ff}
a:hover{text-decoration:underline}
.site-header{border-bottom:1px solid #222;padding:16px 0;background:#161616}
.site-header .inner{max-width:860px;margin:0 auto;padding:0 20px;display:flex;align-items:center;justify-content:space-between}
.site-header .brand{font-size:22px;font-weight:700;color:#f1f2f3}
.site-header nav a{margin-left:20px;font-size:13px;color:#888}
.container{max-width:860px;margin:0 auto;padding:20px}
.post-list-item{display:flex;gap:16px;padding:16px 0;border-bottom:1px solid #1e1e1e;align-items:flex-start}
.post-list-item:last-child{border-bottom:none}
.post-list-thumb{flex:0 0 140px;width:140px}
.post-list-thumb img{width:140px;height:100px;object-fit:cover;border-radius:4px}
.post-list-body{flex:1;min-width:0}
.post-list-title{font-size:16px;font-weight:600;color:#e0e0e0;line-height:1.4;margin-bottom:6px}
.post-list-title a{color:#e0e0e0}
.post-list-title a:hover{color:#4da3ff;text-decoration:none}
.post-list-excerpt{font-size:13px;color:#888;line-height:1.5;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
.post-list-meta{font-size:12px;color:#555;margin-top:6px}
.site-footer{border-top:1px solid #1e1e1e;text-align:center;padding:20px 0;margin-top:20px;font-size:13px;color:#555}
.site-footer a{margin:0 10px;color:#666}
.navi{margin:12px 0}
.navi a{margin:3px 4px;font-size:13px;color:#4da3ff}
@media(max-width:600px){.post-list-thumb{flex:0 0 80px;width:80px}.post-list-thumb img{width:80px;height:60px}}
	</style>
	@yield('head')
	@include('export.html._ads.header')
</head>
<body>
	<header class="site-header">
		<div class="inner">
			<a href="index.html" class="brand">{{ config('export.site_name') }}</a>
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
