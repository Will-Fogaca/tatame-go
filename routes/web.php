<?php

  use App\Controllers\AboutController;
  use \App\Http\Response;
  use \App\Controllers\HomeController;
  use App\Controllers\StudentController;
  use App\Http\Router;


  $router->get('/', [
      function($request) { 
        return new Response(200, HomeController::getHome());
      }
  ]);

  $router->get('/aluno', [
    function($request){
      return new Response(200, StudentController::getIndex($request));
    }
  ]);

  $router->post('/aluno/cadastro', [
    function($request){
      return new Response(200, StudentController::getStore($request));
    }
  ]);

  $router->get('/aluno/cadastro', [
    function($request){
      return new Response(200, StudentController::getStore($request));
    }
  ]);
  
