<?php

namespace App\Models;

use App\Utils\Database;
use Ramsey\Uuid\Uuid;

class User{

    /**
     * Id
     * @var string
     */
    private string $id;


    /**
     * Nome completo
     *
     * @var string
     */
    private string $name;

    /**
     * Email
     *
     * @var string
     */
    private string $email;


    /**
     * Senha
     *
     * @var string
     */
    private string $password;


    /**
     * Define o tipo do usuário - User ou Admin
     *
     * @var string
     */
    private string $user_type;

    /**
     * Documento sem formatação do usuário
     *
     * @var string
     */
    private string $document;


    /**
     * Número de telefone do usuário
     *
     * @var string
     */
    private string $phone_number;
    
    /**
     * Tipo do documento - CPF ou CNPJ
     *
     * @var string
     */
    private string $document_type;


    /**
     * Data de criação do usuário
     *
     * @var string
     */
    private string $created_at;

    /**
     * Data de modificação do usuário
     *
     * @var string
     */
    private string $updated_at;


    /**
     * Flag de ativo ou não do usuário
     *
     * @var boolean
     */
    private bool $is_active;


    /**
     * Método responsável por retornar a senha do usuário
     *
     * @return string
     */
    public function getPassword(){
        return $this->password;
    }

    /**
     * Método responsável por retornar o id do usuario
     *
     * @return string
     */
    public function getId(){
        return $this->id;
    }

    /**
     * Método responsável por retornar o nome do usuario
     *
     * @return string
     */
    public function getName(){
        return $this->name;
    }

     /**
     * Método responsável por retornar o tipo do usuario
     *
     * @return string
     */
    public function getUserType(){
        return $this->user_type;
    }


    /**
     * Método responsável por retornar um usuário a partir do e-mail
     *
     * @param string $email
     * @return User
     */
    public static function getUserByEmail($email){
        return (new Database('users'))->select("email = '".$email."'")->fetchObject(self::class);
    }

}