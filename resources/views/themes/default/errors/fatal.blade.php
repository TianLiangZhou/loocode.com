<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Server Error</title>
    <link rel="stylesheet" type="text/css" href="<?php if (isset($css)) {
        echo $css;
    } ?>"/>
</head>
<body class="bg-gray-50 dark:bg-gray-800">
<div class="relative flex items-top justify-center min-h-screen sm:items-center sm:pt-0">
    <div class="mx-auto max-w-screen-sm sm:px-6 lg:px-8 py-6 bg-white dark:bg-gray-900 w-full">
        <div class="flex flex-col items-center sm:justify-start  text-gray-500 dark:text-gray-400">
            <div class="ml-4 text-lg tracking-wider">
                <?php
                    if (isset($message)) {
                        if (stripos($message, 'writable') !== false || stripos($message, 'Operation not permitted') !== false) {

                ?>
                 <p>
                     <span class="bg-gray-50 dark:bg-gray-700 px-1 font-medium">bootstrap</span>、<span class="bg-gray-50 dark:bg-gray-700 px-1 font-medium">storage</span>目录必须具有写入权限
                 </p>
                <?php
                        }
                    }
                ?>
            </div>
            <div class="text-right mt-4 py-2 text-lg border-t w-full border-gray-100 dark:border-gray-800 tracking-wider">
                服务出了点问题
            </div>
        </div>
    </div>
</div>
</body>
</html>
