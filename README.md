PHP Router as Composer Library 
==========================
### Status
[![Build Status](https://travis-ci.org/noonehos/router.svg?branch=master)](https://travis-ci.org/noonehos/router)
[![Dependency Status](https://www.versioneye.com/user/projects/588f90c1760ce6003a4ea676/badge.svg?style=flat-square)](https://www.versioneye.com/user/projects/588f90c1760ce6003a4ea676)
[![Total Downloads](https://poser.pugx.org/memcrab/router/downloads)](https://packagist.org/packages/memcrab/router)
[![Latest Stable Version](https://poser.pugx.org/memcrab/router/version)](https://packagist.org/packages/memcrab/router)
[![Latest Unstable Version](https://poser.pugx.org/memcrab/router/v/unstable)](//packagist.org/packages/memcrab/router)
[![License](https://poser.pugx.org/memcrab/router/license)](https://packagist.org/packages/memcrab/router)
[![composer.lock available](https://poser.pugx.org/memcrab/router/composerlock)](https://packagist.org/packages/memcrab/router)


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
```composer require memcrab/router```

Dependencies
--------
php extension YAML:
- for Ubuntu/Debian 
```
- apt-get update
- apt-get install php-pear
- apt-get install php-dev
- apt-get install php-xml php7.0-xml
- apt-get install libyaml-dev
- pecl channel-update pecl.php.net
- pecl install yaml-2.0.0
```
- for OS X
```
- brew install php71 --with-pear
- brew install autoconf
- touch $(brew --prefix php71)/lib/php/.lock && chmod 0644 $(brew --prefix php71)/lib/php/.lock
- pecl install yaml-2.0.0
```

Usage
--------
- init Router: `memCrab\Router()`
- load routes: `->loadRoutesFromYaml(string $filePath)`
	- $filePath - Path to yaml files with routes
- run matching: `->matchRoute(string $url, string $method)`
	- $url - URL (`http://example.com/posts`) or just request URI of page (`/post`)
	- $method - http request method
- use your router data with:
	- getService() - return component that we call
	- getAction() - return action that will be run from component
	- getParams() - return route regExp params

Yaml Config Example
--------
```yaml
/:
  GET: [Index, getMain]
/post/:
  GET:    [Post, get]
  POST:   [Post, add]
  PATCH:  [Post, save]
  DELETE: [Post, delete]
/post/publish/:
  POST: [Post, setPublishing]
/catalog/([a-zA-Z0-9]+)-([a-zA-Z0-9]+)/: 
  GET: [Catalog, filter, key1, value1]
```


Run Example
--------
```php
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
  $Router->matchRoute("http://example.com/post/", "POST");
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
}

$Respose->sendHeaders();
$Respose->sendContent();
```

## TODOS

- [ ] Add support for suffixes - right part of uri that not involved in routing like .html, .php, last "/", etc
- [ ] Add support for prefixes - left part of uri that not involved in routing like lang part (uk/us/fr/ru) or geo part (europe/asia), etc

---
**MIT Licensed**
