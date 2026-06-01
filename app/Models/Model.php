<?php

namespace App\Models;

use App\Utils\Database;

abstract class Model {

    /**
     * Nome da tabela no banco de dados.
     * Deve ser definido em cada subclasse:
     *   protected static string $table = 'nome_da_tabela';
     * @var string
     */
    protected static string $table;


    public $id = null;

    public $created_at = null;

    public $updated_at = null;

    public $is_active = true;


    /**
     * Método responsável por retornar uma instância do Database para a tabela da subclasse
     * @return Database
     */
    protected static function db(){
        return new Database(static::$table);
    }

    /**
     * Método responsável por retornar todos os registros da tabela
     *
     * @param string $where
     * @param string $order
     * @param string $limit
     * @param string $offset
     * @param string $fields
     * @param string $join
     * @return \PDOStatement
     */
    public static function list($where = null, $order = null, $limit = null, $offset = null, $fields = '*', $join = null){
        return static::db()->select($where, $order, $limit, $offset, $fields, $join);
    }

    /**
     * Método responsável por buscar um único registro pelo ID
     *
     * @param string $id
     * @return static|false
     */
    public static function getById($id){
        $stmt = static::db()->execute(
            'SELECT * FROM '.static::$table.' WHERE id = ?',
            [$id]
        );

        return $stmt->fetchObject(static::class) ?: false;
    }

    /**
     * Método responsável por persistir o objeto no banco
     * Realiza INSERT se não tiver ID, UPDATE se tiver
     *
     * @return void
     */
    public function save(){

        if (!empty($this->id)) {
            $this->performUpdate();
        } else {
            $this->performInsert();
        }
    }

    /**
     * Método responsável por executar o INSERT com os campos retornados por toArray()
     *
     * @return void
     */
    protected function performInsert(){
        static::db()->insert($this->toArray());
    }
    /**
     * Método responsável por executar o UPDATE com os campos retornados por toArray()
     *
     * @return void
     */
    protected function performUpdate(){
        $data               = $this->toArray();
        $data['updated_at'] = date('Y-m-d H:i:s');
        static::db()->update("id = '{$this->id}'", $data);
    }

    /**
     * Método responsável por realizar soft delete (marca is_active = false)
     * Sobrescreva nas subclasses que precisam de hard delete
     *
     * @return boolean
     */
    public function delete(){
        return static::db()->update(
            "id = '{$this->id}'",
            [
                'is_active'  => false,
                'updated_at' => date('Y-m-d H:i:s'),
            ]
        );
    }

    /**
     * Método responsável por executar uma query diretamente na tabela
     *
     * @param string $query
     * @param array $params
     * @return \PDOStatement
     */
    public static function execute($query, $params = []){
        return static::db()->execute($query, $params);
    }

    /**
     * Método responsável por retornar os campos a serem gravados no banco
     * Deve ser implementado em cada subclasse de acordo com suas colunas
     *
     * @return array
     */
    abstract protected function toArray(): array;
}