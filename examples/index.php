<?php
declare(strict_types=1);
require_once __DIR__ . "/../vendor/autoload.php";

# Initialize Router
$Router = new \memCrab\Router\Router("../src/routs.example.yaml", "Error");
$Router->matchRoute("http://example.com/post/", "POST");

# Run your Controller|Service|Component
$ServiceName = $Router->getService();
$Service = new $ServiceName;
$Action = $Router->getAction();
$Service->$Action($Router->getParams());