<?php

namespace App;

class Route {
    private static array $routes = [];
    private static array $namedRoutes = [];
    private static array $globalMiddlewares = [];
    private static array $groupMiddlewares = [];
    private static string $prefix = '';
    private static $currentRoute = null;

    private static function addRoute(string $method, string $path, callable|array $callback): Route
    {
        $fullPath = self::$prefix . $path;
        $route = [
            'method' => $method,
            'path' => $fullPath,
            'callback' => $callback,
            'where' => [],
            'middlewares' => array_merge(self::$groupMiddlewares, self::$globalMiddlewares),
            'name' => null
        ];

        self::$routes[] = $route;
        self::$currentRoute = count(self::$routes) - 1;

        return new self();
    }

    public static function get(string $path, callable|array $callback): Route
    {
        return self::addRoute('GET', $path, $callback);
    }

    public static function post(string $path, callable|array $callback): Route
    {
        return self::addRoute('POST', $path, $callback);
    }

    public static function put(string $path, callable|array $callback): Route
    {
        return self::addRoute('PUT', $path, $callback);
    }

    public static function patch(string $path, callable|array $callback): Route
    {
        return self::addRoute('PATCH', $path, $callback);
    }

    public static function delete(string $path, callable|array $callback): Route
    {
        return self::addRoute('DELETE', $path, $callback);
    }

    public static function options(string $path, callable|array $callback): Route
    {
        return self::addRoute('OPTIONS', $path, $callback);
    }

    public static function addMatch(array $methods, string $path, callable|array $callback): Route
    {
        foreach ($methods as $method) {
            self::addRoute($method, $path, $callback);
        }
        return new self();
    }

    public static function any(string $path, callable|array $callback): Route
    {
        $methods = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'];
        return self::addMatch($methods, $path, $callback);
    }

    /**
     * @throws \Exception
     */
    public static function generateUrl(string $name, array $params = []): string
    {
        if (!isset(self::$namedRoutes[$name])) {
            throw new \Exception("Route {$name} not defined.");
        }

        $route = self::$namedRoutes[$name];
        $path = $route['path'];

        foreach ($params as $key => $value) {
            $path = str_replace('{' . $key . '}', $value, $path);
        }

        return url($path);
    }

    /**
     * @throws \Exception
     */
    public static function middleware($middleware): Route
    {
        if (!is_callable($middleware) && !is_string($middleware) && !is_array($middleware)) {
            throw new \Exception("Invalid middleware type");
        }

        if (self::$currentRoute !== null) {
            self::$routes[self::$currentRoute]['middlewares'][] = $middleware;
        } else {
            self::$globalMiddlewares[] = $middleware;
        }
        return new self();
    }

    public static function group($attributes, $callback): void
    {
        $parentPrefix = self::$prefix;
        $parentGroupMiddlewares = self::$groupMiddlewares;

        if (is_array($attributes))
        {
            if (isset($attributes['prefix'])) {
                self::$prefix .= $attributes['prefix'];
            }
            if (isset($attributes['middleware'])) {
                $middlewares = is_array($attributes['middleware']) ? $attributes['middleware'] : [$attributes['middleware']];
                self::$groupMiddlewares = array_merge(self::$groupMiddlewares, [$middlewares]);
            }
        }
        else
        {
            self::$prefix .= $attributes;
        }

        call_user_func($callback);

        self::$prefix = $parentPrefix;
        self::$groupMiddlewares = $parentGroupMiddlewares;
    }

    public static function where($param, $regex): Route
    {
        if (self::$currentRoute !== null) {
            self::$routes[self::$currentRoute]['where'][$param] = $regex;
        }
        return new self();
    }

    public static function view(string $path, string $viewName, array $data = []): Route
    {
        return self::addRoute('GET', $path, function($params = []) use ($viewName, $data) {
            View::render($viewName, array_merge($data, $params));
        });
    }

    public static function api(string $path, string $apiName, array $data = []): Route
    {
        return self::addRoute('POST', $path, function($params = []) use ($apiName, $data) {
            extract(array_merge($data, $params));
            require resources_path("api\\$apiName.php");
        });
    }

    private static function getUri() {
        return $_GET['route'] ?? '/';
    }

    /**
     * @throws \Exception
     */
    public static function run(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $path = self::getUri();
        foreach (self::$routes as $route) {
            if ($route['method'] === $method && self::match($path, $route['path'], $params)) {
                if (self::validateParams($params, $route['where'])) {
                    foreach ($route['middlewares'] as $middleware) {
                        if (is_callable($middleware)) {
                            call_user_func($middleware);
                        } elseif (is_array($middleware) && class_exists($middleware[0])) {
                            $middleware_params = array_slice($middleware, 1);
                            if (empty($middleware_params))
                            {
                                $middleware_params = array();
                            }
                            elseif (is_array($middleware_params[0]))
                            {
                                $middleware_params = $middleware_params[0];
                            }
                            (new $middleware[0])->handle($middleware_params);
                        } elseif (class_exists($middleware)) {
                            (new $middleware)->handle([]);
                        } else {
                            throw new \Exception("Invalid middleware configuration");
                        }
                    }
                    echo call_user_func_array($route['callback'], [$params]);
                    return;
                }
            }
        }

        if ($method !== 'GET') {
            showError(405, 405);
        } else {
            showError(404, 404);
        }
    }

    public static function redirect($old_url, $new_url): Route
    {
        return self::addRoute('GET', $old_url, function () use ($new_url){
            if (filter_var($new_url, FILTER_VALIDATE_URL) === FALSE) {
                redirect(url($new_url));
            } else {
                redirect($new_url);
            }

        });
    }

    private static function match($requestPath, $routePath, &$params): bool
    {
        $requestParts = explode('/', trim($requestPath, '/'));
        $routeParts = explode('/', trim($routePath, '/'));

        if (count($requestParts) !== count($routeParts)) {
            return false;
        }

        $params = [];
        foreach ($routeParts as $key => $part) {
            if (preg_match('/^{\w+}$/', $part)) {
                $paramName = trim($part, '{}');
                $params[$paramName] = $requestParts[$key];
            } elseif ($requestParts[$key] !== $part) {
                return false;
            }
        }

        return true;
    }

    private static function validateParams($params, $conditions): bool
    {
        foreach ($conditions as $key => $regex) {
            if (!isset($params[$key]) || !preg_match($regex, $params[$key])) {
                return false;
            }
        }
        return true;
    }

    /**
     * @throws \Exception
     */
    public static function name(string $name): Route
    {
        if (self::$currentRoute !== null) {
            if (!isset(self::$namedRoutes[$name]))
            {
                $route = self::$routes[self::$currentRoute];
                $route['name'] = $name;
                self::$routes[self::$currentRoute] = $route;
                self::$namedRoutes[$name] = $route;
            }
            else
            {
                throw new \Exception('The Route name "'. $name .'" already exists. Choose another name');
            }
        }
        return new self();
    }
}
