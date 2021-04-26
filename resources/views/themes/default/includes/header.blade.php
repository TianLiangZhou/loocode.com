<header class="w-full mb-4 bg-white shadow-md dark:bg-gray-800 border-t-4 border-red-500">
    <div class="container mx-auto px-4">
        <div class="flex justify-start items-center py-1">
            <div class="flex justify-start flex-1 md:flex-none mx-2 md:mx-0">
                <a href="{{ $options['site_url'] }}">
                    <span class="sr-only">{{ $options['site_title'] }}</span>
                    <img class="h-8 w-auto" src="{{ $static_domain }}/assets/images/main-64.png" alt="{{ $options['site_url'] }}logo" />
                </a>
            </div>
            <div class="hidden md:block md:ml-8 w-72">
                <form method="get" action="/search">
                    <label class="block">
                        <input type="text"
                               name="q"
                               class="placeholder-gray-500 bg-gray-50 p-1 block w-full rounded-2xl text-sm dark:bg-gray-800 dark:text-gray-50 focus:ring-0 border border-gray-50 focus:border-gray-500"
                               placeholder="搜索">
                    </label>
                </form>
            </div>
            <div class="hidden md:block flex-1">
                <ul class="font-medium">
                    @foreach($menu as $item)
                    <li class="px-3">
                        <a class="hover:text-red-500" href="@if($item['type'] == 'category')/category/{{ $item['name'] }}@elseif($item['type'] == 'post_tag')/tag/{{ $item['name'] }}@else{{ $item['uri'] }}@endif">
                            {{ $item['name'] }}
                        </a>
                    </li>
                    @endforeach
                </ul>
            </div>
            <div class="mx-2 md:mx-0">
                <ul class="flex items-center font-medium text-gray-500">
                    <li>
                        <a class="hover:text-red-500" href="/" rel="nofollow" title="{{$options['site_title']}}">@lang('main')</a>
                    </li>
                    <li class="mx-4">
                        <a class="hover:text-red-500"  href="/tools">工具</a>
                    </lic>
                    @if($user)
                        <li>
                            <a class="hover:text-red-500"  href="/user/setting">@lang('setting')</a>
                        </li>
                        <li class="mx-4">
                            <a class="hover:text-red-500"  href="/logout">@lang('logout')</a>
                        </li>
                        <li>
                            <a href="javascript:">
                                <img class="rounded-full h-11 w-auto border border-gray-200 overflow-hidden" alt="{{ $user->display_name }} avatar" src="{{ $user->avatar }}" />
                            </a>
                        </li>
                    @else
                        <li class="cursor-pointer">
                            <a href="#" rel="nofollow" class="hover:text-red-500"  @click="login = true">@lang('login')</a>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
</header>
