`Java`的加密解密使用`javax.crypto.*`包的相关函数. 下面的代码实现`AES`的`GCM`模式的加密解密.

## 加密

```java
public String encrypt(String data) throws Exception {
    byte[] dataInBytes = data.getBytes();
    // 获取加密算法模式
    encryptionCipher = Cipher.getInstance("AES/GCM/NoPadding");
    encryptionCipher.init(Cipher.ENCRYPT_MODE, key);
    byte[] encryptedBytes = encryptionCipher.doFinal(dataInBytes);
    return encode(encryptedBytes);
}
```



## 解密

```java

public String decrypt(String encryptedData) throws Exception {
    byte[] dataInBytes = decode(encryptedData);
    Cipher decryptionCipher = Cipher.getInstance("AES/GCM/NoPadding");
    GCMParameterSpec spec = new GCMParameterSpec(DATA_LENGTH, encryptionCipher.getIV());
    decryptionCipher.init(Cipher.DECRYPT_MODE, key, spec);
    byte[] decryptedBytes = decryptionCipher.doFinal(dataInBytes);
    return new String(decryptedBytes);
}
```

## 完整代码

```java
package com.loocode.example;

import javax.crypto.Cipher;
import javax.crypto.KeyGenerator;
import javax.crypto.SecretKey;
import javax.crypto.spec.GCMParameterSpec;
import java.util.Base64;

public class AES {

	private SecretKey key;
	private final int KEY_SIZE = 128;
	private final int DATA_LENGTH = 128;
	private Cipher encryptionCipher;

	public void init() throws Exception {
		KeyGenerator keyGenerator = KeyGenerator.getInstance("AES");
		keyGenerator.init(KEY_SIZE);
		key = keyGenerator.generateKey();
	}
	public String encrypt(String data) throws Exception {
		byte[] dataInBytes = data.getBytes();
		encryptionCipher = Cipher.getInstance("AES/GCM/NoPadding");
		encryptionCipher.init(Cipher.ENCRYPT_MODE, key);
		byte[] encryptedBytes = encryptionCipher.doFinal(dataInBytes);
		return encode(encryptedBytes);
	}

	public String decrypt(String encryptedData) throws Exception {
		byte[] dataInBytes = decode(encryptedData);
		Cipher decryptionCipher = Cipher.getInstance("AES/GCM/NoPadding");
		GCMParameterSpec spec = new GCMParameterSpec(DATA_LENGTH, encryptionCipher.getIV());
		decryptionCipher.init(Cipher.DECRYPT_MODE, key, spec);
		byte[] decryptedBytes = decryptionCipher.doFinal(dataInBytes);
		return new String(decryptedBytes);
	}

	private String encode(byte[] data) {
		return Base64.getEncoder().encodeToString(data);
	}

	private byte[] decode(String data) {
		return Base64.getDecoder().decode(data);
	}

	public static void main(String[] args) throws Exception {
		AES aes = new AES();
		aes.init();
		String encrypt = aes.encrypt("123456");

		System.out.printf("%s",  aes.decrypt(encrypt));

	}
}

```
