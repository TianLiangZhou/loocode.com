`Golang`的AES加密解密主要使用`aes`标准库，标准库下面有`gcm`, `cbc`, `cfb`, `ofb`, `ctr`算法实现。

## 加密

```go
// 加密
func Encrypter(cipherText, key, iv []byte) (string, error) {
	// 补齐内容
	paddingCipherText := PKCS5Padding(cipherText, aes.BlockSize)

	block, err := aes.NewCipher(key)
	if err != nil {
		return "", err
	}
	decCipherText := make([]byte, len(paddingCipherText))
	mode := cipher.NewCBCEncrypter(block, iv)
	mode.CryptBlocks(decCipherText, paddingCipherText)
	return base64.StdEncoding.EncodeToString(decCipherText), nil
}

// 对内容进行补齐, aes都按块加密块的长度需要对齐相等
func PKCS5Padding(cipherText []byte, blockSize int) []byte {
	padding := blockSize - len(cipherText)%blockSize
	padText := bytes.Repeat([]byte{byte(padding)}, padding)
	return append(cipherText, padText...)
}
```

## 解密

```go
func Decrypter(encryptedText string, key, iv []byte) (string, error) {
	cipherText, err := base64.StdEncoding.DecodeString(encryptedText)
	if err != nil {
		return "", err
	}
	block, err := aes.NewCipher(key)
	if err != nil {
		return "", err
	}
	mode := cipher.NewCBCDecrypter(block, iv)
	mode.CryptBlocks(cipherText, cipherText)
	// 去掉补齐的内容
	s, err := PKCS5Unpadding(cipherText, aes.BlockSize)
	if err != nil {
		return "", err
	}
	return fmt.Sprintf("%s", s), nil
}

// 去掉补齐的字节
func PKCS5Unpadding(data []byte, blockSize int) ([]byte, error) {
	if blockSize < 1 {
		return nil, errors.New("block size looks wrong")
	}
	if len(data)%blockSize != 0 {
		return nil, errors.New("data isn't aligned to blockSize")
	}
	if len(data) == 0 {
		return nil, errors.New("data is empty")
	}
	paddingLength := int(data[len(data)-1])
	for _, el := range data[len(data)-paddingLength:] {
		if el != byte(paddingLength) {
			return nil, fmt.Errorf("padding had malformed entries. Have '%x', expected '%x'", paddingLength, el)
		}
	}
	return data[:len(data)-paddingLength], nil
}

```

## 主程序

```go
package main

import (
	"bytes"
	"crypto/aes"
	"crypto/cipher"
	"encoding/base64"
	"errors"
	"fmt"
)

func main() {
	key := []byte("10E10AB578E174EEDC8B20ABEE042454")
	iv := []byte("79FF10964EFC6A5A")
	plainText := []byte("Hello World")
	encrypterText, err := Encrypter(plainText, key, iv)
	if err != nil {
		fmt.Printf("encrypter error: %s\n", err)
		return
	}
	decrypter, err := Decrypter(encrypterText, key, iv)
	if err != nil {
		fmt.Printf("decrypter error: %s\n", err)
	} else {
		fmt.Println(decrypter)
	}
}
```
