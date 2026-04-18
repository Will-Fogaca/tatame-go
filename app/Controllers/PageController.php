<?php

namespace App\Controllers;
use \App\Utils\View;

abstract class PageController{
  /**
  * Método responsável por renderizar o topo da página
  * @return string
  */

  private static function getHeader(){
    return View::render('pages/header');
  }

  /**
  * Método responsável por renderizar o rodapé da página
  * @return string
  */

  private static function getFooter(){
    return View::render('pages/footer');
  }


  /**
  *Método responsável por retornar o conteúdo (view) da nossa página genérica
  * @return string
  */
  public static function getPage($title, $content){
    return View::render('pages/page', [
        'title' => $title,
        'header' => self::getHeader(),
        'content' => $content,
        'footer' => self::getFooter()
    ]);
  }

 /**
 * Método responsável por rendereziar o layout de paginação
 * @param Request $request
 * @param Pagination $pagination 
 */
  public static function getPagination($request, $pagination){
    $pages = $pagination->getPages();
    if(count($pages) <= 1) return '';
  
    $links = '';

    $url = $request->getRouter()->getCurrentUrl();
    $queryParams = $request->getQueryParams();

    foreach ($pages as $page) {
     $queryParams['page'] = $page['page']; 

      $link = $url.'?'.http_build_query($queryParams);
     
      $links .= View::render('pages/pagination/link', [
        'page' => $page['page'],
        'link' => $link
      ]);
    }

    return View::render('pages/pagination/box', [
      'links' => $links
    ]);

   }


}
