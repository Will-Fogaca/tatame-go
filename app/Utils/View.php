<?php

  namespace App\Utils;

  class View{

    /**
      * Método responsável por retornar o conteúdo de uma view
      * @param string $view
      * @return string
    */
    private static function getContentView($view){
        $file = __DIR__.'/../../resources/view/'.$view.'.html';
        return file_exists($file) ? file_get_contents($file) : '';
    }

    /**
      * Método responsável por retornar o conteúdo renderizado de uma view
      * @param string $view
      * @param array $vars
      * @return string
    */
    public static function render($view, $vars = []){
      $contentView = self::getContentView($view);
      foreach (array_keys($vars) as $key) {
          $contentView = str_replace('{{' . $key . '}}', $vars[$key], $contentView);
      }
      return $contentView;
    }


  }
