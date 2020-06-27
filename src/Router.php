<?php declare (strict_types = 1);
namespace memCrab\Router;
use memCrab\Exceptions\RoutingException;

/**
 *  Router for core project
 *
 *  @author Oleksandr Diudiun
 */
class Router {
    private $requestsCounter = 0;
    
    private $routes;
    private $data;
    private $time;

    function __construct() {}
    
    public function loadRoutes(array $routes) {
        if (empty($routes)) {
            throw new RoutingException(_("Empty routes"), 1);
        }

        $this->routes = $routes;
    }

    public function setRouteDataByHash(string $rawUrl, string $method, int $seconds):  ? string{
        $requestsCounter++;
        
        $url = parse_url($rawUrl);
        $routeData = [];

        if (!isset($url['path']) || is_string($url['path']) === false) {
            throw new RoutingException(_("Router can't parse request."), 400);
        }

        if (!is_array($this->routes)) {
            throw new RoutingException(_("Can't find any routes rules."), 501);
        }

        $routesCount = 0;

        foreach ($this->routes as $regExpString => $route) {
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

        $hash = $routeData['serviceName']."_".$routeData['actionName']."_".$requestsCounter;
        $this->data[$hash] = $routeData;
        $this->timer[$hash] = time() + (int) $seconds;

        return $hash;
    }

    public function getParams(string $key):  ? array{
        return !empty($this->data[$key]["params"]) ? $this->data[$key]["params"] : [];
    }

    public function getService(string $key) : string {
        return !empty($this->data[$key]["serviceName"]) ? $this->data[$key]["serviceName"] : "";
    }

    public function getAction(string $key):  ? string {
        return !empty($this->data[$key]["actionName"]) ? $this->data[$key]["actionName"] : "";
    }

    public function getErrorServiceName(string $key) : string {
        return !empty($this->data[$key]["errorServiceName"]) ? $this->data[$key]["errorServiceName"] : "";
    }

    public function getErrorMessage(string $key):  ? string {
        return !empty($this->data[$key]["errorMessage"]) ? $this->data[$key]["errorMessage"] : "";
    }
    
    public function deleteExpiredData(){
        foreach($this->timer as $time => $hash){
            if($time < time()){
                unset($data[$hash]);
            }
        }
    }
}
