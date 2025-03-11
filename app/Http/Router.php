<?php 

namespace App\Http;

use \Closure;
use \Exception;

class Router{

    private $url = '';
    private $prefix = '';
    private $routes = [];
    private $request;

    public function __construct($url){
        $this->request = new Request();
        $this->url     = $url;
        $this->setPrefix();
    }

    private function setPrefix(){
        $parseUrl = parse_url($this->url);
        $this->prefix = $parseUrl['path'] ?? '';
    }

    public function addRoute($method, $route, $params = []){
        foreach($params as $keys => $values){
            if($values instanceof Closure){
                $params['controller'] = $values;
                unset($params[$keys]);
                continue;
            }
        }

        $patternRoute = '/^'.str_replace('/', '\/', $route).'$/';
        $this->routes[$patternRoute][$method] = $params; 
    }

    public function get($route, $params = []){
        return $this->addRoute('GET', $route, $params);
    }

    public function post($route, $params = []){
        return $this->addRoute('POST', $route, $params);
    }

    public function put($route, $params = []){
        return $this->addRoute('PUT', $route, $params);
    }

    public function delete($route, $params = []){
        return $this->addRoute('DELETE', $route, $params);
    }
    
    private function getUri(){
        $uri = $this->request->getUri();
        $xUri = strlen($this->prefix) ? explode($this->prefix, $uri) : [$uri]; 
        return end($xUri);
    }

    private function getRoute(){
        $uri = $this->getUri();

        $httpMethod = $this->request->getHttpMethod();

        foreach($this->routes as $patternRoutes=>$methods){
            if(preg_match($patternRoutes, $uri)){
                if($methods[$httpMethod]){
                    return $methods[$httpMethod];
                }
                throw new Exception("Método não permitido", 405);
            }
        }

        throw new Exception("URL não encontrada", 404);
    }

    public function run(){
        try{
            $route = $this->getRoute();
            
            if(!isset($route['controller'])){
                throw new Exception("A URL não pode ser processada", 500);
            }

            $args = []; 

            return call_user_func_array($route['controller'], $args);

        }catch(Exception $e){
             return new Response($e->getCode(), $e->getMessage());
        }
    }
}

?>