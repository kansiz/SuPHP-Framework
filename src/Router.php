<?php
namespace SuPHP;
class Router
{

    var $deneme;
    public string $salla;
    public static $instance;
    public array $routeMap;

    public function __construct()
    {
        $this->routeMap = [];
        $this->salla = "salladim";
    }

    public static function init(){
        if ( is_null( self::$instance ) )
        {
            Router::$instance = new Router();
        }
        return Router::$instance;
    }

    public static function Add(array $methods, string $url, $callback){
        foreach ($methods as $k => $v) {
            Router::init()->routeMap[$v][$url] = $callback;
        }
    }

    public function renderView($view, $params = [])
    {
        //return Application::$app->view->renderView($view, $params);
    }

    public function renderViewOnly($view, $params = [])
    {
        //return Application::$app->view->renderViewOnly($view, $params);
    }

	public static function Run()
    {
		foreach (Router::init()->routeMap[Request::$method] as $key => $value) {
			$query = preg_match(sprintf('@^%s$@i', rtrim(self::ParsePath($key), '/')), Request::$url, $params);
			if ($query > 0) {
				//Core::Request()->setRouteParams($params);
				self::Call($value);			
				break;
			}
		}
    }

	public static function Call($callback)
    {
		if (is_string($callback)) {
			[$className,$funcName] = explode("@",$callback);
			$className = "App\\Controllers\\".$className;
			//print($className." : ".$funcName);
			if (method_exists($className,$funcName)) {
				return (new $className())->$funcName();
			}

		}
    }

    public static function ParsePath(string $path): string
    {
        if (preg_match('/(\/{.*}\?)/i', $path, $matches)) {
            foreach (range(1, \count($matches)) as $match) {
                $path = preg_replace('/\/({.*}\?)/', '/?$1', $path);
            }
        }

        preg_replace_callback('/[\[{\(].*[\]}\)]/U', function ($match) use (&$path): string {
            $match = str_replace(['{', '}'], '', $match[0]);

            if (str_contains($match, ':')) {
                [$name, $type] = explode(':', $match, 2);
            } else {
                $name = $match;
                $type = 'any';
            }

            $patterns = [
                'num' => '(?<name>\d+)',
                'str' => '(?<name>[\w\-_]+)',
                'any' => '(?<name>[^/]+)',
                'all' => '(?<name>.*)',
            ];
            $replaced = str_replace('name', $name, ($patterns[$type] ?? $patterns['any']));
            $path = str_replace("{{$name}:$type}", $replaced, $path);
            $path = str_replace("{{$name}}", $replaced, $path);

            return $path;
        }, $path);

        return $path;
    }
}
?>