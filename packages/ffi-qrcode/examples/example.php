<?php

use FastFFI\QrCode\QrCode;

include __DIR__ . '/../src/QrCode.php';


$qr_code = QrCode::new("https://github.com/TianLiangZhou/ffi-qrcode");
$qr_code
    ->withDimension(8, 8)
    ->withBgColor("#FF0000")
    ->withFgColor("#FF00FF")
    ->withLogo(__DIR__ . '/logo.png', false, true)
    ->withFilename(__DIR__ . '/php_qrcode.png')
    ->image();
