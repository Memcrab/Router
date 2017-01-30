<?php
declare(strict_types=1);
require_once __DIR__ . "/vendor/autoload.php";

# Initialize Router
$Router = new \memCrab\Router("../src/routs.example.yaml", "Error");
$Router->matchRoute($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);

# Run your Controller|Service|Component
$Service = new $Router->getSrvice();
$Action = $Router->getAction();
$Service->$Action($Router->getParams());