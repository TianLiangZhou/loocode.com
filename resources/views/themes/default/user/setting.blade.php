@extends("default.layout")
@section("content")
    <div class="container mx-auto my-5">
        <div class="flex flex-wrap px-4 ">
            <div class="w-full md:w-3/12 px-4 px-6 bg-white dark:bg-gray-900">
                <div class="mb-3 pt-3">
                    <h3 class="font-bold text-2xl">设置</h3>
                </div>
                <ul class="flex flex-row md:flex-col">
                    <li class="text-xl py-2">
                        <i class="iconfont icon-email"></i>
                        <a href="#email">邮箱</a>
                    </li>
                    <li class="text-xl py-2">
                        <i class="iconfont icon-account"></i>
                        <a href="#account">账户</a>
                    </li>
                    <li class="text-xl py-2">
                        <i class="iconfont icon-security"></i>
                        <a href="#security">安全</a>
                    </li>
                    <li class="text-xl py-2">
                        <i class="iconfont icon-account"></i>
                        <a href="#third">第三方</a>
                    </li>
                </ul>
            </div>
            <div class="flex-1 w-full bg-white dark:bg-gray-900">
                <div class="mb-3 pt-3">
                    <h3 id="email" class="font-medium text-xl">邮箱设置</h3>
                </div>
                <ul class="border-t border-gray-200">
                    <li class="px-5 py-6">
                        <div class="pb-md-0 pb-3">
                            <div class="text-lg font-medium">你的邮箱</div>
                            <div class="mt-3 flex flex-wrap items-center">
                                <label>
                                    <input class="focus:border-red-500 focus:ring-red-500 focus:border-2 bg-gray-200 rounded-md" disabled id="email-text" placeholder="example@gmail.com"
                                           type="email" value="{{ $user->user_email }}"/>
                                </label>
                                <div>
                                    <button class="bg-red-500 text-white font-bold ml-2 py-1 px-2 rounded-md" id="btn-email">修改邮箱</button>
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>
                <div class="mb-3">
                    <h3 id="account" class="font-medium text-xl">账户</h3>
                </div>
                <ul class="border-t border-gray-200">
                    <li class="flex flex-wrap py-6 px-5 justify-between">
                        <div class="pb-md-0 pb-3 flex-1">
                            <div class="text-lg font-medium">
                                支付宝二维码
                            </div>
                            <div class="mt-3">
                                上传个人支付宝收款二维码用于赞赏
                            </div>
                            @if(isset($metas['alipayQr']))
                            <div class="mt-3">
                                <img
                                    width="150"
                                    src="{{ $static_domain }}{{ $metas['alipayQr'] }}"
                                    alt="支付宝收款码">
                            </div>
                            @endif
                        </div>
                        <div class="flex flex-1 relative">
                            <div class="w-full">
                                <input type="file"
                                       class="filepond"
                                       name="alipayQr"
                                       data-allow-image-preview="true"
                                       data-instant-upload="false"
                                       data-max-file-size="1MB"
                                       data-max-files="1">
                            </div>
                        </div>
                    </li>
                    <li class="flex flex-wrap py-6 px-5 justify-between">
                        <div class="pb-md-0 pb-3 flex-1">
                            <div class="text-lg font-medium">
                                微信二维码
                            </div>
                            <div class="mt-3">
                                上传个人微信收款二维码用于赞赏
                            </div>
                            @if(isset($metas['wechatQr']))
                            <div class="mt-3">
                                <img
                                    width="150"
                                    src="{{ $static_domain }}{{ $metas['wechatQr'] }}"
                                    alt="微信收款码">
                            </div>
                            @endif
                        </div>
                        <div class="flex relative flex-1">
                            <div class="w-full">
                                <input type="file"
                                   class="filepond"
                                   name="wechatQr"
                                   data-allow-image-preview="true"
                                   data-instant-upload="false"
                                   data-max-file-size="1MB"
                                   data-max-files="1">
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
@endsection
@section("footer_js")
    <script type="text/javascript" src="{{ mix("/assets/js/user.js") }}"></script>
@endsection
