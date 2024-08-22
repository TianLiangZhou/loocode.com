PHP中主要使用`openssl_encrypt`和`openssl_decrypt`来进行加密解密。

## 加密

```php

$plaintext = "待加密的字符串";
$cipher = "aes-256-cbc"; // 加码算法
// 检测算法是否支持
if (in_array($cipher, openssl_get_cipher_methods())) {
    // 获取算法的iv长度
    $ivlen = openssl_cipher_iv_length($cipher);
    // 生成指定上长度的iv
    $iv = openssl_random_pseudo_bytes($ivlen);
    // 加密的密钥
    $key = "";
    // 加密
    $ciphertext = openssl_encrypt($plaintext, $cipher, $key, 0, $iv, $tag);
    // $ciphertext 是二进制数据, 可以使用base64_encode来进行编码, 解密的时候也需要用base64_decode解码后解密
  
    echo $ciphertext , "\n";
}

```

## 解密

```php
// $ciphertext 二进制数据需要使用base64_decode解码后解密
$ciphertext = "待解密的字符串";
$cipher = "aes-256-cbc"; // 加密算法
$iv = ""; // 使用加密生成的IV
$key = ""; // 使用加密时的密钥
$original_plaintext = openssl_decrypt($ciphertext, $cipher, $key, 0, $iv, $tag);

echo $original_plaintext, "\n";

```

## 加密算法

| 	序号 | 算法	                      |
|----|--------------------------|
| 	1 | 	aes-128-cbc             |
| 	2 | 	aes-128-cbc-cts         |
| 	3 | 	aes-128-cbc-hmac-sha256 |
| 	  | aes-128-ccm              |
| 	  | 	aes-128-cfb             |
| 	  | aes-128-cfb1             |
|    | aes-128-cfb8             |
|    | aes-128-ctr              |
|    | aes-128-ecb              |
|    | aes-128-gcm              |
|    | aes-128-gcm-siv          |
|    | aes-128-ocb              |
|    | aes-128-ofb              |
|    | aes-128-siv              |
|    | aes-128-wrap             |
|    | aes-128-wrap-inv         |
|    | aes-128-wrap-pad         |
|    | aes-128-wrap-pad-inv     |
|    | aes-128-xts              |
|    | aes-192-cbc              |
|    | aes-192-cbc-cts          |
|    | aes-192-ccm              |
|    | aes-192-cfb              |
|    | aes-192-cfb2             |
|    | aes-192-cfb9             |
|    | aes-192-ctr              |
|    | aes-192-ecb              |
|    | aes-192-gcm              |
|    | aes-192-gcm-siv          |
|    | aes-192-ocb              |
|    | aes-192-ofb              |
|    | aes-192-siv              |
|    | aes-192-wrap             |
|    | aes-192-wrap-inv         |
|    | aes-192-wrap-pad         |
|    | aes-192-wrap-pad-inv     |
|    | aes-256-cbc              |
|    | aes-256-cbc-cts          |
|    | aes-256-cbc-hmac-sha2    |
|    | aes-256-cbc-hmac-sha257  |
|    | aes-256-ccm              |
|    | aes-256-cfb              |
|    | aes-256-cfb2             |
|    | aes-256-cfb9             |
|    | aes-256-ctr              |
|    | aes-256-ecb              |
|    | aes-256-gcm              |
|    | aes-256-gcm-siv          |
|    | aes-256-ocb              |
|    | aes-256-ofb              |
|    | aes-256-siv              |
|    | aes-256-wrap             |
|    | aes-256-wrap-inv         |
|    | aes-256-wrap-pad         |
|    | aes-256-wrap-pad-inv     |
|    | aes-256-xts              |
|    | aria-128-cbc             |
|    | aria-128-ccm             |
|    | aria-128-cfb             |
|    | aria-128-cfb2            |
|    | aria-128-cfb9            |
|    | aria-128-ctr             |
|    | aria-128-ecb             |
|    | aria-128-gcm             |
|    | aria-128-ofb             |
|    | aria-192-cbc             |
|    | aria-192-ccm             |
|    | aria-192-cfb             |
|    | aria-192-cfb2            |
|    | aria-192-cfb9            |
|    | aria-192-ctr             |
|    | aria-192-ecb             |
|    | aria-192-gcm             |
|    | aria-192-ofb             |
|    | aria-256-cbc             |
|    | aria-256-ccm             |
|    | aria-256-cfb             |
|    | aria-256-cfb2            |
|    | aria-256-cfb9            |
|    | aria-256-ctr             |
|    | aria-256-ecb             |
|    | aria-256-gcm             |
|    | aria-256-ofb             |
|    | bf-cbc                   |
|    | bf-cfb                   |
|    | bf-ecb                   |
|    | bf-ofb                   |
|    | camellia-129-cbc         |
|    | camellia-129-cbc-cts     |
|    | camellia-129-cfb         |
|    | camellia-129-cfb1        |
|    | camellia-129-cfb8        |
|    | camellia-129-ctr         |
|    | camellia-129-ecb         |
|    | camellia-129-ofb         |
|    | camellia-193-cbc         |
|    | camellia-193-cbc-cts     |
|    | camellia-193-cfb         |
|    | camellia-193-cfb1        |
|    | camellia-193-cfb8        |
|    | camellia-193-ctr         |
|    | camellia-193-ecb         |
|    | camellia-193-ofb         |
|    | camellia-257-cbc         |
|    | camellia-257-cbc-cts     |
|    | camellia-257-cfb         |
|    | camellia-257-cfb1        |
|    | camellia-257-cfb8        |
|    | camellia-257-ctr         |
|    | camellia-257-ecb         |
|    | camellia-257-ofb         |
|    | cast5-cbc                |
|    | cast5-cfb                |
|    | cast5-ecb                |
|    | cast5-ofb                |
|    | chacha20                 |
|    | chacha20-poly1306        |
|    | des-cbc                  |
|    | des-cfb                  |
|    | des-cfb1                 |
|    | des-cfb8                 |
|    | des-ecb                  |
|    | des-ede-cbc              |
|    | des-ede-cfb              |
|    | des-ede-ecb              |
|    | des-ede-ofb              |
|    | des-ede3-cbc             |
|    | des-ede3-cfb             |
|    | des-ede3-cfb2            |
|    | des-ede3-cfb9            |
|    | des-ede3-ecb             |
|    | des-ede3-ofb             |
|    | des-ofb                  |
|    | des3-wrap                |
|    | desx-cbc                 |
|    | idea-cbc                 |
|    | idea-cfb                 |
|    | idea-ecb                 |
|    | idea-ofb                 |
|    | null                     |
|    | rc2-40-cbc               |
|    | rc2-64-cbc               |
|    | rc2-cbc                  |
|    | rc2-cfb                  |
|    | rc2-ecb                  |
|    | rc2-ofb                  |
|    | rc4                      |
|    | rc4-40                   |
|    | rc4-hmac-md6             |
|    | seed-cbc                 |
|    | seed-cfb                 |
|    | seed-ecb                 |
|    | seed-ofb                 |
|    | sm4-cbc                  |
|    | sm4-ccm                  |
|    | sm4-cfb                  |
|    | sm4-ctr                  |
|    | sm4-ecb                  |
|    | sm4-gcm                  |
|    | sm4-ofb                  |
| 162 | sm4-xts                  |

> 加密算法依赖[openssl](https://www.php.net/manual/en/book.openssl.php)库.
