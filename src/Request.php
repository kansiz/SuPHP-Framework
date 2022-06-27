<?php
namespace SuPHP;

class Request{

    public static string $method;
    public static string $url;
    public static array $params = [];

    public function __construct()
    {
        self::$method = $this->getMethod();
        self::$url = $this->getUrl();
    }

    public function getMethod()
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    public function getUrl()
    {
        $path = $_SERVER['REQUEST_URI'];
        $position = strpos($path, '?');
        if ($position !== false) {
            $path = substr($path, 0, $position);
        }
        return $path;
    }

    public function isGet()
    {
        return $this->getMethod() === 'GET';
    }

    public function isPost()
    {
        return $this->getMethod() === 'POST';
    }

    public function getBody()
    {
        $data = [];
        if ($this->isGet()) {
            foreach ($_GET as $key => $value) {
                $data[$key] = filter_input(INPUT_GET, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            }
        }
        if ($this->isPost()) {
            foreach ($_POST as $key => $value) {
                $data[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            }
        }
        return $data;
    }

    public function setRouteParams($params)
    {
        self::$params = $params;
    }

    public function getRouteParams()
    {
        return self::$params;
    }

    public function getRouteParam($param, $default = null)
    {
        return self::$params[$param] ?? $default;
    }
}
?>