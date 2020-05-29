<?php
class Helper {
    private static $json;
    private static $viewAttributes = [];

    /**
     * 判断当前请求是否是json请求，即 content-type 是 否包含 application/json
     */
    public static function isJson() {
        return ($c = $_SERVER['CONTENT_TYPE'] ?? null) && strpos(strtolower($c), 'application/json') !== false;
    }

    public static function pathInfo() {
        return $_SERVER['PATH_INFO'] ?? "/";
    }

    /**
     * 获取当前请求的参数，如果不存在返回null
     */
    public static function request($key) {
        return $_REQUEST[$key] ?? null;
    }

    /**
     * 如果当前请求是json，返回jsonObject或者jsonObject的某一个$key
     */
    public static function json($key = null) {
        if (!Helper::isJson()) {
            return null;
        }
        if (Helper::$json == null) {
            Helper::$json = json_decode(file_get_contents('php://input'));
        }
        if ($key == null) {
            return Helper::$json;
        }
        return Helper::$json->$key ?? null;
    }

    /**
     * 向View中添加属性，以在View中访问
     */
    public static function addViewAttribute($key, $value) {
        Helper::$viewAttributes[$key] = $value;
    }

    /**
     * 获取View的属性
     */
    public static function getViewAttribute() {
        return Helper::$viewAttributes;
    }

    /**
     * 注册filter
     */
    public static function registryFilter($pattern, $filter) {
        Server::getIntance()->registryFilter($pattern, $filter);
    }

    /**
     * 注册handler
     */
    public static function registry($pattern, $handler) {
        Server::getIntance()->registry($pattern, $handler);
    }

    /**
     * 扫描目录下的所有文件，或者递归扫描所有目录下的所有文件
     * 
     * @param bool $recursive 是否递归处理
     */
    public static function recursiveScanDir($dir, $callable, $recursive = false) {
        $files = array_filter(scandir($dir), function($v) {
            return $v != '.' && $v != '..';
        });
        $arr = [];
        foreach ($files as $file) {
            $filePath = $dir . $file;
            if (is_dir($filePath) && $recursive) {
                $arr = array_merge($arr, recursiveScanDir($filePath, $callable, $recursive));
            } else {
                $arr[] = $callable($file, $dir);
            }
        }
        return $arr;
    }

    /**
     * 计算$path对于$root的相对路径
     */
    public static function relativePath($root, $path) {
        $root = realpath($root);
        $path = realpath($path);
        $rArr = explode(DIRECTORY_SEPARATOR, $root);
        $pArr = explode(DIRECTORY_SEPARATOR, $path);
        $rLen = count($rArr);
        $pLen = count($pArr);
        for ($r = $p = 0; $r < $rLen && $p < $pLen; $r++, $p++) {
            if ($rArr[$r] != $pArr[$p]) {
                break;
            }
        }
        $res = '';
        if ($r < $rLen) {
            $res .= str_repeat('..' . DIRECTORY_SEPARATOR , $rLen - $r);
        }
        if ($p < $pLen) {
            $res .= join(array_slice($pArr, $p), DIRECTORY_SEPARATOR);
        }
        return $res;
    }

    /**
     * base64_encode url编码版
     */
    public static function base64url_encode($data) {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
    
    /**
     * base64_decode url编码版
     */
    public static function base64url_decode($data) {
        return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
    }
}