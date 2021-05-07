<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>安装</title>
    <link rel="stylesheet" type="text/css" href="{{ mix("/assets/css/app.css")  }}"/>
</head>
<body class="bg-gray-50 dark:bg-gray-800">
<div class="w-full">
    <div class="mx-auto max-w-screen-sm">
        <div class="w-full p-9 bg-white mt-16 border border-gray-200 rounded-md dark:bg-gray-900  dark:border-gray-800">
            <h2 class="text-xl font-bold pb-3 border-b border-gray-300 dark:text-gray-500 dark:border-gray-800">您正在安装程序</h2>
            <form action="/install" method="post" class="mt-2">
                <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                <div>
                    <label class="text-gray-600 pb-1 block dark:text-gray-500">站点名称</label>
                    <input name="title" required="" class="w-full border border-gray-300 rounded-md px-1 py-2 ring-0 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400" />
                    <label class="text-gray-500 pt-0.5 block text-sm">
                        您给站点取得名称，它将显示在每一个页面。
                    </label>
                </div>
                <div class="mt-9">
                    <label class="text-gray-600 pb-1 block  dark:text-gray-500">标语</label>
                    <input name="subtitle" class="w-full border border-gray-300 rounded-md px-1 py-2 ring-0 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400" />
                </div>
                <div class="mt-9">
                    <label class="text-gray-600 pb-1 block  dark:text-gray-500">昵称</label>
                    <input name="nickname" class="w-full border border-gray-300 rounded-md px-1 py-2 ring-0 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400"/>
                    <label class="text-gray-500 pt-0.5 block text-sm">
                        您可以给自己取一个闪亮的昵称，虽然它不是必须的。
                    </label>
                </div>
                <div class="mt-9">
                    <label class="text-gray-600 pb-1 block  dark:text-gray-500">邮箱</label>
                    <input type="email" required="" name="email" class="w-full border border-gray-300 rounded-md px-1 py-2 ring-0 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400"/>
                    <label class="text-gray-500 pt-0.5 block text-sm">
                        邮箱是您登录后台的凭证
                    </label>
                </div>
                <div class="mt-9">
                    <label class="text-gray-600 pb-1 block dark:text-gray-500">密码</label>
                    <input name="password" required="" minlength="6" class="w-full border border-gray-300 rounded-md px-1 py-2 ring-0 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400" />
                    <label class="text-gray-500 pt-0.5 block text-sm">
                        密码也是您登录后台的重要凭证，一定要保管好您的密码
                    </label>
                </div>
                <div class="mt-9 flex justify-between">
                    <div>
                        @if ($errors->any())
                            <div class="">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                    <button type="submit" class="px-4 py-2 rounded-md border border-red-500 bg-red-500 text-white font-medium">安装</button>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>
