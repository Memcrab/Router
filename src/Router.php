<?php
declare (strict_types = 1);
namespace memCrab\Router;
use memCrab\Exceptions\RoutingException;

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

	public function loadRoutes(array $routes) {
		if (empty($routes)) {
			throw new RoutingException(_("Empty routes"), 1);
		}

		$this->routes = $routes;
	}

	public function matchRoute(string $rawUrl, string $method): void{
		$url = parse_url($rawUrl);
		if (!isset($url['path']) || is_string($url['path']) === false) {
			throw new RoutingException(_("Router can't parse request."), 400);
		}

		if (!is_array($this->routes)) {
			throw new RoutingException(_("Can't find any routes rules."), 501);
		}

		foreach ($this->routes as $regExpString => $route) {
			$routes = 0;
			$result = preg_match("/^" . str_replace("/", "\/", $regExpString) . "$/u", $url['path'], $matches);
			if ($result === 0) {
				continue;
			} elseif ($result === false) {
				throw new RoutingException(_("Can't parse route RegExp. ") . $regExpString, 501);
			} elseif ($result === 1) {
				if (isset($route[$method])) {
					$this->serviceName = $route[$method][0];
					$this->actionName = $route[$method][1];

					$paramsCount = count($route[$method]) - 2;
					if ($paramsCount > 0) {
						for ($i = 0; $i < $paramsCount; $i++) {
							$this->params[$route[$method][$i + 2]] = $matches[$i + 1];
						}
					} else {
						$this->params = null;
					}

					$routes++;
					break;
				}
			}
		}

		if ($routes === 0) {
			throw new RoutingException(_("Not found route: ") . $method . " " . $rawUrl, 404);
		}

		if ($routes > 1) {
			throw new RoutingException(_("Conflict. Multiple routes found."), 501);
		}
	}

	public function getParams():  ? array{
		return $this->params;
	}

	public function getService() : string {
		return $this->serviceName;
	}

	public function getAction():  ? string {
		return $this->actionName;
	}

	public function getErrorMessage() :  ? string {
		return $this->errorMessage;
	}

	public function getErrorServiceName() : string {
		return $this->errorServiceName;
	}
}
