<!DOCTYPE html>
<html lang="zh_CN" class="text-gray-700 bg-gray-100 font-sans">
<head>
    <meta charset="UTF-8" />
    <title>{{ $seo->title }}</title>
    <meta name="description" content="{{ $seo->description }}" />
    <meta name="keywords" content="{{ $seo->keyword }}" />
    <link href="/favicon.ico" rel="icon" sizes="32x32"/>
    <meta name="google-site-verification" content="HySQr9AQd4P4wZ8jK8glrbXDbN38fBpoLyXi50YxduU" />
    <meta name="baidu-site-verification" content="code-TNCHrV5vi3" />
    <meta name="baidu_union_verify" content="9a195a5285bf05966b08bb34cdbe62fa">
    <link rel="manifest" href="/manifest.json" />
    <meta name="theme-color" content="#f4645f" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
    <link rel="stylesheet" type="text/css" href="//at.alicdn.com/t/font_338218_4wxwv706rhb.css" />
    <link rel="stylesheet" type="text/css" href="{{ mix("/assets/css/app.css")  }}"/>
    @if($user)
    <meta name="Authorization" content="{{ $user->ID }}">
    @endif
    <!-- in your header -->
    @yield("header_css")
</head>
<body x-data="{login: false}" x-on:login.window="login = $event.detail.open">
<div class="w-full">
    @include("default.includes.header")
    @yield("content")
    @include("default.includes.footer")
</div>
<div id="fly-rocket" class="fixed bottom-12 right-12 text-red-500 bg-white rounded-full cursor-pointer">
    <i class="iconfont icon-Top text-6xl"></i>
</div>
<script type="text/javascript" src="{{ mix("/assets/js/manifest.js") }}"></script>
<script type="text/javascript" src="{{ mix("/assets/js/vendor.js") }}"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.8.2/dist/alpine.min.js"></script>
<script type="text/javascript" src="{{ mix("/assets/js/app.js") }}"></script>
@include('default.includes.ad_analysis')
@yield("footer_js")
</body>
</html>

