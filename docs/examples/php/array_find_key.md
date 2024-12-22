#### 返回满足回调函数要求的第一个元素的键值

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

// 查找第一个名称长度超过 4 个字符的动物。
$key = array_find_key($array, function (string $value) {
    return strlen($value) > 4;
});

var_dump($key); // string(1) "e"

```
