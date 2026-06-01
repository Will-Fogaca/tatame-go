<?php 

namespace App\Models;
use \App\Utils\Database;

class ClassModality extends Model{
    
    /**
     * Nome da tabela
     * @var string
     */
    protected static string $table = 'class_modalities';

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
            'phone_number'   => $this->phone_number,
            'guardian_name'  => $this->guardian_name,
            'guardian_phone' => $this->guardian_phone,
            'notes'          => $this->notes,
            'is_active'      => $this->is_active,
            'updated_at'     => date('Y-m-d H:i:s'),
        ];
    }


    
    
}