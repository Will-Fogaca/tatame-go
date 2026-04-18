<?php

namespace App\Models;

use App\Utils\Database;

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
     * ID da graduação (faixa)
     * @var int|null
     */
    public $graduation_id;

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

    public function save(){
        $this->id = (new Database('tb_student'))->insert([
            'name' => $this->name,
            'birth_date' => $this->birth_date, 
            'phone_number' => $this->phone_number,
            'guardian_name'=> $this->guardian_name,
            'guardian_phone' => $this->guardian_phone,
            'graduation_id' => $this->graduation_id,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at, 
            'updated_at' => $this->updated_at
        ]);

        
    }

    /**
     * Método responsável por retornar alunos
     * @param string $where
     * @param string $order
     * @param string $limit
     * @param string $offset
     * @param string $fields
     * @return PDOStatement
     */
   public static function list($where = null, $order = null, $limit = null, $offset = null, $fields = '*'){
    return (new Database('tb_student'))->select($where, $order, $limit, $offset, $fields);
   }

}