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
    public static function postLogin($request)
    {
        $postVars = $request->getPostVars();
        
        if (!isset($postVars['email'])) {
            return self::getIndex($request, 'E-mail não informado.');
        }

        if (!isset($postVars['password'])) {
            return self::getIndex($request, 'Senha não informada.');
        }


        $email = trim($postVars['email'] ?? '');
        $password = $postVars['password'] ?? '';

       
        $user = User::getUserByEmail($email);


        if (!$user instanceof User) {
            return self::getIndex($request, 'E-mail ou senha inválidos');
        }

        if (!password_verify($password, $user->getPassword())) {
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

    /**
     * Método responsável por retornar a página de cadastro de usuário
     *
     * @param Request $request
     * @return string
     */
    public static function getRegister($request, $errorMessage = null)
    {
        $status = !is_null($errorMessage) ? AlertController::error($errorMessage) : '';

        $content = View::render('shared/login/register', [
            'status' => $status
        ]);

        return Page::getPage('Cadastro', $content);
    }

    /**
     * Método responsável por cadastrar um usuário
     *
     * @param Request $request
     * @return void
     */
    public static function postRegister($request)
    {
        try {
            $postVars = $request->getPostVars();

            $name            = trim($postVars['name'] ?? '');
            $email           = trim($postVars['email'] ?? '');
            $phoneNumber     = trim($postVars['phone_number'] ?? '');
            $document        = trim($postVars['document'] ?? '');
            $documentType    = trim($postVars['document_type'] ?? '');
            $password        = $postVars['password'] ?? '';
            $passwordConfirm = $postVars['password_confirm'] ?? '';

            if ($password !== $passwordConfirm) {
                throw new \InvalidArgumentException('As senhas não conferem.');
            }

            $user = new User(
                $name,
                $email,
                password_hash($password, PASSWORD_DEFAULT),
                $document,
                $phoneNumber,
                $documentType
            );

            $user->validate();
            $user->save();

            LoginSession::login($user);

            $request->getRouter()->redirect('/');

        } catch (\InvalidArgumentException $e) {

            return self::getRegister($request, $e->getMessage());

        } catch (\Exception $e) {

            return self::getRegister(
                $request,
                'Erro interno ao realizar cadastro.'
            );

        }
    }

}