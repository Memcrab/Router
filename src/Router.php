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
		if(!file_exists($filePath)) 
			throw new RouterException("Router can't find routes file.", 501);

		$result = yaml_parse_file($filePath, 0);
		
		if($result === false) 
			throw new RouterException("Router can't parse routes from file.", 501);
		
		if(!isset($result['routes']) || !is_array($result['routes']) || empty($result['routes']))
			throw new RouterException("Bad syntax of routing Yaml file", 501);	

		$this->routes = $result['routes'];
	}

	public function matchRoute(string $url, string $method) : void {
		$requestUri = parse_url($url)['path'];
		if (!is_array($this->routes))
			throw new RouterException("Can't find any routes rules.", 501);
		foreach ($this->routes as $regExpString => $route) {
			$routes = 0;
			$result = preg_match("/^" . str_replace("/", "\/", $regExpString) . "$/u", $requestUri, $matches);
			if ($result === 0) continue;
			elseif ($result === false)
				throw new RouterException("Can't parse route RegExp " . $regExpString, 501);
			elseif ($result === 1) {
				if(isset($route[$method])) {
					$this->serviceName = $route[$method][0];
					$this->actionName = $route[$method][1];
			
					$paramsCount = count($route[$method]) - 2;
					if($paramsCount > 0)
						for($i = 0; $i < $paramsCount; $i++)
							$this->params[$route[$method][$i+2]] = $matches[$i + 1];
					
					$routes++;
					break;
				}
			}
		}

		if($routes === 0) throw new RouterException("Not found", 404);
		if($routes > 1) throw new RouterException("Conflict. Multiple routes found.", 501);
	}

	public function getParams() : ?array {
		return (empty($this->params)? []:$this->params;
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
