#### 返回满足回调函数要求的第一个元素

`PHP` `8.4`

```php
$animal = array_find(
    ['dog', 'cat', 'cow', 'duck', 'goose'],
    static fn (string $value): bool => str_starts_with($value, 'c'),
);

var_dump($animal); // string(3) "cat"

```
