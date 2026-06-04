<?php

namespace App\Models;

class StudentBeltRanks extends Model {

    /**
     * Nome da tabela
     * @var string
     */
    protected static string $table = 'student_belt_ranks';

    /**
     * Id do aluno
     * @var string
     */
    public $student_id;

    /**
     * Id da academia
     * @var string
     */
    public $academy_id;

    /**
     * Id da graduação
     * @var string
     */
    public $belt_rank_id;

    /**
     * Data em que foi concedida a graduação
     * @var string
     */
    public $awarded_at;

    /**
     * Anotações da graduação
     * @var string|null
     */
    public $notes;


    /**
     * Método responsável por retornar o objeto em formato de array
     *
     * @return array
     */
    protected function toArray(): array {
        return [
            'student_id'   => $this->student_id,
            'academy_id'   => $this->academy_id,
            'belt_rank_id' => $this->belt_rank_id,
            'awarded_at'   => $this->awarded_at,
            'notes'        => $this->notes,
            'created_at'   => $this->created_at ?? date('Y-m-d H:i:s'),
        ];
    }

    /**
     * Método responsável por deletar a relação aluno-graduação (hard delete)
     *
     * @return boolean
     */
    public function delete(): bool {
        return static::db()->delete("id = '{$this->id}'");
    }
}