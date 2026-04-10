<?php
  require __DIR__.'/vendor/autoload.php';

  use \App\Controller\HomeController;

  echo HomeController::getHome();
