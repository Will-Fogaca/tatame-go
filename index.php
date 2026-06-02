<?php
  require __DIR__.'/includes/app.php';
  use \App\Http\Router;
  
  
  $router = new Router(URL);
  
   include __DIR__.'/routes/web.php';
   include __DIR__.'/routes/admin.php';
   include __DIR__.'/routes/user.php';
  
  $router->run()->sendResponse();