@extends("default.layout")
@section("header_css")
    <link rel="stylesheet" href="//cdn.jsdelivr.net/gh/devicons/devicon@v2.9.0/devicon.min.css">
@endsection
@section("content")
    <div class="container mx-auto my-5">
        <div class="flex flex-wrap">
            <div class="flex-1 flex-grow px-4 overflow-hidden">
                <div class="flex flex-col relative bg-white">
                    <div class="flex text-6xl flex-wrap flex-grow p-6 justify-between text-red-500">
                        <div class="mx-4"><a href="/tag/php" title="php专题"><i class="devicon-php-plain"></i></a></div>
                        <div class="mx-4"><a href="/tag/docker" title="docker专题"><i class="devicon-docker-plain"></i></a></div>
                        <div class="mx-4"><a href="/tag/java" title="java专题"><i class="devicon-java-plain"></i></a></div>
                        <div class="mx-4"><a href="/tag/golang" title="golang专题"><i class="devicon-go-plain"></i></a></div>
                        <div class="mx-4"><a href="/tag/mysql" title="mysql专题"><i class="devicon-mysql-plain"></i></a></div>
                        <div class="mx-4"><a href="/tag/javascript" title="javascript专题"><i class="devicon-javascript-plain"></i></a></div>
                    </div>
                    <div class="">
                        @foreach($posts as $item)
                        <div class="p-6 relative border-b border-opacity-5">
                            <h4 class="text-xl font-medium"><a class="hover:text-red-500 hover:underline" href="/post/{{ $item->id }}" title="{{ $item->post_title }}">{{ $item->post_title }}</a></h4>
                            <div class="text-gray-400 my-3">
                                <a href="/user/{{ $item->name }}"><span>{{ $item->name }}</span></a>
                                <span>·</span>
                                <span>{{ date('Y/m/d', strtotime($item->post_modified)) }}</span>
                                @if($item->tags)
                                    <span>·</span>
                                    <a href="/tag/{{ $item->tags[0] }}">{{ $item->tags[0] }}</a>
                                @endif
                            </div>
                        </div>
                        @if ($loop->index == 1)
                            <ins class="adsbygoogle"
                                 style="display:block"
                                 data-ad-format="fluid"
                                 data-ad-layout-key="-dy-6f+m7+bm-20a"
                                 data-ad-client="ca-pub-1413550160662632"
                                 data-ad-slot="8061439532"></ins>
                        @endif
                        @endforeach
                    </div>
                    <div class="p-6">
                        {{ $posts->links("default/vendor/pagination/simple-tailwind")  }}
                    </div>
                </div>
            </div>
            <div class="w-full mt-4 lg:mt-0 lg:w-80 px-4 flex-initial">
                <div class="bg-white p-4">
                    <div class="">
                        <h3 class="text-xl text-gray-500 font-medium border-b pb-1 border-gray-200 after:border-b after:border-red-500">@lang("top_question")</h3>
                        <div class="pt-4">
                            <ul>
                                @foreach($hotPosts as $posts)
                                <li class="text-gray-600 py-2 font-medium">
                                    <span>{{ $loop->iteration }}.</span><a class="hover:underline" href="/post/{{ $posts->id }}">{{ $posts->post_title}}</a>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
