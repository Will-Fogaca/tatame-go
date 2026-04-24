<?php 

namespace App\Controllers\Admin;
use \App\Utils\View;
use \App\Utils\Page;
class HomeController{

    /**
     * Método responsável por retornar a página inicial do administrador
     *
     * @param Request $request
     * @return string
     */
    public static function getIndex($request){

        $pagination = null;

        $content = View::render('admin/index', []);

        return Page::getPage('Painel do Administrador', $content);
    }

}