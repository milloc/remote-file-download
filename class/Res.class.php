<?php
/**
 * 返回结果
 */
class Res {
    const CODE_SUCCESS = 200;
    const CODE_ERROR = 500;

    public $code;
    public $data;
    public $msg;

    public function __construct($code = null, $data = null, $msg = null) {
        $this->code = $code;
        $this->data = $data;
        $this->msg = $msg;
    }

    public static function ok($data = null) {
        return new Res(Res::CODE_SUCCESS, $data);
    }

    public static function err($msg = null) {
        return new Res(Res::CODE_ERROR, null, $msg);
    }
}