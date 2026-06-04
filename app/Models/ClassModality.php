<?php

namespace App\Models;

class ClassModality extends Model {

    /**
     * Nome da tabela
     *
     * @var string
     */
    protected static string $table = 'class_modalities';

    /**
     * Id da academia
     *
     * @var string
     */
    public $academy_id = null;

    /**
     * Nome da modalidade
     *
     * @var string
     */
    public $name = null;

    /**
     * Método responsável por retornar o objeto em formato de array
     *
     * @return array
     */
    protected function toArray(): array {
        return [
            'academy_id' => $this->academy_id,
            'name'       => $this->name,
            'is_active'  => 1,
        ];
    }
}