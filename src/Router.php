<?php
declare(strict_types=1);
namespace memCrab\Router;

/**
*  Router for core project
*
*  @author Oleksandr Diudiun
*/
class Router {
	private $routes;
	private $params;
	private $actionName;
	private $serviceName;
	private $errorMessage;
	private $errorServiceName;

	function __construct() {}

	public function loadRoutesFromYaml(string $filePath) {
		if(file_exists($filePath) === false)
			throw new RouterException(_("Router can't find routes file."), 501);
		if(is_readable($filePath) === false)
			throw new RouterException(_("Router can't open file, permission denied."), 501);

		$result = yaml_parse_file($filePath, 0);

		if($result === false)
			throw new RouterException(_("Router can't parse routes from file."), 501);
		if(!isset($result['routes']) || !is_array($result['routes']) || empty($result['routes']))
			throw new RouterException(_("Bad syntax of routing Yaml file."), 501);

		$this->routes = $result['routes'];
	}

	public function matchRoute(string $url, string $method) : void {
		$url = parse_url($url);
		if( !isset($url['path']) || is_string($url['path']) === false )
			throw new RouterException(_("Router can't parse request."), 400);
		if (!is_array($this->routes))
			throw new RouterException(_("Can't find any routes rules."), 501);
		foreach ($this->routes as $regExpString => $route) {
			$routes = 0;
			$result = preg_match("/^" . str_replace("/", "\/", $regExpString) . "$/u", $url['path'], $matches);
			if ($result === 0) continue;
			elseif ($result === false)
				throw new RouterException(_("Can't parse route RegExp.") . $regExpString, 501);
			elseif ($result === 1) {
				if(isset($route[$method])) {
					$this->serviceName = $route[$method][0];
					$this->actionName = $route[$method][1];

					$paramsCount = count($route[$method]) - 2;
					if($paramsCount > 0){
						for($i = 0; $i < $paramsCount; $i++){
							if (preg_match("/^([$])([0-9]+)$/", $route[$method][$i+2],$matches2))
                						$this->params[$matches[$matches2[2]]] = $matches[$i + 2];
              						else
						                $this->params[$route[$method][$i+2]] = $matches[$i + 1];
					}
					else $this->params = null;

					$routes++;
					break;
				}
			}
		}

		if($routes === 0) throw new RouterException(_("Not found"), 404);
		if($routes > 1) throw new RouterException(_("Conflict. Multiple routes found."), 501);
	}

	public function getParams() : ?array {
		return $this->params;
	}

	public function getService() : string {
		return $this->serviceName;
	}

	public function getAction() : ?string {
		return $this->actionName;
	}

	public function getErrorMessage() : ?string {
		return $this->errorMessage;
	}

	public function getErrorServiceName() : string {
		return $this->errorServiceName;
	}
}
