<?php

namespace App\Session;

use App\Models\User;

class LoginSession {

    /**
     * Inicia a sessão se ainda não estiver iniciada
     * @return void 
     */
    private static function init(){
        if(session_status() !== PHP_SESSION_ACTIVE){
            session_start();
        }
    }

    /**
     * Cria a sessão de login
     */
    public static function login(User $user){
        self::init();

        $_SESSION['user'] = [
            'id' => $user->getId(),
            'name' => $user->getName(),
            'user_type' => $user->getUserType()
        ];

    }

    /**
     * Método que verifica se o usuário é um administrador
     *
     * @return boolean
     */
    public static function isAdmin(){
        self::init();
        return $_SESSION['user']['user_type'] == 'admin';
    }

    /**
     * Método que verifica se o usuário é um master
     *
     * @return boolean
     */
    public static function isMaster(){
        self::init();
        return $_SESSION['user']['user_type'] == 'master';
    }

    /**
     * Método que verifica se o usuário é um usuário comum
     *
     * @return boolean
     */

    public static function isUser(){
        self::init();
        return $_SESSION['user']['user_type'] == 'user';
    }


    /**
     * Verifica se o usuário está logado
     */
    public static function isLogged(){
        self::init();
        return isset($_SESSION['user']['id']);
    }

    /**
     * Retorna dados do usuário logado
     * @return array
     */
    public static function getUser(){
        self::init();
        return $_SESSION['user'] ?? null;
    }

     /**
     * Retorna o id do usuário logado
     * @return string
     */
    public static function getUserId(){
        self::init();
        return $_SESSION['user']['id'] ?? null;
    }

    /**
     * Faz logout
     */
    public static function logout(){
        self::init();
        session_destroy();
    }
}