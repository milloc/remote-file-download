<?php
spl_autoload_register(function ($name) {
    $path = __DIR__ . DIRECTORY_SEPARATOR . 'class' . DIRECTORY_SEPARATOR . $name . '.class.php';
    require_once $path;
});

date_default_timezone_set('Asia/Shanghai');
error_reporting(0);
set_error_handler(function ($errno, $errstr, $errfile, $errline) {
    $logDir = Constant::LOG_DIR;
    $now = time();
    $logFile = $logDir . date('Y-m-d', $now) . '.log';
    $time = date(Constant::DATE_FORMAT, $now);
    $content = "[$time] $errno : $errstr : $errfile($errline)\n";
    error_log($content, 3, $logFile);
}); 

$server = Server::getIntance();
Helper::recursiveScanDir(Constant::HANDLER_DIR, function ($f, $p) {
    require_once $p . $f;
}, true);

$server->run();