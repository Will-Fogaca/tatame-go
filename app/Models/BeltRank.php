<?php 

namespace App\Models;

use \App\Utils\Database;

class BeltRank{

    /**
     * Id da graduação
     * @var string|null
     */
    public ?string $id = null;

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
     * Data de criação
     * @var string|null
     */
    public ?string $created_at = null;


    /**
     * Método responsável por listar as faixas-graduações
     *
     * @param string $where
     * @param string $order
     * @param string $limit
     * @param string $offset
     * @param string $fields
     * @return \PDOStatement
     */
    public static function list($where = null, $order = null, $limit = null, $offset = null, $fields = '*'){
        return (new Database('belt_ranks'))->select($where, $order, $limit, $offset, $fields);
    }

    /**
     * Método responsável por gravar uma nova graduação
     *
     * @return void
     */
    public function save(){

        $this->created_at = $this->created_at ?? date('Y-m-d H:i:s');

        $this->id = (new Database('belt_ranks'))->insert([
            'academy_id'=> $this->academy_id,
            'description'=> $this->description,
            'level'=> $this->level,
            'created_at' => $this->created_at
        ]);

        return $this->id;
    }
}