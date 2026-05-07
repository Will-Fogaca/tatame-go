<?php 

namespace App\Models;
use \App\Utils\Database;

class ClassModality{
    
    /**
     * Id da modalidade da classe
     *
     * @var string #UUid
     */
    public string $id;

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
     * Data de cadastro da modalidade
     *
     * @var string
     */
    public ?string $created_at = null;

    /**
     * Flag que indica se está ativo
     *
     * @var boolean
     */
    public bool $is_active = true;  


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
        return (new Database('class_modalities'))->select($where, $order, $limit, $offset, $fields);
    }


    
}