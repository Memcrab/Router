<?php
declare(strict_types=1);
require_once __DIR__ . "/../vendor/autoload.php";

use memCrab\Router\Router;
use memCrab\Router\RouterException;

try {
	# Initialize Router
	$Router = new Router();
	$Router->loadRoutesFromYaml("../src/routs.example.yaml");
	
	# Routing
	$Router->matchRoute("http://example.com/post/", "POST");		
	
	# Run your Controller|Service|Component
	$ServiceName = $Router->getService();
	$Service = new $ServiceName();
	$Action = $Router->getAction();
	$Response = $Service->$Action($Router->getParams());
}
catch(RouterException $error){
	$Respose = new \Response();
	$Respose->setErrorResponse($error);
}

$Respose->sendHeaders();
$Respose->sendContent();