PHP Router as Composer Library 
==========================
### Status
[![Build Status](https://travis-ci.org/noonehos/router.svg?branch=master)](https://travis-ci.org/noonehos/router)

It's php router based on yaml configuration file and support regular expressions in each route condition. 
Thats help build more accurate routes with only numbers in part of url or with required part of word etc.

Features
--------

* Support RegExp in any kind of route
* Support multiple routings for single url throw different request methods (POST, GET, PUT, DELETE, ...)
* Support full url or just request uri
* All configurations in simple YAML file
* Each route can return already named params (as many params as you want, or as you have in Regular Expression)
* High performance yaml parse throw using updated pecl yaml-ext 2.0.0 for php 7.0
* Strict standart coding with full Typing of params and returns (by php 7.1)
* PSR-4 autoloading compliant structure
* Unit-Testing with PHPUnit
* Easy to use to any framework

Install
--------
`composer install memcrab/router`

Dependencies
--------
- php extension YAML: `pecl install yaml-2.0.0`

Usage
--------
- `new \memCrab\Router("path/to/yaml/file", "ErrorClassName");`

Simple Example
--------
```php
require_once __DIR__ . "/vendor/autoload.php";

# Initialize Router
$Router = new \memCrab\Router("../src/routs.example.yaml", "Error");
$Router->matchRoute($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);

# Run your Controller|Service|Component
$Service = new $Router->getSrvice();
$Action = $Router->getAction();
$Service->$Action($Router->getParams());
```

## TODOS

- [ ] Add support for suffixes - right part of uri that not involved in routing like .html, .php, last "/", etc
- [ ] Add support for prefixes - left part of uri that not involved in routing like lang part (uk/us/fr/ru) or geo part (europe/asia), etc

---
**MIT Licensed**
