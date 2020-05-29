<?php
class Constant {
    /**
     * root
     */
    const ROOT_DIR = __DIR__ . DIRECTORY_SEPARATOR . '..'. DIRECTORY_SEPARATOR;
    /**
     * tmp_dir 用于保存远程下载到服务器的文件
     */
    const TMP_DIR = Constant::ROOT_DIR . 'tmp' . DIRECTORY_SEPARATOR;
    /**
     * zip_dir 保存压缩的文件的目录
     */
    const ZIP_DIR = Constant::ROOT_DIR . 'zip' . DIRECTORY_SEPARATOR;
    /**
     * handler 的目录
     */
    const HANDLER_DIR = Constant::ROOT_DIR . 'handler' . DIRECTORY_SEPARATOR;
    /**
     * 日志的目录
     */
    const LOG_DIR = Constant::ROOT_DIR . 'log' . DIRECTORY_SEPARATOR;
    /**
     * template的目录
     */
    const TMPLATE_DIR = Constant::ROOT_DIR . 'template' . DIRECTORY_SEPARATOR;
    /**
     * 登录用户信息文件地址
     */
    const ACCOUNT_FILE = Constant::ROOT_DIR . 'accounts.json';

    /**
     * 默认的时间格式
     */
    const DATE_FORMAT = 'Y-m-d H:i:s';
}