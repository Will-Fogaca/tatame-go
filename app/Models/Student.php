<?php

namespace App\Models;

use App\Utils\Database;
use App\Utils\BusinessException;

class Student
{
    /**
     * ID do aluno
     * @var string UUID
     */
    public $id;

    /**
     * Nome do aluno
     * @var string
     */
    public $name;

    /**
     * Data de nascimento
     * @var string (YYYY-MM-DD)
     */
    public $birth_date;

    /**
     * Telefone do aluno
     * @var string|null
     */
    public $phone_number;

    /**
     * Nome do responsável (pai, mãe, etc)
     * @var string|null
     */
    public $guardian_name;

    /**
     * Telefone do responsável
     * @var string|null
     */
    public $guardian_phone;

    /**
     * Observações do aluno
     * @var string|null
     */
    public $notes;

    /**
     * Data de criação
     * @var string
     */
    public $created_at;

    /**
     * Data de atualização
     * @var string
     */
    public $updated_at;

    /**
     * Status ativo/inativo
     * @var bool
     */
    public $is_active;


    /**
     * Remove tudo que não é dígito de um telefone
     * @param string|null $phone
     * @return string|null
     */
    private static function sanitizePhone(?string $phone): ?string {
        if(empty($phone)) return null;
        $digits = preg_replace('/\D/', '', $phone);
        return $digits ?: null;
    }

    /**
     * Valida os dados do aluno antes de salvar
     * @throws BusinessException
     */
    private function validate(): void {

        // Nome
        if(empty(trim($this->name ?? ''))){
            throw new BusinessException('O nome do aluno é obrigatório.');
        }
        if(strlen(trim($this->name)) < 3){
            throw new BusinessException('O nome do aluno deve ter pelo menos 3 caracteres.');
        }
        if(strlen(trim($this->name)) > 100){
            throw new BusinessException('O nome do aluno deve ter no máximo 100 caracteres.');
        }

        // Data de nascimento
        if(empty($this->birth_date)){
            throw new BusinessException('A data de nascimento é obrigatória.');
        }

        $birthDate = \DateTime::createFromFormat('Y-m-d', $this->birth_date);
        if(!$birthDate){
            throw new BusinessException('A data de nascimento é inválida.');
        }

        $today = new \DateTime();
        if($birthDate >= $today){
            throw new BusinessException('A data de nascimento deve ser anterior à data atual.');
        }

        $age = $today->diff($birthDate)->y;
        if($age > 120){
            throw new BusinessException('A data de nascimento informada é inválida.');
        }

        // Telefone do aluno
        if(!empty($this->phone_number)){
            $phoneDigits = preg_replace('/\D/', '', $this->phone_number);
            if(strlen($phoneDigits) < 10 || strlen($phoneDigits) > 11){
                throw new BusinessException('O telefone do aluno deve ter 10 ou 11 dígitos.');
            }
        }

        // Responsável obrigatório para menores de 18
        if($age < 18){
            if(empty(trim($this->guardian_name ?? ''))){
                throw new BusinessException('Para menores de 18 anos, o nome do responsável é obrigatório.');
            }
            if(empty(trim($this->guardian_phone ?? ''))){
                throw new BusinessException('Para menores de 18 anos, o telefone do responsável é obrigatório.');
            }
        }

        // Telefone do responsável
        if(!empty($this->guardian_phone)){
            $guardianPhoneDigits = preg_replace('/\D/', '', $this->guardian_phone);
            if(strlen($guardianPhoneDigits) < 10 || strlen($guardianPhoneDigits) > 11){
                throw new BusinessException('O telefone do responsável deve ter 10 ou 11 dígitos.');
            }
        }

        // Nome do responsável
        if(!empty($this->guardian_name) && strlen(trim($this->guardian_name)) < 3){
            throw new BusinessException('O nome do responsável deve ter pelo menos 3 caracteres.');
        }

        // Observações
        if(!empty($this->notes) && strlen($this->notes) > 500){
            throw new BusinessException('As observações devem ter no máximo 500 caracteres.');
        }
    }

    /**
     * Método responsável por salvar ou atualizar o aluno no banco
     * @throws BusinessException
     */
    public function save(): void {

        // Sanitiza os campos antes de validar
        $this->name          = trim($this->name ?? '');
        $this->guardian_name = trim($this->guardian_name ?? '') ?: null;
        $this->notes         = trim($this->notes ?? '') ?: null;
        $this->phone_number  = self::sanitizePhone($this->phone_number);
        $this->guardian_phone= self::sanitizePhone($this->guardian_phone);

        $this->validate();

        $now = date('Y-m-d H:i:s');

        $data = [
            'name'           => $this->name,
            'birth_date'     => $this->birth_date,
            'phone_number'   => $this->phone_number,
            'guardian_name'  => $this->guardian_name,
            'guardian_phone' => $this->guardian_phone,
            'notes'          => $this->notes,
            'is_active'      => $this->is_active,
            'updated_at'     => $now,
        ];

        if($this->id){
            (new Database('students'))->update("id = '{$this->id}'", $data);
        } else {
            $this->created_at  = $now;
            $data['created_at']= $this->created_at;
            $this->id = (new Database('students'))->insert($data);
        }
    }

    /**
     * Método responsável por retornar alunos
     * @param string $where
     * @param string $order
     * @param string $limit
     * @param string $offset
     * @param string $fields
     * @return \PDOStatement
     */
    public static function list($where = null, $order = null, $limit = null, $offset = null, $fields = '*'){
        return (new Database('students'))->select($where, $order, $limit, $offset, $fields);
    }
}