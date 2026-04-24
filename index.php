<?php
  require __DIR__.'/includes/app.php';
  use \App\Http\Router;
  
  
  $router = new Router(URL);
  
   include __DIR__.'/routes/web.php';
   include __DIR__.'/routes/admin.php';
  
  $router->run()->sendResponse();