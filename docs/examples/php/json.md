## json_encode


```php
$array = [ 'a' => 'dog', 'b' => 'cat', 'c' => 'cow', 'd' => 'duck', 'e' => 'goose', 'f' => 'elephant' ];

echo json_encode($array);

```

> 这将输出: `{"a":"dog","b":"cat","c":"cow","d":"duck","e":"goose","f":"elephant"}`


不编码输出.


```php
$array = [ 'a' => '测试不编码输出' ];

echo json_encode($array), "\n", json_encode($array, JSON_UNESCAPED_UNICODE);


```

> 这将输出: {"a":"\u6d4b\u8bd5\u4e0d\u7f16\u7801\u8f93\u51fa"}
> {"a":"测试不编码输出"}


美化输出.


```php
$array = [ 'a' => 'dog', 'b' => 'cat', 'c' => 'cow', 'd' => 'duck', 'e' => 'goose', 'f' => 'elephant' ];

echo json_encode($array, JSON_PRETTY_PRINT);

```

> 这将输出:
> {
>    "a": "dog",
>    "b": "cat",
>    "c": "cow",
>    "d": "duck",
>    "e": "goose",
>    "f": "elephant"
> }


## json_decode

对`JSON`格式的字符串进行解码.

```php

$json = '{"a":"dog"}';

var_dump(json_decode($json)); // 以对象形式返回
var_dump(json_decode($json, true)); // 以数组形式返回

```

以上示例会输出：

```plain

object(stdClass)#1 (1) {
  ["a"]=>
  string(3) "dog"
}
array(1) {
  ["a"]=>
  string(3) "dog"
}

```


大整形转换成字符串.


```php

$json = '{"number": 12345678901234567890}';

var_dump(json_decode($json));
var_dump(json_decode($json, false, 512, JSON_BIGINT_AS_STRING));

```

以上示例会输出：

```plain

object(stdClass)#1 (1) {
  ["number"]=>
  float(1.2345678901235E+19)
}
object(stdClass)#1 (1) {
  ["number"]=>
  string(20) "12345678901234567890"
}

```


## json_validate

`PHP >= 8.3`

检查一个字符串是否包含有效的JSON.

```php 

<?php
var_dump(json_validate('{ "test": { "foo": "bar" } }'));
var_dump(json_validate('{ "": "": "" } }'));
?>

```

以上示例会输出：

```plain

bool(true)
bool(false)

```

## json_last_error

最后发生的错误。

```php
<?php
// An invalid UTF8 sequence
$text = "\xB1\x31";

$json  = json_encode($text);
$error = json_last_error();

var_dump($json, $error === JSON_ERROR_UTF8);
?>
```

示例将输出：

```php
string(4) "null"
bool(true)
```
