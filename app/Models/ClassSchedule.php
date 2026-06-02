<?php 

namespace App\Models;

class ClassSchedule extends Model{
    
    /**
     * Nome da tabela
     * @var string
     */
    protected static string $table = 'class_schedules';

    /**
     * Id da academia
     *
     * @var string #UUid
     */
    public string $academy_id;

    /**
     * Nome da modalidade
     *
     * @var string
     */
    public string $name;


    
     /**
     * Método responsável por retornar os campos a serem gravados no banco
     *
     * @return array
     */
    protected function toArray(): array {
        return [
            'name'           => $this->name,
            'academy_id'     => $this->academy_id,
            'is_active'      => $this->is_active,
            'updated_at'     => date('Y-m-d H:i:s'),
        ];
    }
}