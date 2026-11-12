<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
	<link href="https://fonts.googleapis.com/css?family=Raleway" rel="stylesheet">
	<style type="text/css">
.columns,.container,img{max-width:100%}
.site-title a:hover,a{text-decoration:none}
body,html{font-family:Raleway,sans-serif;font-size:14px;font-weight:400;background:#fff;background-size:cover;height:100%}
.site-title,.site-title-tag{text-align:center;font-family:Raleway,sans-serif}
.site-title{font-size:44px;margin-bottom:20px;border-bottom:1px solid #e1e2e3;line-height:34px;padding:20px 0}
.columns .posts-list p,.site-title-tag{line-height:24px}
.site-title a{color:#111}
.site-title-tag{font-size:12px;letter-spacing:8px;margin:0}
.header{padding-bottom:13px;margin-bottom:13px}
.container{margin:0 auto;padding:0;background-color:#fff;max-width:900px}
.row.content,.row.footer,.row.header{width:auto;max-width:100%;margin:0 10%}
.row.footer{padding:30px 0;background:0 0}
.columns{column-width:240px;column-gap:25px;width:100%;height:auto;padding:0;margin:0 auto}
.columns .posts-list{width:100%;margin:0 0 10px;padding:0;display:inline-block;column-break-inside:avoid}
.columns .posts-list img{width:100%;height:auto;margin-bottom:5px}
a{color:#f42966}
.sidebar ul{margin:0;padding:0}
.sidebar ul li{list-style:none}
.footer{margin-top:21px;padding-top:13px;border-top:1px solid #eee}
.footer a{margin:0 15px}
.navi{margin:13px 0}
.navi a{margin:5px 2px;font-size:95%}
@media only screen and (min-width:1489px){.columns{column-width:300px}}
@media only screen and (max-width:1280px){.row.content,.row.footer,.row.header{margin:0 5%}}
@media only screen and (max-width:1024px){.row.content,.row.footer,.row.header{margin:0}}
	</style>
	@yield('head')
	@include('export.html._ads.header')
</head>
<body>
	<div class="site-title">
		<a href="index.html">{{ config('export.site_name') }}</a>
		<p class="site-title-tag">Home Design Ideas</p>
	</div>
	<div class="container">
		<div class="row header">
			<div class="col-sm-12 text-center">
				@yield('header')
				@include('export.html._ads.related')
			</div>
		</div>
		<div class="row content">
			<div class="col-md-12">
				@yield('content')
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
</body>
</html>
