<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<style type="text/css">
*{box-sizing:border-box;margin:0;padding:0}
body,html{font-family:Georgia,'Times New Roman',serif;font-size:16px;background:#f8f9fa;color:#212529}
img{max-width:100%;display:block}
a{text-decoration:none;color:inherit}
.site-header{background:#fff;border-bottom:2px solid #2c7be5;padding:14px 20px}
.site-header .inner{max-width:1200px;margin:0 auto;display:flex;align-items:center;justify-content:space-between}
.site-header .logo{font-size:22px;font-weight:700;color:#2c7be5;font-family:Arial,sans-serif}
.site-header nav a{font-size:12px;color:#666;margin-left:14px;font-family:Arial,sans-serif}
.site-header nav a:hover{color:#2c7be5}
.outer{max-width:1200px;margin:0 auto;padding:28px 16px}
.layout-wrap{display:grid;grid-template-columns:280px 1fr;gap:32px}
.sidebar{font-family:Arial,sans-serif;position:sticky;top:20px;align-self:start}
.sidebar-title{font-size:11px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:#888;margin-bottom:10px;padding-bottom:8px;border-bottom:2px solid #2c7be5}
.sidebar-links a{display:block;font-size:13px;color:#444;padding:7px 0;border-bottom:1px solid #e9ecef;line-height:1.4}
.sidebar-links a:hover{color:#2c7be5;padding-left:4px;transition:padding .15s}
.post-list{}
.post-item{display:flex;gap:14px;padding:16px 0;border-bottom:1px solid #dee2e6;background:#fff;margin-bottom:8px;padding:14px;border-radius:4px}
.post-item:last-child{border-bottom:none}
.post-thumb{flex:0 0 100px;height:70px;overflow:hidden;border-radius:3px}
.post-thumb img{width:100%;height:100%;object-fit:cover}
.post-info{flex:1}
.post-title{font-size:16px;font-weight:700;margin-bottom:6px;line-height:1.3}
.post-title a:hover{color:#2c7be5}
.post-excerpt{font-size:13px;color:#666;line-height:1.5;font-family:Arial,sans-serif}
.article-header{margin-bottom:24px}
.article-title{font-size:28px;font-weight:700;line-height:1.3;margin-bottom:12px;color:#1a1a2e}
.article-tags{display:flex;flex-wrap:wrap;gap:6px;margin-top:10px;font-family:Arial,sans-serif}
.article-tags .tag{font-size:11px;background:#e8f1fd;color:#2c7be5;padding:3px 10px;border-radius:12px}
.article-tags .tag:hover{background:#2c7be5;color:#fff}
.article-body{line-height:1.8;color:#333;font-size:16px}
.article-body p{margin-bottom:16px}
.article-body h2,h3{font-family:Arial,sans-serif;margin:24px 0 10px}
.site-footer{background:#fff;border-top:1px solid #dee2e6;text-align:center;padding:18px;font-size:12px;color:#aaa;font-family:Arial,sans-serif;margin-top:28px}
.site-footer a{margin:0 8px;color:#bbb}
.site-footer a:hover{color:#2c7be5}
@media(max-width:900px){.layout-wrap{grid-template-columns:1fr}.sidebar{display:none}}
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
	<div class="outer">
		<div class="layout-wrap">
			<aside class="sidebar">
				<div class="sidebar-title">Recent Posts</div>
				<div class="sidebar-links">
					@foreach($posts as $spost)
					<a href="{{ $spost->slug }}.html">{{ $spost->title }}</a>
					@endforeach
				</div>
			</aside>
			<main class="main-content">
				@yield('header')
				@include('export.html._ads.related')
				@yield('content')
			</main>
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
