<?php
  require __DIR__.'/includes/app.php';
  use \App\Http\Router;
  
  
  $router = new Router(URL);
  include __DIR__.'/routes/web.php';
  $router->run()->sendResponse();