## ffi-qrcode

`ffi-qrcode`是一个快速将字符串生成二维码的`PHP FFI`库，支持多种生成方式。

### 环境

需要`php >= 7.4` 以上的版本并且开启了`FFI`扩展。如果你需要自己编译库还需要装`rust` 工具链。

还需要设置`php.ini` 中的`ffi.enable`为`On`。

### Usage 

该库提供四种基础用法：不带音标，带音标，首字母，多音字带音标。

```php
<?php

use FastFFI\QrCode\QrCode;

include __DIR__ . '/../src/QrCode.php';


$qr_code = QrCode::new("abc");
$qr_code
    ->withDimension(8, 8)
    ->withBgColor("#FF0000")
    ->withFgColor("#FF00FF")
    ->withLogo(__DIR__ . '/logo.png', false, true)
    ->withFilename(__DIR__ . '/php_qrcode.png')
    ->image();
```

以上程序执行后的结果: 

![php_qrcode](examples/php_qrcode.png)

[在线生成](http://loocode.com/tool/qrcode/qr-code-generator)

### FAQ

- 在`centos`上执行失败?
  
  确定是不是`glibc`版本过低。可以使用`ldd lib/libffi_pinyin.so` 来查看库信息。
如果出现`/lib64/libc.so.6: version 'glibc_2.18' not found`就说明你服务的`glibc`版本过低。 
  下载glibc编译升级，下载地址: `wget http://mirrors.ustc.edu.cn/gnu/libc/glibc-2.18.tar.gz` 
  
