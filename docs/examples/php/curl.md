## POST GET

使用`curl` 发起POST、GET、PUT等请求.

```php
<?php
// 初始化curl 对象.
$ch = curl_init();

// 设置请求的地址
curl_setopt($ch, CURLOPT_URL, "https://api.example.com/data");

// 设置为POST请求, 不设置默认为GET
curl_setopt($ch, CURLOPT_POST, true);

// 设置post请求body
curl_setopt($ch, CURLOPT_POSTFIELDS, [
    'key1' => 'value1',
    'key2' => 'value2'
]);

// 设置需要返回内容
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// 执行发起请求
$response = curl_exec($ch);

// 
if(curl_errno($ch)){
    echo 'Curl error: ' . curl_error($ch);
} else {
    // 输出返回内容
    echo $response;
}

// Close cURL session
curl_close($ch);
?>
```


## Cookie 和 Header

设置`Cookie`和自定义`Header`示例：

```php
<?php
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://api.example.com/data");
// 设置 Cookie
curl_setopt($ch, CURLOPT_COOKIE, "token=abc123; user=demo");
// 使用 cookie 文件加载 cookie
curl_setopt($ch, CURLOPT_COOKIEFILE, __DIR__ . '/cookie.txt'); // cookie.txt 路径请根据实际情况修改
// 设置自定义 Header
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer your_token',
    'X-Custom-Header: value',
    'Content-Type: application/json'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
if(curl_errno($ch)){
    echo 'Curl error: ' . curl_error($ch);
} else {
    echo $response;
}
curl_close($ch);
?>
```


## 上传文件

使用`cURL`上传文件示例：

```php
<?php
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://api.example.com/upload");
// 使用 CURLFile 上传文件
$postData = [
    'file' => new CURLFile(__DIR__ . '/test.png'), // 路径请替换为实际文件路径
    'desc' => '文件描述'
];
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
if(curl_errno($ch)){
    echo 'Curl error: ' . curl_error($ch);
} else {
    echo $response;
}
curl_close($ch);
?>
```


## 自定义请求方法

使用`cURL`发起`PUT`请求示例：

```php
<?php
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://api.example.com/data/123");
// 设置自定义请求方法
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
curl_setopt($ch, CURLOPT_POSTFIELDS, [
    'key1' => 'newValue1',
    'key2' => 'newValue2'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
if(curl_errno($ch)){
    echo 'Curl error: ' . curl_error($ch);
} else {
    echo $response;
}
curl_close($ch);
?>
```


## 其它常用用法

### 设置超时时间
```php
curl_setopt($ch, CURLOPT_TIMEOUT, 10); // 10秒超时
```

### 跟随重定向
```php
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
```

### 获取响应头
```php
curl_setopt($ch, CURLOPT_HEADER, true); // 响应内容包含header
// 或自定义处理header:
curl_setopt($ch, CURLOPT_HEADERFUNCTION, function($ch, $header) {
    // 处理header内容
    return strlen($header);
});
```

### 保存/读取Cookie到文件
```php
curl_setopt($ch, CURLOPT_COOKIEJAR, __DIR__ . '/cookie.txt'); // 保存cookie
curl_setopt($ch, CURLOPT_COOKIEFILE, __DIR__ . '/cookie.txt'); // 读取cookie
```

### 设置代理
```php
curl_setopt($ch, CURLOPT_PROXY, '127.0.0.1:8888');
```

### 跳过SSL验证（不推荐生产环境）
```php
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
```

### 获取请求详细信息
```php
$info = curl_getinfo($ch);
print_r($info);
```


## 高级用法

### 并发多请求（curl_multi_）
```php
$mh = curl_multi_init();
$chs = [];
foreach ([
    'https://api.example.com/a',
    'https://api.example.com/b'
] as $url) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_multi_add_handle($mh, $ch);
    $chs[] = $ch;
}
do {
    $status = curl_multi_exec($mh, $active);
    curl_multi_select($mh);
} while ($active);
foreach ($chs as $ch) {
    $response = curl_multi_getcontent($ch);
    curl_multi_remove_handle($mh, $ch);
    curl_close($ch);
}
curl_multi_close($mh);
```

### 只获取响应头不下载内容
```php
curl_setopt($ch, CURLOPT_NOBODY, true);
```

### 断点续传（Range）
```php
curl_setopt($ch, CURLOPT_RANGE, '100-200'); // 只下载第100到200字节
```

### 限速
```php
curl_setopt($ch, CURLOPT_MAX_RECV_SPEED_LARGE, 1024*10); // 每秒最多接收10KB
curl_setopt($ch, CURLOPT_MAX_SEND_SPEED_LARGE, 1024*10); // 每秒最多发送10KB
```

### 自定义DNS解析
```php
curl_setopt($ch, CURLOPT_RESOLVE, ['api.example.com:443:1.2.3.4']);
```

### 上传大文件/流式上传
```php
$fp = fopen(__DIR__ . '/bigfile.zip', 'r');
curl_setopt($ch, CURLOPT_PUT, true);
curl_setopt($ch, CURLOPT_INFILE, $fp);
curl_setopt($ch, CURLOPT_INFILESIZE, filesize(__DIR__ . '/bigfile.zip'));
```

### 记录调试日志
```php
curl_setopt($ch, CURLOPT_VERBOSE, true);
$fp = fopen(__DIR__ . '/curl_debug.log', 'w');
curl_setopt($ch, CURLOPT_STDERR, $fp);
```


