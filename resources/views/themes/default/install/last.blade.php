<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>安装成功</title>
    <link rel="stylesheet" type="text/css" href="{{ mix("/assets/css/app.css")  }}"/>
</head>
<body class="bg-gray-50 dark:bg-gray-800">
<div class="w-full">
    <div class="mx-auto max-w-screen-sm">
        <div class="w-full p-9 bg-white mt-16 border border-gray-200 rounded-md dark:bg-gray-900 dark:border-gray-800">
            <h2 class="text-xl font-bold pb-3 border-b border-gray-300 dark:border-gray-800  dark:text-gray-500">安装完成</h2>
            <div class="my-2 text-gray-600  dark:text-gray-400">
                您的程序已安装成功，如果想重新安装，请删除boostrap/cache目录中的<span class="font-bold text-red-500">__installed</span>文件。
            </div>
            <div class="my-2"></div>
            <a href="{{ $dashboard }}"
                class="px-4 py-1.5 rounded-md border border-red-500 bg-red-500 text-white font-medium">
                登录
            </a>
        </div>
    </div>
</div>
</body>
</html>
