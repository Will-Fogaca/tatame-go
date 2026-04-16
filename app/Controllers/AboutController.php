<?php

namespace App\Controllers;
use \App\Utils\View;
use \App\Models\Academy;

class AboutController extends PageController{

  /**
  *Método responsável por retornar o conteúdo (view) da nossa página de sobre
  * @return string
  */
  public static function getAbout(){
    $content = View::render('about', [
      'about' => 'Minha página TATAMEGO carregada dinamicamente'
    ]);
    return parent::getPage('About', $content);
  }


}
