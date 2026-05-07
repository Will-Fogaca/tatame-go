<?php 

namespace App\Models;
use DateTime;
use \App\Utils\Database;


class AcademyStudents {

    /**
     * Id do relacionamento academia-aluno
     *
     * @var string
     */
    public string $id;
    
    /**
     * Id da academia
     *
     * @var string
     */
    public string $academy_id;

    /**
     * Id do aluno
     *
     * @var string
     */
    public string $student_id; 

    /**
     * Data de criação do relacionamento
     *
     * @var string
     */
    public string $created_at;


    /**
     * Método responsável por salvar o relacionamento entre aluno e academia]
     * @return string
     */

    public function save(){
        $this->id = (new Database("academy_students"))->insert([
            'academy_id' => $this->academy_id,
            'student_id' => $this->student_id,
            'created_at'=> new DateTime()->format('Y-m-d H:i:s')
        ]);

        return $this->id;
    }

}
