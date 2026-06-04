<?php

namespace App\Utils;

class Environment{

  /**
   * Método responsável por carregar as variáveis de ambiente do projeto
   * @param  string $dir Caminho absoluto da pasta onde encontra-se o arquivo .env
   */
  public static function load($dir){
    if(!file_exists($dir.'/.env')){
        return false;
    }

    $lines = file($dir.'/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach($lines as $line){

        if(strpos(trim($line), '#') === 0){
            continue;
        }

        [$name, $value] = explode('=', $line, 2);

        $name = trim($name);
        $value = trim($value);

        $_ENV[$name] = $value;
        $_SERVER[$name] = $value;
    }
}

}