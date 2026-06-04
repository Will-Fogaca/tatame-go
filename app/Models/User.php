<?php

namespace App\Models;

use InvalidArgumentException;

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
    protected string $name = '';

    /**
     * Email
     * @var string
     */
    protected string $email = '';

    /**
     * Senha
     * @var string
     */
    protected string $password = '';

    /**
     * Define o tipo do usuário - User ou Admin
     * @var string
     */
    protected string $user_type = 'User';

    /**
     * Documento sem formatação do usuário
     * @var string
     */
    protected string $document = '';

    /**
     * Número de telefone do usuário
     * @var string
     */
    protected string $phone_number = '';

    /**
     * Tipo do documento - CPF ou CNPJ
     * @var string
     */
    protected string $document_type = '';


    public function __construct($name, $email, $password, $document, $phone_number, $document_type, $user_type = 'user'){
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
        $this->document = $document;
        $this->phone_number = $phone_number;
        $this->document_type = $document_type;
        $this->user_type = $user_type;
    }

    public function validate(): void
    {
        // Padronização dos dados
        $this->name = trim($this->name);
        $this->email = strtolower(trim($this->email));
        $this->document = self::onlyNumbers($this->document);
        $this->phone_number = self::onlyNumbers($this->phone_number);
        $this->document_type = strtoupper(trim($this->document_type));

        if (empty($this->name)) {
            throw new InvalidArgumentException('O nome é obrigatório.');
        }

        if (strlen($this->name) < 3) {
            throw new InvalidArgumentException(
                'O nome deve possuir pelo menos 3 caracteres.'
            );
        }

        if (empty($this->email)) {
            throw new InvalidArgumentException('O e-mail é obrigatório.');
        }

        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException(
                'O e-mail informado é inválido.'
            );
        }

        if (empty($this->password)) {
            throw new InvalidArgumentException('A senha é obrigatória.');
        }

        if (empty($this->document)) {
            throw new InvalidArgumentException('O documento é obrigatório.');
        }

        if (empty($this->phone_number)) {
            throw new InvalidArgumentException('O telefone é obrigatório.');
        }

        if (strlen($this->phone_number) < 10 || strlen($this->phone_number) > 11) {
            throw new InvalidArgumentException('Telefone inválido.');
        }

        if (empty($this->document_type)) {
            throw new InvalidArgumentException(
                'O tipo de documento é obrigatório.'
            );
        }

        if (!in_array($this->document_type, ['CPF', 'CNPJ'])) {
            throw new InvalidArgumentException(
                'O tipo de documento deve ser CPF ou CNPJ.'
            );
        }

        if ($this->document_type === 'CPF') {

            if (strlen($this->document) !== 11) {
                throw new InvalidArgumentException(
                    'CPF deve possuir 11 dígitos.'
                );
            }

            if (!self::isValidCPF($this->document)) {
                throw new InvalidArgumentException('CPF inválido.');
            }
        }

        if ($this->document_type === 'CNPJ') {

            if (strlen($this->document) !== 14) {
                throw new InvalidArgumentException(
                    'CNPJ deve possuir 14 dígitos.'
                );
            }

            if (!self::isValidCNPJ($this->document)) {
                throw new InvalidArgumentException('CNPJ inválido.');
            }
        }

        if (!in_array($this->user_type, ['user', 'admin'])) {
            throw new InvalidArgumentException(
                'Tipo de usuário inválido.'
            );
        }

        // Documento duplicado
        $existingDocument = self::getUserByDocument($this->document);

        if ($existingDocument && $existingDocument->getId() !== $this->id) {
            throw new InvalidArgumentException(
                'Já existe um usuário cadastrado com este documento.'
            );
        }

        // E-mail duplicado
        $existingUser = self::getUserByEmail($this->email);

        if ($existingUser && $existingUser->getId() !== $this->id) {
            throw new InvalidArgumentException(
                'Já existe um usuário cadastrado com este e-mail.'
            );
        }
    }


    /**
     * Método responsável por retornar um usuário a partir do documento
     *
     * @param string $document
     * @return static|false
     */
    public static function getUserByDocument(string $document): static|false
    {
        $stmt = static::db()->select("document = '$document'");
        return $stmt->fetchObject(static::class) ?: false;
    }

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
    public static function getUserByEmail($email): static|false
    {
        $stmt = static::db()->select("email = '$email'");

        $data = $stmt->fetch();

        if (!$data) {
            return false;
        }

        $user = new static(
            $data['name'],
            $data['email'],
            $data['password'],
            $data['document'],
            $data['phone_number'],
            $data['document_type'],
            $data['user_type']  // fix: user_type agora é passado corretamente
        );

        $user->id         = $data['id'];
        $user->created_at = $data['created_at'];
        $user->updated_at = $data['updated_at'];
        $user->is_active  = $data['is_active'];

        return $user;
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

    private static function onlyNumbers(string $value): string
    {
        return preg_replace('/\D/', '', $value);
    }

    private static function isValidCPF(string $cpf): bool
    {
        $cpf = self::onlyNumbers($cpf);

        if (strlen($cpf) !== 11) {
            return false;
        }

        if (preg_match('/(\d)\1{10}/', $cpf)) {
            return false;
        }

        for ($t = 9; $t < 11; $t++) {
            $d = 0;

            for ($c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * (($t + 1) - $c);
            }

            $d = ((10 * $d) % 11) % 10;

            if ($cpf[$c] != $d) {
                return false;
            }
        }

        return true;
    }

    private static function isValidCNPJ(string $cnpj): bool
    {
        $cnpj = self::onlyNumbers($cnpj);

        if (strlen($cnpj) !== 14) {
            return false;
        }

        if (preg_match('/(\d)\1{13}/', $cnpj)) {
            return false;
        }

        $weights1 = [5,4,3,2,9,8,7,6,5,4,3,2];
        $weights2 = [6,5,4,3,2,9,8,7,6,5,4,3,2];

        $sum = 0;
        foreach ($weights1 as $i => $weight) {
            $sum += $cnpj[$i] * $weight;
        }

        $digit1 = $sum % 11 < 2 ? 0 : 11 - ($sum % 11);

        $sum = 0;
        foreach ($weights2 as $i => $weight) {
            $sum += $cnpj[$i] * $weight;
        }

        $digit2 = $sum % 11 < 2 ? 0 : 11 - ($sum % 11);

        return $cnpj[12] == $digit1 && $cnpj[13] == $digit2;
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