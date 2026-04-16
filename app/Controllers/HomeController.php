<?php

namespace App\Controllers;
use \App\Utils\View;
use \App\Models\Academy;

class HomeController extends PageController{

  /**
  *Método responsável por retornar o conteúdo (view) da nossa home
  * @return string
  */
  public static function getHome(){
    
    $content = View::render('pages/home', []);

    return parent::getPage('Public', $content);
  }


}
