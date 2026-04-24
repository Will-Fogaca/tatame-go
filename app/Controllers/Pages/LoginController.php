<?php 
namespace App\Controllers\Pages;

use App\Utils\View;
use App\Utils\Page;
use App\Models\User;
use App\Session\LoginSession;

class LoginController{

    /**
     * Método responsável por retornar a renderização da página de login
     *
     * @param Request $request
     * @return string
     */
    public static function getIndex($request, $errorMessage = null){
        $status = !is_null($errorMessage) ? AlertController::error($errorMessage) : '';

        $content = View::render('shared/login/index', [
            'status'=> $status
        ]);

        return Page::getPage('Login', $content);
    }

    /**
     * Método responsável por definir o login do usuário
     *
     * @param Request $request
     * @return void
     */
    public static function postLogin($request){
        $postVars = $request->getPostVars();

        if(!isset($postVars['email'])){
            return self::getIndex($request, 'E-mail não informado.');
        }

        if(!isset($postVars['password'])){
            return self::getIndex($request, 'Senha não informada.');
        }

        $email = $postVars['email'] ?? '';
        $password = $postVars['password'] ?? '';

        $user = User::getUserByEmail($email);

        if(!$user instanceof User){
            return self::getIndex($request, 'E-mail ou senha inválidos');
        }


        if(!password_verify($password, $user->getPassword())){
            return self::getIndex($request, 'E-mail ou senha inválidos');
        }
        
        LoginSession::login($user);

        $request->getRouter()->redirect('/');
    }

    /**
     * Método responsável por executar o logout no sistema
     *
     * @param Request $request
     * @return void
     */
    public static function postLogout($request){
        LoginSession::logout();
        $request->getRouter()->redirect('/');
    }

    public static function getRegister($request){
        $content = View::render('shared/login/register', []);
        return Page::getPage('Cadastro', $content);
    }
}