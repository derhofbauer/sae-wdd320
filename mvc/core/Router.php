<?php

namespace Core;

/**
 * Class Router
 *
 * @package Core
 * @todo    : comment
 */
class Router
{
    /**
     * @var array
     */
    private array $routes = [];

    /**
     * @var array
     */
    private array $paramNames = [];

    /**
     * Routen automatisch laden
     */
    public function __construct ()
    {
        $this->loadRoutes();
    }

    /**
     * Routern aus den Routes Files laden
     */
    public function loadRoutes ()
    {
        $webRoutes = require_once __DIR__ . '/../routes/web.php';
        $apiRoutes = require_once __DIR__ . '/../routes/api.php';

        $this->routes = $webRoutes;

        foreach ($apiRoutes as $apiRoute => $callable) {
            $route = "/api/$apiRoute";
            $route = str_replace('//', '/', $route);
            $this->routes[$route] = $callable;
        }
    }

    public function route ()
    {
        $path = '';
        if (isset($_GET['path'])) {
            $path = $_GET['path'];
        }

        $path = '/' . rtrim($path, '/'); // '/', '/blog/foobar'

        $callable = [];
        $params = [];

        if (array_key_exists($path, $this->routes)) {
            $callable = $this->routes[$path];
        } else {
            foreach ($this->routes as $route => $_callable) {
                if (str_contains($route, '{')) {
                    $regex = $this->buildRegex($route);

                    $matches = [];
                    if (preg_match_all($regex, $path, $matches, PREG_SET_ORDER) >= 1) {
                        $callable = $_callable;

                        foreach ($this->paramNames as $paramName) {
                            $params[$paramName] = $matches[0][$paramName];
                        }

                        break;
                    }
                }
            }
        }

        if (empty($callable)) {
            View::error404();
        } else {
            $controller = new $callable[0]();
            $action = $callable[1];

            call_user_func_array([$controller, $action], $params); // $controller->$action(...$params)
        }
    }

    public function buildRegex (string $route): string
    {
        $matches = [];
        preg_match_all('/{([a-zA-Z]+)}/', $route, $matches);
        $this->paramNames = $matches[1];

        // ^\/blog\/(?<slug>[^\/]+)$
        $regex = str_replace('/', '\/', $route); // /blog/{slug} => \/blog\/{slug}
        foreach ($this->paramNames as $paramName) {
            $regex = str_replace("{{$paramName}}", "(?<$paramName>[^\/]+)", $regex); // {slug} => (?<slug>[^\/]+) führt zu \/blog\/{slug} => \/blog\/(?<slug>[^\/]+)
        }

        $regex = "/^$regex$/"; // \/blog\/(?<slug>[^\/]+) => /^\/blog\/(?<slug>[^\/]+)$/

        return $regex;
    }

}
