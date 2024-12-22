#### 检查所有数组元素是否满足回调函数的要求

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

// 检查所有动物名称都短于12个字母。
$b = array_all($array, function (string $value) {
    return strlen($value) < 12;
});

var_dump($b); // bool(true)


```
