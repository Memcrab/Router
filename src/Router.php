<?php 
declare(strict_types=1);
namespace memCrab;

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

	function __construct(string $filePath, string $errorServiceName) {
		$this->errorServiceName = $errorServiceName;
		$result = yaml_parse_file($filePath, 0);
		if($result === false) 
			throw new \Exception("Router can't parse routes from file.", 501);
		$this->routes = $result['routes'];
	}

	public function matchRoute(string $url, string $method) : void {
		try {
			$requestUri = parse_url($url)['path'];
			foreach ($this->routes as $regExpString => $route) {
				$routes = 0;
				$result = preg_match("/^" . str_replace("/", "\/", $regExpString) . "$/u", $requestUri, $matches);
				if ($result === 0) continue;
				elseif ($result === false)
					throw new \Exception("Can't parse route RegExp " . $regExpString, 501);
				elseif ($result === 1) {
					if(isset($route[$method])) {
						$this->serviceName = $route[$method]['route'][0];
						$this->actionName = $route[$method]['route'][1];
				
						if(isset($route[$method]['matches']))
							foreach ($route[$method]['matches'] as $matchKey => $paramName)
								$this->params[$paramName] = $matches[$matchKey + 1];
						else $this->params = null;
						
						$routes++;
						break;
					}
				}
			}

			if($routes === 0) throw new \Exception("Not found", 404);
			if($routes > 1) throw new \Exception("Conflict. Multiple routes found.", 501);

		} catch(\Exception $e) {
			$this->serviceName = $this->errorServiceName;
			$this->actionName = (string) $e->getCode();
			$this->errorMessage = $e->getMessage();
		}
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