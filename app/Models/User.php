<?php

namespace App\Models;

class User extends Model {

    /**
     * Nome da tabela
     * @var string
     */
    protected static string $table = 'users';

    /**
     * Nome completo
     * @var string
     */
    private string $name = '';

    /**
     * Email
     * @var string
     */
    private string $email = '';

    /**
     * Senha
     * @var string
     */
    private string $password = '';

    /**
     * Define o tipo do usuário - User ou Admin
     * @var string
     */
    private string $user_type = 'User';

    /**
     * Documento sem formatação do usuário
     * @var string
     */
    private string $document = '';

    /**
     * Número de telefone do usuário
     * @var string
     */
    private string $phone_number = '';

    /**
     * Tipo do documento - CPF ou CNPJ
     * @var string
     */
    private string $document_type = '';

    /**
     * Método responsável por retornar a senha do usuário
     *
     * @return string
     */
    public function getPassword(): string {
        return $this->password;
    }

    /**
     * Método responsável por retornar o id do usuário
     *
     * @return string
     */
    public function getId(): string {
        return $this->id;
    }

    /**
     * Método responsável por retornar o nome do usuário
     *
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * Método responsável por retornar o tipo do usuário
     *
     * @return string
     */
    public function getUserType(): string {
        return $this->user_type;
    }

    /**
     * Observações do usuário
     * @var string
     */
    public ?string $notes = null;

    /**
     * Método responsável por retornar um usuário a partir do e-mail
     *
     * @param string $email
     * @return static|false
     */
    public static function getUserByEmail($email): static|false {
        $stmt = static::db()->select("email = '$email'");
        return $stmt->fetchObject(static::class) ?: false;
    }

    /**
     * Método responsável por retornar os campos a serem gravados no banco
     *
     * @return array
     */
    protected function toArray(): array {
        return [
            'name'          => $this->name,
            'email'         => $this->email,
            'password'      => $this->password,
            'user_type'     => $this->user_type,
            'document'      => $this->document,
            'phone_number'  => $this->phone_number,
            'document_type' => $this->document_type,
            'is_active'     => $this->is_active,
        ];
    }

    /**
     * Método responsável por setar o nome do usuário
     *
     * @param string $name
     * @return void
     */
    public function setName(string $name): void {
        $this->name = $name;
    }

    /**
     * Método responsável por setar o email do usuário
     *
     * @param string $email
     * @return void
     */
    public function setEmail(string $email): void {
        $this->email = $email;
    }

    /**
     * Método responsável por setar a senha do usuário
     *
     * @param string $password
     * @return void
     */
    public function setPassword(string $password): void {
        $this->password = $password;
    }

    /**
     * Método responsável por setar o número de telefone do usuário
     *
     * @param string $phoneNumber
     * @return void
     */
    public function setPhoneNumber(string $phoneNumber): void {
        $this->phone_number = $phoneNumber;
    }

    /**
     * Método responsável por setar o documento do usuário
     *
     * @param string $document
     * @return void
     */
    public function setDocument(string $document): void {
        $this->document = $document;
    }

    /**
     * Método responsável por setar o tipo de documento do usuário
     *
     * @param string $documentType
     * @return void
     */
    public function setDocumentType(string $documentType): void {
        $this->document_type = $documentType;
    }

    /**
     * Método responsável por setar o tipo do usuário
     *
     * @param string $userType
     * @return void
     */
    public function setUserType(string $userType): void {
        $this->user_type = strtolower($userType);
    }
}