<?php

namespace App\Controllers\Pages;

use \App\Utils\View;
use \App\Utils\Page;
use \App\Session\LoginSession;
use \App\Controllers\User;

class HomeController {

   public static function getIndex(){

        if(!LoginSession::isLogged()){
            $content = View::render('shared/index', []);
            return Page::getPage('TatameGO', $content);
        }

        $user = LoginSession::getUser();

        return match($user['user_type']){

            'admin' => \App\Controllers\Admin\HomeController::getIndex(null),

            'user' => \App\Controllers\User\HomeController::getIndex(null),

            default => Page::getError(
                'Acesso negado',
                'Tipo de usuário inválido.'
            )
        };
    }
}