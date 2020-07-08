<?php declare (strict_types = 1);
namespace memCrab\Router;
use memCrab\Exceptions\RoutingException;

/**
 *  Router for core project
 *
 *  @author Oleksandr Diudiun
 */
class Router {
    protected static $routes;
    
    private $params;
    private $actionName;
    private $serviceName;
    private $errorMessage;
    private $errorServiceName;

    private function __clone() {}
    private function __wakeup() {}
    private function __construct(string $serviceName = null, string $actionName = null, array $params = []) {
        $this->serviceName = $serviceName;
        $this->actionName = $actionName;
        $this->params = $params;
    }

    public static function loadRoutes(array $routes, string $environment) {
        if (empty($routes)) {
            throw new RoutingException(_("Empty routes"), 501);
        }
        self::$routes[$environment] = $routes;
    }

    public static function getHandledRouter(string $rawUrl, string $method, string $environment):  ? self{
        $url = parse_url($rawUrl);
        $routeData = [];

        if (!isset($url['path']) || is_string($url['path']) === false) {
            throw new RoutingException(_("Router can't parse request."), 400);
        }

        if (!empty(self::$routes[$environment]) && !is_array(self::$routes[$environment])) {
            throw new RoutingException(_("Can't find any routes rules."), 501);
        }

        $routesCount = 0;
        foreach (self::$routes[$environment] as $regExpString => $route) {
            $result = preg_match("/^" . str_replace("/", "\/", $regExpString) . "$/u", $url['path'], $matches);
            if ($result === 0) {
                continue;
            } elseif ($result === false) {
                throw new RoutingException(_("Can't parse route RegExp. ") . $regExpString, 501);
            } elseif ($result === 1 && isset($route[$method])) {
                $routeData['serviceName'] = $route[$method][0];
                $routeData['actionName'] = $route[$method][1];

                $paramsCount = count($route[$method]) - 2;
                if ($paramsCount > 0) {
                    for ($i = 0; $i < $paramsCount; $i++) {
                        $routeData['params'][$route[$method][$i + 2]] = $matches[$i + 1];
                    }
                } else {
                    $routeData['params'] = array();
                }

                $routesCount++;
                break;
            }
        }

        if ($routesCount > 1) {
            throw new RoutingException(_("Conflict. Multiple routes found."), 501);
        }
        if ($routesCount === 0) {
            throw new RoutingException(_("Route not found."), 501);
        }

        return new self($routeData['serviceName'], $routeData['actionName'], $routeData['params']);
    }

    public function getParams(): ? array{
        return $this->params;
    }

    public function getService(): ? string{
        return $this->serviceName;
    }

    public function getAction(): ? string{
        return $this->actionName;
    }

    public function getErrorServiceName(): ? string{
        return $this->errorServiceName;
    }

    public function getErrorMessage(): ? string {
        return $this->errorMessage;
    }
}
