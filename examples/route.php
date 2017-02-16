<?php
declare (strict_types = 1);
require_once __DIR__ . "/../vendor/autoload.php";

use memCrab\Exceptions\FileException;
use memCrab\Exceptions\RoutingException;
use memCrab\Router\Router;

try {
	# Initialize Router
	$Yaml = new Yaml();
	$routes = $Yaml->load("../src/routs.example.yaml", null)->getContent();

	$Router = new Router();
	$Router->loadRoutes($routes);

	# Routing
	$Router->matchRoute("http://example.com/post/", "POST");

	# Run your Controller|Service|Component
	$ServiceName = $Router->getService();
	$Service = new $ServiceName();
	$Action = $Router->getAction();
	$Response = $Service->$Action($Router->getParams());
} catch (RoutingException $error) {
	$Respose = new \Response();
	$Respose->setErrorResponse($error);
} catch (FileException $error) {
	$Respose = new \Response();
	$Respose->setErrorResponse($error);
}

$Respose->sendHeaders();
$Respose->sendContent();