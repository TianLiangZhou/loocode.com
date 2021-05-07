@extends("default.layout")
@section("content")
    <div class="container mx-auto my-5">
        <div class="flex flex-wrap">
            <div class="flex-1 flex-grow px-4 overflow-hidden">
                <div class="bg-white border-b  dark:bg-gray-900 dark:border-gray-700">
                    <nav aria-label="breadcrumb">
                        <ol class="px-6 py-3 m-0 flex items-center">
                            <li class=""><a href="/">@lang('main')</a></li>
                            <li class="px-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="8" height="8">
                                    <path d="M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z" fill="currentColor"/>
                                </svg>
                            </li>
                            <li aria-current="page" class="text-gray-400">工具合集</li>
                        </ol>
                    </nav>
                </div>
                <div class="bg-white py-5 px-6  dark:bg-gray-900">
                    <ul class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-6 xl:gap-8 font-semibold text-gray-900 text-center">
                        @foreach($tools as $item)
                            <li class="flex">
                                <a href="{{$item['href']}}" class="relative rounded-xl ring-1 ring-black ring-opacity-5 dark:ring-opacity-50 shadow-sm w-full pt-8 pb-6 px-6 dark:text-gray-500 hover:text-red-500 dark:ring-gray-700">
                                    {{$item['name']}}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
