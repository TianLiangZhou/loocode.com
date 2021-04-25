<?php
namespace FastFFI\QrCode;

use FFI;
use RuntimeException;

class QrCode
{
    /**
     * @var ?QrCode
     */
    private static ?QrCode $qc = null;

    /**
     * @var FFI
     */
    protected FFI $ffi;

    /**
     * @var FFI\CData|null
     */
    private ?FFI\CData $qr_code = null;


    /**
     * @var FFI\CData|null
     */
    private ?FFI\CData $cfg = null;

    /**
     * QrCode constructor.
     */
    private function __construct(string $data = null)
    {
        if (ini_get('ffi.enable') == false) {
            throw new RuntimeException("请设置php.ini中的ffi.enable参数");
        }
        $this->ffi = $this->makeFFI();
        if ($data) {
            $this->create($data);
        }
    }

    /**
     *
     */
    public function __destruct()
    {
        // TODO: Implement __destruct() method.
        if ($this->cfg) {
            if ($this->cfg->filename != null) {
                FFI::free($this->cfg->filename);
            }
            if ($this->cfg->bg_color != null) {
                FFI::free($this->cfg->bg_color);
            }
            if ($this->cfg->fg_color != null) {
                FFI::free($this->cfg->fg_color);
            }
            if ($this->cfg->logo != null) {
                FFI::free($this->cfg->logo);
            }

        }
        if ($this->qr_code) {
            $this->ffi->free_qrcode($this->qr_code);
        }
    }

    /**
     * @param string $data
     */
    public function create(string $data)
    {
        if ($this->qr_code) {
            $this->__destruct();
            $this->qr_code = null;
        }
        $this->qr_code = $this->ffi->create($data);
        if (($len = $this->lastErrorLength()) > 0) {
            if (($errMsg = $this->lastErrorMessage($len))) {
                throw new RuntimeException($errMsg);
            }
        }
    }

    /**
     *
     */
    private function cfg()
    {
        $this->cfg = $this->ffi->new("struct Cfg");
        $this->cfg->dimension_w = 8;
        $this->cfg->dimension_h = 8;
        $this->cfg->zone = 1;
    }

    /**
     * 以svg字符串输出
     *
     * @return string
     */
    public function svg()
    {
        if ($this->cfg == null) {
            $this->cfg();
        }
        return $this->convent($this->ffi->svg($this->qr_code, FFI::addr($this->cfg)));
    }

    /**
     * @return string
     */
    public function image(): string
    {
        if ($this->cfg == null) {
            $this->cfg();
        }
        if ($this->cfg->filename == null) {
            throw new RuntimeException("The image mode must specify the file name");
        }
        return $this->convent($this->ffi->image($this->qr_code, FFI::addr($this->cfg)));
    }

    /**
     * @return string
     */
    public function character(): string
    {
        if ($this->cfg == null) {
            $this->cfg();
        }
        return $this->convent($this->ffi->character($this->qr_code, FFI::addr($this->cfg)));
    }

    /**
     * @return string
     */
    public function unicode(): string
    {
        if ($this->cfg == null) {
            $this->cfg();
        }
        return $this->convent($this->ffi->unicode($this->qr_code, FFI::addr($this->cfg)));
    }

    /**
     * @param int $width
     * @param int $height
     * @return $this
     */
    public function withDimension(int $width, int $height = 1): QrCode
    {
        if ($this->cfg == null) {
            $this->cfg();
        }
        $this->cfg->dimension_w = $width;
        $this->cfg->dimension_h = $height;
        return $this;
    }

    /**
     * @param bool $zone
     * @return $this
     */
    public function withZone(bool $zone): QrCode
    {
        if ($this->cfg == null) {
            $this->cfg();
        }
        $this->cfg->zone = (int) $zone;
        return $this;
    }

