@extends("default.layout")
@section("content")
    <div class="container mx-auto my-5">
        <div class="flex flex-wrap">
            <div class="flex-1 flex-grow px-4 overflow-hidden">
                <div class="bg-white border-b">
                    <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
                        <ol class="px-6 py-3 m-0 flex items-center">
                            <li class=""><a href="/">@lang('main')</a></li>
                            <li class="px-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="8" height="8">
                                    <path d="M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z" fill="currentColor"/>
                                </svg>
                            </li>
                            <li aria-current="page" class="text-gray-400">{{ $taxonomy }}</li>
                        </ol>
                    </nav>
                </div>
                <div class="bg-white">
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
            </div>
            <div class="w-full mt-4 lg:mt-0 lg:w-80 px-4 flex-initial">

            </div>
        </div>
    </div>
@endsection
