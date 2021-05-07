@extends("default.layout")
@section("content")
    <div class="container mx-auto my-5">
        <div class="flex flex-wrap">
            <div class="flex-1 flex-grow px-4 overflow-hidden">
                <div class="bg-white dark:bg-gray-900">
                    <div class="p-7">
                        <div class="mb-2 border-b dark:border-gray-600">
                            <h1 class="mb-4 text-3xl font-bold dark:text-gray-400">{{ $post->post_title }}</h1>
                            <div class="mb-4">
                                <span class="text-gray-400 dark:text-gray-500">
                                    <i class="text-red-500">{{ $post->author->display_name ?? "" }}</i>
                                    · {{ date('Y年m月d日', strtotime($post->post_modified)) }}
                                    · 阅读 {{ $post->meta['_lc_post_views'] }}
                                </span>
                            </div>
                        </div>
                        <article class="markdown-body line-numbers">
                            {!! str_replace('//image.', '//static.', $post->post_content) !!}
                        </article>
                        <div class="mt-8 text-center">
                            @if(($attribute = $post->getTermsAttribute()))
                                @foreach($attribute['tag'] as $slug => $name)
                                    <a class="mr-2 px-2 py-1 font-medium rounded-md text-white bg-red-500" href="/tag/{{ $slug }}">{{ $name }}</a>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
                @if($globalComment)
                <div class="mt-4">
                    @if($comments)
                    <div class="p-7 bg-white dark:bg-gray-900">
                        @foreach($comments as $item)
                            <div class="flex">
                                <div class="p-3 pt-0 pl-0">
                                    <a href="/user/{{ $item->name }}">
                                        <img class="h-12 w-auto rounded-full border border-gray-200 overflow-hidden"
                                             @if($item->avatar)
                                             src="{{ $item->avatar }}"
                                             @else
                                             src="{{ $static_domain }}/assets/images/default_avatar.png"
                                             @endif
                                             alt="{{ $item->name }} avatar">
                                    </a>
                                </div>
                                <div class="">
                                    <div class="pb-2">
                                        <a class="text-red-500" href="/user/{{ $item->name }}">{{ $item->name }}</a>
                                        @if(isset($item->saying))
                                        <span>·</span>
                                        <span class="text-gray-400">最重要的事，永远只有一件</span>
                                        @endif
                                        <div class="text-sm text-gray-400">
                                            <a name="reply{{ $loop->index + 1 }}"
                                               id="reply{{ $loop->index +1 }}"
                                               href="#reply{{ $loop->index + 1 }}">#{{ $loop->index + 1 }}</a>
                                            <span>·</span>
                                            <span>{{ $item->comment_date }}</span>
                                        </div>
                                    </div>
                                    <div class="markdown-body">
                                        {!! $item->comment_content !!}
                                    </div>
                                </div>
                                <div class="text-center justify-content-center">
                                    <a class="fas fa-reply text-custom-white" href="javascript:" title="回复"></a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @endif
                    <div class="p-7 mt-4 bg-white dark:bg-gray-900">
                        <form>
                            <div class="border border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500 dark:border-gray-800">
                                <markdown-toolbar for="commentTextarea" class="flex flex-wrap border-b border-gray-300 dark:border-gray-800">
                                    <md-header class="p-2" aria-label="Add header text" data-ga-click="Markdown Toolbar, click, header" role="button">
                                        <svg  height="16" viewBox="0 0 16 16" version="1.1" width="16" aria-hidden="true"><path fill-rule="evenodd" d="M3.75 2a.75.75 0 01.75.75V7h7V2.75a.75.75 0 011.5 0v10.5a.75.75 0 01-1.5 0V8.5h-7v4.75a.75.75 0 01-1.5 0V2.75A.75.75 0 013.75 2z"></path></svg>
                                    </md-header>
                                    <md-bold class="p-2 pl-0" aria-label="Add bold text <cmd-b>" data-ga-click="Markdown Toolbar, click, bold" role="button" hotkey="b">
                                        <svg  height="16" viewBox="0 0 16 16" version="1.1" width="16" aria-hidden="true"><path fill-rule="evenodd" d="M4 2a1 1 0 00-1 1v10a1 1 0 001 1h5.5a3.5 3.5 0 001.852-6.47A3.5 3.5 0 008.5 2H4zm4.5 5a1.5 1.5 0 100-3H5v3h3.5zM5 9v3h4.5a1.5 1.5 0 000-3H5z"></path></svg>
                                    </md-bold>
                                    <md-italic class="p-2 pl-0" aria-label="Add italic text <cmd-i>" data-ga-click="Markdown Toolbar, click, italic" role="button" hotkey="i">
                                        <svg  height="16" viewBox="0 0 16 16" version="1.1" width="16" aria-hidden="true"><path fill-rule="evenodd" d="M6 2.75A.75.75 0 016.75 2h6.5a.75.75 0 010 1.5h-2.505l-3.858 9H9.25a.75.75 0 010 1.5h-6.5a.75.75 0 010-1.5h2.505l3.858-9H6.75A.75.75 0 016 2.75z"></path></svg>
                                    </md-italic>
                                    <md-quote class="p-2 pl-0" aria-label="Insert a quote" data-ga-click="Markdown Toolbar, click, quote" role="button">
                                        <svg  height="16" viewBox="0 0 16 16" version="1.1" width="16" aria-hidden="true"><path fill-rule="evenodd" d="M1.75 2.5a.75.75 0 000 1.5h10.5a.75.75 0 000-1.5H1.75zm4 5a.75.75 0 000 1.5h8.5a.75.75 0 000-1.5h-8.5zm0 5a.75.75 0 000 1.5h8.5a.75.75 0 000-1.5h-8.5zM2.5 7.75a.75.75 0 00-1.5 0v6a.75.75 0 001.5 0v-6z"></path></svg>
                                    </md-quote>
                                    <md-code class="p-2 pl-0" aria-label="Insert code <cmd-e>" data-ga-click="Markdown Toolbar, click, code" role="button" hotkey="e">
                                        <svg height="16" viewBox="0 0 16 16" version="1.1" width="16" aria-hidden="true"><path fill-rule="evenodd" d="M4.72 3.22a.75.75 0 011.06 1.06L2.06 8l3.72 3.72a.75.75 0 11-1.06 1.06L.47 8.53a.75.75 0 010-1.06l4.25-4.25zm6.56 0a.75.75 0 10-1.06 1.06L13.94 8l-3.72 3.72a.75.75 0 101.06 1.06l4.25-4.25a.75.75 0 000-1.06l-4.25-4.25z"></path></svg>
                                    </md-code>
                                    <md-link class="p-2 pl-0" aria-label="Add a link <cmd-k>" data-ga-click="Markdown Toolbar, click, link" role="button" hotkey="k">
                                        <svg  height="16" viewBox="0 0 16 16" version="1.1" width="16" aria-hidden="true"><path fill-rule="evenodd" d="M7.775 3.275a.75.75 0 001.06 1.06l1.25-1.25a2 2 0 112.83 2.83l-2.5 2.5a2 2 0 01-2.83 0 .75.75 0 00-1.06 1.06 3.5 3.5 0 004.95 0l2.5-2.5a3.5 3.5 0 00-4.95-4.95l-1.25 1.25zm-4.69 9.64a2 2 0 010-2.83l2.5-2.5a2 2 0 012.83 0 .75.75 0 001.06-1.06 3.5 3.5 0 00-4.95 0l-2.5 2.5a3.5 3.5 0 004.95 4.95l1.25-1.25a.75.75 0 00-1.06-1.06l-1.25 1.25a2 2 0 01-2.83 0z"></path></svg>
                                    </md-link>
                                    <md-unordered-list class="p-2 pl-0" aria-label="Add a bulleted list" data-ga-click="Markdown Toolbar, click, unordered list" role="button">
                                        <svg  height="16" viewBox="0 0 16 16" version="1.1" width="16" aria-hidden="true"><path fill-rule="evenodd" d="M2 4a1 1 0 100-2 1 1 0 000 2zm3.75-1.5a.75.75 0 000 1.5h8.5a.75.75 0 000-1.5h-8.5zm0 5a.75.75 0 000 1.5h8.5a.75.75 0 000-1.5h-8.5zm0 5a.75.75 0 000 1.5h8.5a.75.75 0 000-1.5h-8.5zM3 8a1 1 0 11-2 0 1 1 0 012 0zm-1 6a1 1 0 100-2 1 1 0 000 2z"></path></svg>
                                    </md-unordered-list>
                                    <md-ordered-list class="p-2 pl-0" aria-label="Add a numbered list" data-ga-click="Markdown Toolbar, click, ordered list" role="button">
                                        <svg  height="16" viewBox="0 0 16 16" version="1.1" width="16" aria-hidden="true"><path fill-rule="evenodd" d="M2.003 2.5a.5.5 0 00-.723-.447l-1.003.5a.5.5 0 00.446.895l.28-.14V6H.5a.5.5 0 000 1h2.006a.5.5 0 100-1h-.503V2.5zM5 3.25a.75.75 0 01.75-.75h8.5a.75.75 0 010 1.5h-8.5A.75.75 0 015 3.25zm0 5a.75.75 0 01.75-.75h8.5a.75.75 0 010 1.5h-8.5A.75.75 0 015 8.25zm0 5a.75.75 0 01.75-.75h8.5a.75.75 0 010 1.5h-8.5a.75.75 0 01-.75-.75zM.924 10.32l.003-.004a.851.851 0 01.144-.153A.66.66 0 011.5 10c.195 0 .306.068.374.146a.57.57 0 01.128.376c0 .453-.269.682-.8 1.078l-.035.025C.692 11.98 0 12.495 0 13.5a.5.5 0 00.5.5h2.003a.5.5 0 000-1H1.146c.132-.197.351-.372.654-.597l.047-.035c.47-.35 1.156-.858 1.156-1.845 0-.365-.118-.744-.377-1.038-.268-.303-.658-.484-1.126-.484-.48 0-.84.202-1.068.392a1.858 1.858 0 00-.348.384l-.007.011-.002.004-.001.002-.001.001a.5.5 0 00.851.525zM.5 10.055l-.427-.26.427.26z"></path></svg>
                                    </md-ordered-list>
                                    <md-task-list class="p-2 pl-0" aria-label="Add a task list" data-ga-click="Markdown Toolbar, click, task list" role="button" hotkey="L">
                                        <svg height="16" viewBox="0 0 16 16" version="1.1" width="16" aria-hidden="true"><path fill-rule="evenodd" d="M2.5 2.75a.25.25 0 01.25-.25h10.5a.25.25 0 01.25.25v10.5a.25.25 0 01-.25.25H2.75a.25.25 0 01-.25-.25V2.75zM2.75 1A1.75 1.75 0 001 2.75v10.5c0 .966.784 1.75 1.75 1.75h10.5A1.75 1.75 0 0015 13.25V2.75A1.75 1.75 0 0013.25 1H2.75zm9.03 5.28a.75.75 0 00-1.06-1.06L6.75 9.19 5.28 7.72a.75.75 0 00-1.06 1.06l2 2a.75.75 0 001.06 0l4.5-4.5z"></path></svg>
                                    </md-task-list>
                                    <md-mention class="p-2 pl-0" aria-label="Directly mention a user or team" data-ga-click="Markdown Toolbar, click, mention" role="button">
                                        <svg height="16" viewBox="0 0 16 16" version="1.1" width="16" aria-hidden="true"><path fill-rule="evenodd" d="M4.75 2.37a6.5 6.5 0 006.5 11.26.75.75 0 01.75 1.298 8 8 0 113.994-7.273.754.754 0 01.006.095v1.5a2.75 2.75 0 01-5.072 1.475A4 4 0 1112 8v1.25a1.25 1.25 0 002.5 0V7.867a6.5 6.5 0 00-9.75-5.496V2.37zM10.5 8a2.5 2.5 0 10-5 0 2.5 2.5 0 005 0z"></path></svg>
                                    </md-mention>
                                </markdown-toolbar>
                                <textarea  id="commentTextarea" class="w-full dark:bg-gray-900 dark:text-gray-400 h-48 border-0 focus:ring-2 focus:ring-red-500 focus:border-red-500 focus:border-2 " placeholder="@lang('posts.comment_placeholder')" ></textarea>
                            </div>
                            <div class="text-right mt-2">
                                <button type="button" class="bg-red-500 text-white rounded-md px-3 py-1 font-medium" data-loading-text="Loading..." id="commentButton">@lang('posts.comment')</button>
                            </div>
                        </form>
                    </div>
                    <div class="clearfix"></div>
                </div>
                @endif
            </div>
            <div class="w-full mt-4 lg:mt-0 lg:w-80 px-4 flex-initial">
                <div class="bg-white p-4 dark:bg-gray-900">
                    <div class="">
                        <h3 class="text-lg text-gray-500 dark:text-gray-400 font-medium border-b pb-1 border-gray-200 dark:border-gray-700">关于作者</h3>
                        <div class="pt-4">
                            <div class="flex items-center">
                                <picture class="rounded-full mr-3">
                                    <img
                                        @if(isset($post->author->avatar) && $post->author->avatar)
                                        src="{{ $post->author->avatar }}"
                                        @else
                                        src="{{ $static_domain }}/assets/images/default_avatar.png"
                                        @endif
                                        alt="avatar"
                                        class="rounded-full" width="64" height="64">
                                </picture>
                                <div><h5 class="mb-0"><a href="javascript:" class="text-body dark:text-gray-500">{{ $post->author->display_name ?? ""  }}</a></h5></div>
                            </div>
                            <p class="py-3 dark:text-gray-400">全栈工程师</p>
                            <div class="flex items-center mb-3 text-gray-500 text-sm">
                                <div class="mr-4">
                                    <span>@lang('posts.view')</span> <strong>{{$post->userMeta['_lc_post_views']}}</strong>
                                </div>
                                <div>
                                    <span>@lang('posts.like')</span> <strong>{{$post->userMeta['_lc_post_like']}}</strong>
                                </div>
                            </div>
{{--                            <button class="bg-red-500 text-white rounded-md px-3 py-1">关注作者</button>--}}
                        </div>
                    </div>
                </div>
                @if(isset($post->meta["toc"]) && $post->meta["toc"])
                    <div class="bg-white p-4 mt-4 dark:bg-gray-900" style="position: sticky;top: 0">
                        <div class="">
                            <h3 class="text-lg text-gray-500 dark:text-gray-400 font-medium border-b pb-1 border-gray-200 dark:border-gray-700">文章目录</h3>
                            <div class="pt-4 dark:text-gray-500">
                                {!! $post->meta["toc"] !!}
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
    @if(isset($post->userMeta['alipayQr']) || isset($post->userMeta['wechatQr']))
    <div class="hidden" id="appreciate" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog  modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">{{ __('posts.donation') }}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" class="text-muted">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <ul class="list-inline">
                        @if(isset($post->userMeta['alipayQr']))
                        <li class="list-inline-item m-3">
                            <img style="border: 2px solid #449ee2"
                                 width="150"
                                 src="{{ $static_domain }}{{ $post->userMeta['alipayQr'] }}"
                                 alt="支付宝收款码">
                        </li>
                        @endif
                        @if(isset($post->userMeta['wechatQr']))
                        <li class="list-inline-item m-3">
                            <img style="border: 2px solid #53a849"
                                 width="150"
                                 src="{{ $static_domain }}{{ $post->userMeta['wechatQr'] }}"
                                 alt="微信收款码">
                        </li>
                        @endif
                    </ul>
                </div>
                <div class="modal-footer">
                </div>
            </div>
        </div>
    </div>
    @endif
@endsection

@section("footer_js")
    <script type="text/javascript" src="//cdn.jsdelivr.net/npm/prismjs@1.23.0/prism.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/prismjs@1.23.0/plugins/autoloader/prism-autoloader.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/prismjs@1.23.0/plugins/line-numbers/prism-line-numbers.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/prismjs@1.23.0/plugins/toolbar/prism-toolbar.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/prismjs@1.23.0/plugins/copy-to-clipboard/prism-copy-to-clipboard.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/prismjs@1.23.0/plugins/show-language/prism-show-language.min.js"></script>
    <script type="text/javascript" src="{{ mix("/assets/js/article.js")  }}"></script>
@endsection
