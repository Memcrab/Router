PHP Router as Composer Library 
==========================
### Status
[![Build Status](https://travis-ci.org/noonehos/router.svg?branch=master)](https://travis-ci.org/noonehos/router)

It's php router based on yaml configuration file and support regular expressions in each route condition. 
Thats help build more accurate routes with only numbers in part of url or with required part of word etc.

Features
--------

* Support RegExp in any kind of route
* All configurations in simple YAML file
* Each route can return already named params (as many params as you want, or as you have in Regular Expression)
* High performance yaml parse throw updated pecl yaml-ext 2.0.0 for php 7.0
* Strict standart coding with full Typing of params and returns (by php 7.1)
* PSR-4 autoloading compliant structure
* Unit-Testing with PHPUnit
* Easy to use to any framework

Install
--------
`composer install memcrab/router`

## TODOS

- [ ] Add support for suffixes - right part of uri that not involved in routing like .html, .php, last "/", etc
- [ ] Add support for prefixes - left part of uri that not involved in routing like lang part (uk/us/fr/ru) or geo part (europe/asia), etc

---

**MIT Licensed**
