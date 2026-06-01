<?php 

namespace App\Models;

class AcademyStudents extends Model {

    /**
     * Nome da tabela
     * @var string
     */
    protected static string $table = 'academy_students';

    /**
     * Id da academia
     * @var string
     */
    public string $academy_id;

    /**
     * Id do aluno
     * @var string
     */
    public string $student_id;


    /**
     * Método responsável por retornar os campos a serem gravados no banco
     *
     * @return array
     */
    protected function toArray(): array{
        return [
            'id'         => $this->id, // ← adicione
            'academy_id' => $this->academy_id,
            'student_id' => $this->student_id,
            'is_active'  => 1,
            'created_at' => $this->created_at ?? date('Y-m-d H:i:s'),
        ];
    }

    /**
     * Método responsável por salvar a relação no banco
     *
     * @return void
     */
    public function save(): void {
        $isNew = empty($this->id); 

        if($isNew){
            $this->id = \Ramsey\Uuid\Uuid::uuid4()->toString();
        }

        $isNew ? $this->performInsert() : $this->performUpdate();
    }
}