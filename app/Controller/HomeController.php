<?php

namespace App\Controller;
use \App\Utils\View;
use \App\Model\Entity\Academy;

class HomeController extends PageController{

  /**
  *Método responsável por retornar o conteúdo (view) da nossa home
  * @return string
  */
  public static function getHome(){

    $academy = new Academy('SBA Taquarituba', 'sbataquarituba@gmail.com');
    $content = View::render('pages/home', [
      'id' => $academy->getId(),
      'name' => $academy->getName(),
      'email'=> $academy->getEmail(),
      'teste'=> 'TESTE DE APLICACAO'
    ]);

    return parent::getPage('Home', $content);
  }


}
