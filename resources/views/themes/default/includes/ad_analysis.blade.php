@if($ad['google'])
    <script data-ad-client="{{$ad['google']}}" async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
@endif
@if($ad['baidu'])
    <script type="text/javascript" src="//cpro.baidustatic.com/cpro/ui/cm.js" async="async" defer="defer"></script>
@endif
@if($analysis['google'])
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ $analysis['google'] }}"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '{{$analysis['google']}}');
    </script>
@endif
@if($analysis['cnzz'])
    <script type="text/javascript" src="https://s22.cnzz.com/z_stat.php?id={{$analysis['cnzz']}}&web_id={{$analysis['cnzz']}}"></script>
@endif
@if($analysis['baidu'])
    <script>
        var _hmt = _hmt || [];
        (function() {
            var hm = document.createElement("script");
            hm.src = "https://hm.baidu.com/hm.js?{{$analysis['baidu']}}";
            var s = document.getElementsByTagName("script")[0];
            s.parentNode.insertBefore(hm, s);
        })();
    </script>
@endif
