<?php
class Server {
    private $filters = [];
    private $handlers = [];
    private static $instance = null;

    public static function getIntance():Server {
        if (Server::$instance == null) {
            Server::$instance = new Server();
        }
        return Server::$instance;
    }

    /**
     * 注册filter，处理请求的时候，如果一个返回false则不会继续处理filter
     */
    public function registryFilter($pattern, $filter) {
        $this->filters[$pattern] = $filter;
    }

    /**
     * 注册handler，处理请求的时候，只会返回一个最佳匹配的handler来处理请求
     */
    public function registry($pattern, $handhler) {
        $this->handlers[$pattern] = $handhler;
    }

    /**
     * 运行
    */
    public function run() {
        $this->handleFilter();
        $this->handleMapping();
    }

    protected function handleFilter() {
        $pathInfo = $_SERVER['PATH_INFO'] ?? "/";
        foreach ($this->filters as $pattern => $filter) {
            $ignore = $this->matchPattern($pattern, $pathInfo) < 0;
            if (!$ignore && !$filter()) {
                return false;
            }
        }
        return true;
    }

    protected function handleMapping($uri = '') {
        $uri = $uri ?: ($_SERVER['PATH_INFO'] ?? "/");
        $handle = $this->resovleMapping($uri);
        $res = $handle($uri);
        $this->handleResult($res);
    }

    /**
     * 根据pathInfo匹配
     * 1. 完全匹配
     * 2. 通配符匹配, 支持末尾**
     */
    protected function resovleMapping($pathInfo) {
        $max = 0;
        $maxHandler = null;
        foreach ($this->handlers as $pattern => $h) {
            if (($c = $this->matchPattern($pattern, $pathInfo)) == 0) {
                // 完全匹配 直接返回
                return $h;
            }
            // 否则寻找匹配程度最大的 pattern
            if ($c > 0 && $c > $max) {
                $maxHandler = $h;
            }
        }
        if ($maxHandler !== null) {
            return $maxHandler;
        }
       
        // 404
        return $this->handler['/404'] ?? function () {
            return $this->_404();
        };
    }

    /**
     * 计算 pattern 和 pathInfo 的匹配程度
     * 
     * @return int 0 完全一样 > 0 表示通配符之前匹配的长度 -1 表示不匹配
     */
    private function matchPattern($pattern, $pathInfo) {
        if ($pattern == $pathInfo) {
            return 0;
        }
        if (($l = strlen($pattern)) >= 3 && ($p = strpos($pattern, '**', $l - 2)) !== false) {
            return $p;
        }
        return -1;
    }

    protected function handleView($view) {
        if (strpos($view, '.') === false) {
            $view .= '.php';
        }
        $viewPath = Constant::TMPLATE_DIR . $view;
        if (file_exists($viewPath)) {
            extract(Helper::getViewAttribute());
            require_once $viewPath;
            return;
        } else {
            $this->handleMapping('/404');
        }
    }

    protected function handleResult($res) {
        if (is_string($res)) {
            if (strpos($res, 'forword:') === 0) {
                $forword = substr($res, strlen('forword:'));
                $this->handleMapping($forword);
                return;
            }
            $this->handleView($res);
            return;
        }
        header('content-type: application/json');
        echo json_encode($res);
    }

    protected function _404() {
        http_response_code(404);
    }
}
