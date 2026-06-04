<?php

namespace App\Models;

use App\Utils\Database;

class StudentUser extends Model {

    /**
     * Nome da tabela
     *
     * @var string
     */
    protected static string $table = 'student_user';

    /**
     * Id do usuário
     *
     * @var string
     */
    public $user_id = null;

    /**
     * Id do aluno
     *
     * @var string
     */
    public $student_id = null;

    /**
     * Método responsável por retornar o id da relação usuário-aluno
     *
     * @return string
     */
    public function getId(){ 
        return $this->id; 
    }
    
    /**
     * Método responsável por retornar o id do usuário
     *
     * @return string
     */
    public function getUserId(){
        return $this->user_id; 
    }

    /**
     * Método responsável por retornar o id do aluno
     *
     * @return string
     */
    public function getStudentId(){
        return $this->student_id; 
    }
    
    /**
     * Método responsável por retornar a data de cadastro da relação usuário-aluno
     *
     * @return string
     */
    public function getCreatedAt() {
        return $this->created_at; 
    }

    /**
     * Método responsável por retornar o status da relação usuário-aluno
     *
     * @return string
     */
    public function isActive(){ 
        return (bool) $this->is_active; 
    }

    /**
     * Método responsável por retornar o objeto em formato de array
     *
     * @return array
     */
    protected function toArray(): array {
        return [
            'user_id'    => $this->user_id,
            'student_id' => $this->student_id,
            'is_active'  => $this->is_active,
        ];
    }
}