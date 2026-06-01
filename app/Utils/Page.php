<?php

namespace App\Utils;
use \App\Utils\View;
use \App\Session\LoginSession;
 class Page{
  /**
  * Método responsável por renderizar o topo da página
  * @return string
  */

  private static function getHeader(){


    if(!LoginSession::isLogged()){
        return View::render('shared/header');
    }

    $user = LoginSession::getUser();

    return match($user['user_type']){
        'admin'  => View::render('admin/header'),
        'master' => View::render('master/header'),
        default  => View::render('user/header'),
    };
  }

  /**
  * Método responsável por renderizar o rodapé da página
  * @return string
  */

  private static function getFooter(){
    return View::render('shared/footer');
  }


  /**
  *Método responsável por retornar o conteúdo (view) da nossa página genérica
  * @return string
  */
  public static function getPage($title, $content){
    return View::render('shared/page', [
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
     
      $links .= View::render('shared/pagination/link', [
        'page' => $page['page'],
        'link' => $link
      ]);
    }

    return View::render('shared/pagination/box', [
      'links' => $links
    ]);

   }


   /**
    * Método responsável por exibir a tela de erros
    *
    * @param string $title
    * @param string $message
    * @param string $backUrl
    * @return string
    */
   public static function getError($title, $message, $backUrl = '/'){

      $content = \App\Utils\View::render('shared/error', [
          'title' => $title,
          'message' => $message,
          'back_url' => $backUrl
      ]);

      return self::getPage('Erro', $content);
  }


}
