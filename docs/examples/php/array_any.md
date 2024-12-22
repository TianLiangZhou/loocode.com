#### 检查是否至少有一个数组元素满足回调函数的要求

`PHP` `8.4`

```php
$array = [
    'a' => 'dog',
    'b' => 'cat',
    'c' => 'cow',
    'd' => 'duck',
    'e' => 'goose',
    'f' => 'elephant'
];

// 检测动物名大于5个字符
$b = array_any($array, function (string $value) {
    return strlen($value) > 5;
});

var_dump($b); // bool(true)

```
