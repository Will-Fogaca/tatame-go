<?php

namespace App\Models;

use App\Utils\Database;
use PDOStatement;

class ClassModel extends Model {

    /**
     * Nome da tabela
     *
     * @var string
     */
    protected static string $table = 'classes';

    /**
     * Id da academia
     *
     * @var string
     */
    public $academy_id = null;

    /**
     * Id do horário agendado
     *
     * @var string
     */
    public $schedule_id = null;

    /**
     * Id da modalidade
     *
     * @var string
     */
    public $modality_id = null;

    /**
     * Data da aula
     *
     * @var string
     */
    public $class_date = null;

    /**
     * Hora do início da aula
     *
     * @var string
     */
    public $start_time = null;

    /**
     * Hora do fim da aula
     *
     * @var string
     */
    public $end_time = null;
    
    /**
     * Observações da aula
     *
     * @var string
     */
    public $notes = null;


    /**
     * Método responsável por retornar o id da aula
     *
     * @return string
     */     
    public function getId() { 
        return $this->id; 
    }

    /**
     * Método responsável por retornar o id da academia
     *
     * @return string
     */
    public function getAcademyId() {
        return $this->academy_id;
    }

    /**
     * Método responsável por retornar a data de lançamento da aula
     *
     * @return string
     */
    public function getClassDate() {
        return $this->class_date;
    }

    /**
     * Método responsável por retornar a hora de início da aula
     *
     * @return string
     */
    public function getStartTime() {
        return $this->start_time; 
    }

    /**
     * Método responsável por retornar a hora de fim da aula
     *
     * @return void
     */
    public function getEndTime() {
        return $this->end_time; 
    }

    /**
     * Método responsável por retornar as observações da aula
     *
     * @return void
     */
    public function getNotes() {
        return $this->notes; 
    }

    /**
     * Método responsável por retornar o objeto em formato de array
     *
     * @return array
     */
    protected function toArray(): array {
        return [
            'academy_id'  => $this->academy_id,
            'schedule_id' => $this->schedule_id  ?: null,
            'modality_id' => $this->modality_id  ?: null,
            'class_date'  => $this->class_date,
            'start_time'  => $this->start_time   ?: null,
            'end_time'    => $this->end_time      ?: null,
            'notes'       => $this->notes         ?: null,
            'is_active'   => 1,
        ];
    }

    /**
     * Método responsável por retornar os detalhes das aulas
     *
     * @param string $where
     * @param string $order
     * @param string $limit
     * @param string $offset
     * @return PDOStatement
     */
    public static function listWithDetails($where = null, $order = null, $limit = null, $offset = null) {

        $join = 'LEFT JOIN class_modalities cm ON cm.id = classes.modality_id'. 
                ' LEFT JOIN academies a ON a.id = classes.academy_id';

        $fields = 'classes.id, classes.class_date, classes.start_time, classes.end_time,'
                . ' classes.notes, classes.academy_id, classes.schedule_id, classes.modality_id,'
                . ' cm.name AS modality_name, a.name AS academy_name';

        return static::list($where, $order, $limit, $offset, $fields, $join);
    }
}