    /**
     * 设置二维码logo标志
     *
     * @param string $filename
     * @param bool $autoResize
     * @param bool $filterTransparent
     */
    public function withLogo(string $filename, bool $autoResize = false, bool $filterTransparent = false): QrCode
    {
        if (!file_exists($filename)) {
            throw new RuntimeException("'$filename' not exist");
        }
        if ($this->cfg == null) {
            $this->cfg();
        }
        $len = strlen($filename) + 1;
        $this->cfg->logo = $this->ffi->new("char[$len]", 0);
        $this->cfg->auto_resize = (int) $autoResize;
        $this->cfg->filter_transparent = (int) $filterTransparent;
        FFI::memcpy($this->cfg->logo, $filename, $len - 1);
        return $this;
    }

    /**
     * @param string $bgColor
     * @return $this
     */
    public function withBgColor(string $bgColor): QrCode
    {
        if ($this->cfg == null) {
            $this->cfg();
        }
        $len = strlen($bgColor) + 1;
        $this->cfg->bg_color = $this->ffi->new("char[$len]", 0);
        FFI::memcpy($this->cfg->bg_color, $bgColor, $len - 1);
        return $this;
    }

    /**
     * @param string $fgColor
     * @return $this
     */
    public function withFgColor(string $fgColor): QrCode
    {
        if ($this->cfg == null) {
            $this->cfg();
        }
        $len = strlen($fgColor) + 1;
        $this->cfg->fg_color = $this->ffi->new("char[$len]", 0);
        FFI::memcpy($this->cfg->fg_color, $fgColor, $len - 1);
        return $this;
    }

    /**
     * @param string $filename
     * @return $this
     */
    public function withFilename(string $filename): QrCode
    {
        if (!file_exists(($dir = dirname($filename)))) {
            throw new RuntimeException("'$dir' not exist");
        }
        if ($this->cfg == null) {
            $this->cfg();
        }
        $len = strlen($filename) + 1;
        $this->cfg->filename = $this->ffi->new("char[$len]", 0);
        FFI::memcpy($this->cfg->filename, $filename, $len - 1);
        return $this;
    }

    /**
     * @return int
     */
    public function lastErrorLength()
    {
        return $this->ffi->last_error_length();
    }

    /**
     * @param int $len
     * @return string
     */
    public function lastErrorMessage(int $len)
    {
        $err = $this->ffi->new('char[' . $len .']', 0);
        $errMsg = "";
        if ($this->ffi->last_error_message($err, $len) > 0) {
            $errMsg = FFI::string($err);
            FFI::free($err);
        }
        return $errMsg;
    }

    /**
     * @param FFI\CData $CData
     * @return string
     */
    private function convent(FFI\CData $CData)
    {
        $result = FFI::string($CData);
        $this->ffi->free_image($CData);
        return $result;
    }



    /**
     * @param string|null $data
     * @return static
     */
    public static function new(string $data = null): QrCode
    {
        if (self::$qc == null) {
            self::$qc = new static($data);
        }
        return self::$qc;
    }

    /**
     *
     */
    private function __clone()
    {

    }

    /**
     * @return FFI
     */
    private function makeFFI(): FFI
    {
        return FFI::cdef(
            file_get_contents(__DIR__ . '/../lib/ffi_qrcode.h'),
            $this->defaultLibraryPath()
        );
    }

    /**
     * @return string
     */
    private function defaultLibraryPath(): string
    {
        if (PHP_INT_SIZE !== 8) {
            throw new RuntimeException('不支持32位系统，请自行编译lib文件');
        }
        $suffix = PHP_SHLIB_SUFFIX;
        if (PHP_OS == 'Darwin') {
            $suffix = 'dylib';
        }
        $filepath = __DIR__ . '/../lib/libffi_qrcode.' . $suffix;
        if (file_exists($filepath)) {
            return realpath($filepath);
        }
        throw new RuntimeException('不支持的系统，请自行编译lib文件');
    }
}
