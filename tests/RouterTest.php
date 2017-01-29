<?php 
require_once __DIR__ . "/../vendor/autoload.php";
require_once __DIR__ . "/../src/Router.php";
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
    $this->Router = new memCrab\Router(
      $this->yaml, 
      "Error"
    );
  }

  protected function tearDown() {
    unset($this->Router);
    unset($this->yaml);
  }
  
  //TODO check to file example exist
  //TODO check to result of file an array

  public function routesProvider () {
    return array (
      array ("/", "GET", "Index", "getMain", null),
      array ("/post/", "GET", "Post", "get", null),
      array ("/post/", "POST", "Post", "add", null), 
      array ("/post/", "PATCH", "Post", "save", null), 
      array ("/post/", "DELETE", "Post", "delete", null),
      array ("/post/publish/", "POST", "Post", "setPublishing", null), 
      array ("/catalog/brand-nike/", "GET", "Catalog", "filter", array("key1"=>"brand", "value1"=>"nike")),
      array ("/post/bla-bla/", "get", "Error", "404", null)
    ); 
  }

  public function testRoutesFileExist() {
    $this->assertFileExists(__DIR__ . "/../src/routs.example.yaml", "routs.example.yaml not found");
  }

  public function testSuccessRoutesParsing() {
    $file = yaml_parse_file($this->yaml, 0);
    $this->assertFalse($file === false, "routs.example.yaml has are syntax error.");
    $this->assertArrayHasKey('routes', $file);
    $this->assertArrayHasKey('/', $file['routes']);
    $this->assertArrayHasKey('/post/', $file['routes']);
    $this->assertArrayHasKey('/post/publish/', $file['routes']);
    $this->assertArrayHasKey('/catalog/([a-zA-Z0-9]+)-([a-zA-Z0-9]+)/', $file['routes']);
  }

  /**
   * @dataProvider routesProvider
   */
  public function testParsedRoutes(string $url, string $method, string $service, string $action, ?array $params) {
    $this->Router->matchRoute($url, $method);

    $this->assertEquals($this->Router->getService(), $service);
    $this->assertEquals($this->Router->getAction(), $action);
    $this->assertEquals($this->Router->getParams(), $params);
  }
}