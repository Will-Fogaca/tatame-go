<?php

namespace App\Http;
use \Closure;
use \Exception;
use \ReflectionFunction;


class Router{
    
    /**
     * URL completa do projeto (raiz)
     * @var string
     */
    private $url = '';


    /**
     * Prefixo de todas as rotas
     * @var string
     */
    private $prefix = '';
    
    /**
     * Índice de rotas 
     * @var array
     */
    private $routes = [];

    
    /**
     * Instância de Request
     * @var Request
     */
    private $request;

    /**
     * Construtor da classe
     * @param string
     */
    public function __construct($url = '')
    {
        $this->request = new Request($this);
        $this->url = $url;
        $this->setPrefix();
    }

    /**
     * Método responsável por definir o prefixo das rotas
     */

    private function setPrefix(){
        $parseUrl = parse_url($this->url);
        $this->prefix = $parseUrl['path'] ?? '';
    }

    /**
     * Método responsável por adicionar uma rota
     * @param string $method
     * @param string $route
     * @param array  $params
     */
    private function addRoute($method, $route, $params = []){

        foreach($params as $key=>$value){
            if($value instanceof Closure){
                $params['controller'] = $value;
                unset($params[$key]);
                continue;
            }
        }
        $params['variables'] = [];
        $patternVariable = '/{(.*?)}/';
        if(preg_match_all($patternVariable, $route, $matches)){
           $route = preg_replace($patternVariable, '(.*?)', $route);
           $params['variables'] = $matches[1];
        }

        $patternRoute = '/^'.str_replace('/', '\/', $route).'$/';
        $this->routes[$patternRoute][$method] = $params;
    }

    /**
     * Método responsável por retornar a URI sem o prefixo
     * @return string
     */
    private function getUri(){
        $uri = $this->request->getUri();
        $xUri = strlen($this->prefix) ? explode($this->prefix, $uri) : [$uri];
        return end($xUri);
    }

    /**
     * Método responsável por retornar os dados da rota atual
     * @return array
    */
    private function getRoute(){
        $uri = $this->getUri();
        $httpMethod = $this->request->getHttpMethod();
        foreach($this->routes as $patternRoute => $methods){
            if(preg_match($patternRoute, $uri, $matches)){
                if(isset($methods[$httpMethod])){
                    unset($matches[0]);

                    $keys = $methods[$httpMethod]['variables'];
                    $methods[$httpMethod]['variables'] = array_combine($keys, $matches);
                    $methods[$httpMethod]['variables']['request'] = $this->request;

                    return $methods[$httpMethod];
                }
                throw new Exception('Método não permitido', 405);
            }
        }

        throw new Exception('URL não encontrada', 404);
    }

    /**
     * Método responsável por executar a rota atual
     * @return Response
     */
    public function run(){
        try{
            $route = $this->getRoute();
            if(!isset($route['controller'])){
                throw new Exception("A URL não pôde ser processada.", 500);
            }
            $args = [];

            $reflection = new ReflectionFunction($route['controller']);
            foreach($reflection->getParameters() as $parameter){
                $name = $parameter->getName();
                $args[$name] = $route['variables'][$name] ?? '';
            }

            return call_user_func_array($route['controller'], $args);
        }catch(Exception $e){
            return new Response($e->getCode(), $e->getMessage());
        }

    }


    /**
     * Método responsável por definir uma rota GET
     * @param string $route
     * @param array $params
     */
    public function get($route, $params = []){
        return $this->addRoute('GET', $route, $params);
    }

    /**
     * Método responsável por definir uma rota POST
     * @param string $route
     * @param array $params
     */
    public function post($route, $params = []){
        return $this->addRoute('POST', $route, $params);
    }

    /**
     * Método responsável por definir uma rota PUT
     * @param string $route
     * @param array $params
     */
    public function put($route, $params = []){
        return $this->addRoute('PUT', $route, $params);
    }

    /**
     * Método responsável por definir uma rota DELETE
     * @param string $route
     * @param array $params
     */
    public function delete($route, $params = []){
        return $this->addRoute('DELETE', $route, $params);
    }
}