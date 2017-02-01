<?php 
require_once __DIR__ . "/../vendor/autoload.php";

use memCrab\Router\Router;
use memCrab\Router\RouterException;

/**
*  Corresponding Class to test Router class
*
*  For each class in your library, there should be a corresponding Unit-Test for it
*  Unit-Tests should be as much as possible independent from other test going on.
*
*  @author Oleksandr Diudiun
*/
class RouterTest extends PHPUnit_Framework_TestCase { 
  protected $Router;
  protected $yaml;

  protected function setUp() {
    $this->yaml = __DIR__ . "/../src/routs.example.yaml"; 
    $this->Router = new Router();
    $this->routesExample = array(
      "routes" => array(
        "/"=>array("GET"=>array("Index", "getMain")),
        "/post/"=>array(
          "GET"=>    array("Post", "get"),
          "POST"=>   array("Post", "add"),
          "PATCH"=>  array("Post", "save"),
          "DELETE"=> array("Post", "delete")
        ),
        "/post/publish/" => array("POST" => array("Post", "setPublishing")),
        "/catalog/([a-zA-Z0-9]+)-([a-zA-Z0-9]+)/" => array(
          "GET" => array("Catalog", "filter", "key1", "value1")
        )
      )
    );
  }

  protected function tearDown() {
    unset($this->Router);
    unset($this->yaml);
    unset($this->routesExample);
  }
  
  public function routesProvider () {
    return array (
      array ("http://example.com/", "GET", "Index", "getMain", null),
      array ("http://example.com/post/", "GET", "Post", "get", null),
      array ("http://example.com/post/", "POST", "Post", "add", null), 
      array ("http://example.com/post/", "PATCH", "Post", "save", null), 
      array ("http://example.com/post/", "DELETE", "Post", "delete", null),
      array ("/post/", "DELETE", "Post", "delete", null),
      array ("http://example.com/post/publish/", "POST", "Post", "setPublishing", null), 
      array ("http://example.com/catalog/brand-nike/", "GET", "Catalog", "filter", array("key1"=>"brand", "value1"=>"nike")),
    ); 
  }

  public function testRoutesFileExist() {
    $this->assertFileExists(__DIR__ . "/../src/routs.example.yaml", "routs.example.yaml not found");
  }
  // array ("http://example.com/post/bla-bla/", "get", "Error", "404", null)

  // public function testLoadRoutesFromYaml() {
  //   $this->Router->loadRoutesFromYaml($this->yaml);
  // }

  public function testSuccessRoutesFileParsing() {
    $fileRoutes = yaml_parse_file($this->yaml, 0);
    $this->assertEquals($this->routesExample, $fileRoutes);
  }

  /**
   * @dataProvider routesProvider
   */
  public function testParsedRoutes(string $url, string $method, string $service, string $action, ?array $params) {
    $this->Router->loadRoutesFromYaml($this->yaml);
    $this->Router->matchRoute($url, $method);

    $this->assertEquals($this->Router->getService(), $service);
    $this->assertEquals($this->Router->getAction(), $action);
    $this->assertEquals($this->Router->getParams(), $params);
  }
}