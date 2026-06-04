<?php 

namespace App\Models;

class BeltRank extends Model {

    /**
     * Nome da tabela
     * @var string
     */
    protected static string $table = 'belt_ranks';

    /**
     * Id da academia
     * @var string
     */
    public string $academy_id;

    /**
     * Descrição da graduação
     * @var string
     */
    public string $description;

    /**
     * Nível da graduação
     * @var int
     */
    public int $level;

    /**
     * Método responsável por retornar os campos a serem gravados no banco
     *
     * @return array
     */
    protected function toArray(): array{
        return [
            'academy_id'  => $this->academy_id,
            'description' => $this->description,
            'level'       => $this->level,
            'created_at'  => $this->created_at ?? date('Y-m-d H:i:s'),
        ];
    }

    /**
     * Método responsável por excluir a graduação (hard delete)
     *
     * @return boolean
     */
    public function delete(): bool{
        return static::db()->delete("id = '{$this->id}'");
    }


    /**
     * Retorna o próximo nível disponível da academia
     *
     * @param string $academyId
     * @return int
     */
    public static function getNextLevel(string $academyId): int
    {
        $belt = static::list(
            "academy_id = '".$academyId."'",
            'level DESC',
            '1'
        )->fetchObject(self::class);

        return $belt ? ($belt->level + 1) : 1;
    }
}