<?php 

namespace App\Models;
use \App\Utils\Database;

class BeltRank{

    /**
     * Id da graduação
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
     * Descrição da graduação
     *
     * @var string
     */
    public string $description;


    /**
     * Nível da graduação
     *
     * @var integer
     */
    public int $level;

    /**
     * Data de cadastro da graduação
     *
     * @var string
     */
    public string $created_at;


   /**
     * Método responsável por retornar alunos
     * @param string $where
     * @param string $order
     * @param string $limit
     * @param string $offset
     * @param string $fields
     * @return PDOStatement
     */
   public static function list($where = null, $order = null, $limit = null, $offset = null, $fields = '*'){
        return (new Database('belt_ranks'))->select($where, $order, $limit, $offset, $fields);
   }
}