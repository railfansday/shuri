<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<link href="https://fonts.googleapis.com/css?family=Inter:400,600,700" rel="stylesheet">
	<style type="text/css">
*{box-sizing:border-box;margin:0;padding:0}
body,html{font-family:Inter,sans-serif;font-size:14px;background:#fff;color:#1a1a1a}
img{max-width:100%;display:block}
a{text-decoration:none;color:#1a73e8}
a:hover{text-decoration:underline}
.site-header{border-bottom:1px solid #eee;padding:14px 0;background:#fff}
.site-header .inner{max-width:640px;margin:0 auto;padding:0 20px;display:flex;align-items:center;justify-content:space-between}
.site-header .brand{font-size:20px;font-weight:700;color:#1a1a1a}
.site-header nav a{margin-left:16px;font-size:12px;color:#555}
.container{max-width:640px;margin:0 auto;padding:20px}
.post-list-item{display:flex;gap:12px;padding:14px 0;border-bottom:1px solid #f0f0f0;align-items:flex-start}
.post-list-item:last-child{border-bottom:none}
.post-list-thumb{flex:0 0 100px;width:100px}
.post-list-thumb img{width:100px;height:72px;object-fit:cover;border-radius:3px}
.post-list-body{flex:1;min-width:0}
.post-list-title{font-size:14px;font-weight:600;color:#1a1a1a;line-height:1.4;margin-bottom:5px}
.post-list-title a{color:#1a1a1a}
.post-list-title a:hover{color:#1a73e8;text-decoration:none}
.post-list-excerpt{font-size:12px;color:#777;line-height:1.5;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
.site-footer{border-top:1px solid #eee;text-align:center;padding:18px 0;margin-top:20px;font-size:12px;color:#999}
.site-footer a{margin:0 8px;color:#666}
.navi{margin:10px 0}
.navi a{margin:3px 4px;font-size:12px;color:#1a73e8}
@media(max-width:480px){.post-list-thumb{flex:0 0 70px;width:70px}.post-list-thumb img{width:70px;height:50px}}
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
