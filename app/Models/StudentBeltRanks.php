<?php

namespace App\Models;

use App\Utils\Database;

class StudentBeltRanks {

    /**
     * Id da relação entre graduação-aluno
     *
     * @var string
     */
    public $id;

    /**
     * Id do aluno
     *
     * @var string
     */
    public $student_id;

    /**
     * Id da academia
     *
     * @var string
     */
    public $academy_id;

    /**
     * Id da graduação
     *
     * @var string
     */
    public $belt_rank_id;

    /**
     * Data em que foi concedida a graduaçao
     *
     * @var string
     */
    public $awarded_at;

    /**
     * Anotações da graduação
     *
     * @var string
     */
    public $notes;

    /**
     * Data de criação do registro
     *
     * @var string
     */
    public $created_at;


    public function save(){

        $this->id = (new Database('student_belt_ranks'))->insert([
            'student_id'   => $this->student_id,
            'academy_id'   => $this->academy_id,
            'belt_rank_id'=> $this->belt_rank_id,
            'awarded_at'   => $this->awarded_at,
            'notes'        => $this->notes,
            'created_at'   => date('Y-m-d H:i:s')
        ]);

        return true;
    }


    public static function list($where = null, $order = null, $limit = null, $offset = null, $fields = '*'){
        return (new Database('student_belt_ranks'))->select($where, $order, $limit, $offset, $fields);
    }


    public static function getById($id){
        return self::list("id = '".$id."'") ->fetchObject(self::class);
    }

     /**
     * Método responsável por deletar a relação de aluno-graduação
     *
     * @return string
     */
    public function delete(){

        return (new Database('student_belt_ranks'))->delete(
            "id = '".$this->id."'"
        );
    }

    /**
     * Método que atualiza a relação aluno-graduação
     *
     * @return string
     */
    public function update(){

        return (new Database('student_belt_ranks'))->update(
            "id = '".$this->id."'",
            [
                'belt_rank_id' => $this->belt_rank_id,
                'awarded_at'   => $this->awarded_at,
                'notes'        => $this->notes
            ]
        );
    }
}