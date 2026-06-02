<?php 
namespace App\Controllers\User;
use \App\Utils\View;
use \App\Utils\Page;

class HomeController{

    /**
     * Método responsável por retornar a página inicial do usuário
     *
     * @param Request $request
     * @return string
     */
    public static function getIndex($request){

        $pagination = null;

        $content = View::render('user/index', []);

        return Page::getPage('Painel do usuário', $content);
    }

}