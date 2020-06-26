<?php
declare (strict_types = 1);
require_once __DIR__ . "/vendor/autoload.php";

use memCrab\Exceptions\FileException;
use memCrab\Exceptions\RoutingException;
use memCrab\File\Yaml;
use memCrab\Router\Router;

try {

	# Read routes from yaml
	$Yaml = new Yaml();

	$routes = $Yaml->load("../src/routs.example.yaml", null)->getContent();

	# For enable cache You can use FileCache object as second parametr of
	# $Yaml->load() function. Use memCrab\Cache library for it.
	# Redis Cache: $FileCache = new RedisCache([Redis obj]);
	# PHP file Cache: $FileCache = new PHPCache([PathToTMLFolder]);

	# Initialize Router
	$Router = new Router();
	$Router->loadRoutes($routes);

	# Routing
	$Router = $Router->disposeData("http://example.com/post/", "POST");
	# Run your Controller|Service|Component
	$ServiceName = $Router->getService();
	$ActionName = $Router->getAction();
	$Service = new $ServiceName();
	$Response = $Service->$Action($Router->getParams());
} catch (RoutingException $error) {
	$Respose = new \Response();
	$Respose->setErrorResponse($error);
} catch (FileException $error) {
	$Respose = new \Response();
	$Respose->setErrorResponse($error);
#  test here
}

$Respose->sendHeaders();
$Respose->sendContent();
