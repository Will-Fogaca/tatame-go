<?php

namespace App\Controllers\Pages;

use \App\Utils\View;
use \App\Utils\Page;

class HomeController {

  /**
  *Método responsável por retornar o conteúdo (view) da nossa home
  * @return string
  */
  public static function getHome(){
    
    $content = View::render('shared/index', []);

    return Page::getPage('Home', $content);
  }


}
