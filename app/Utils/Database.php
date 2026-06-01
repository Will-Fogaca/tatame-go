<?php

namespace App\Utils;

use \PDO;
use \PDOException;

class Database{

  /**
   * Host de conexão com o banco de dados
   * @var string
   */
  private static $host;

  /**
   * Nome do banco de dados
   * @var string
   */
  private static $name;

  /**
   * Usuário do banco
   * @var string
   */
  private static $user;

  /**
   * Senha de acesso ao banco de dados
   * @var string
   */
  private static $pass;

  /**
   * Porta de acesso ao banco
   * @var integer
   */
  private static $port;

  /**
   * Nome da tabela a ser manipulada
   * @var string
   */
  private $table;

  /**
   * Instancia de conexão com o banco de dados
   * @var PDO
   */
  private $connection;

  /**
   * Método responsável por configurar a classe
   * @param  string  $host
   * @param  string  $name
   * @param  string  $user
   * @param  string  $pass
   * @param  integer $port
   */
  public static function config($host,$name,$user,$pass,$port = 3306){
    self::$host = $host;
    self::$name = $name;
    self::$user = $user;
    self::$pass = $pass;
    self::$port = $port;
  }

  /**
   * Define a tabela e instancia a conexão
   * @param string $table
   */
  public function __construct($table = null){
    $this->table = $table;
    $this->setConnection();
  }

  /**
   * Método responsável por criar uma conexão com o banco de dados
   */
  private function setConnection(){

    try{

      $this->connection = new PDO(
        'mysql:host='.self::$host.
        ';port='.self::$port.
        ';dbname='.self::$name.
        ';charset=utf8mb4',

        self::$user,
        self::$pass
      );

      $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    }catch(PDOException $e){

      die('ERROR: '.$e->getMessage());
    }
  }

  /**
   * Método responsável por executar queries dentro do banco de dados
   * @param  string $query
   * @param  array  $params
   * @return PDOStatement
   */
  public function execute($query, $params = []){

    try{

      $statement = $this->connection->prepare($query);
      $statement->execute($params);

      return $statement;

    }catch(PDOException $e){

      die('ERROR: '.$e->getMessage());
    }
  }

  /**
   * Método responsável por inserir dados no banco
   * @param  array $values [ field => value ]
   * @return integer ID inserido
   */
  public function insert($values){

      foreach ($values as $key => $value) {
          if (is_bool($value)) {
              $values[$key] = $value ? 1 : 0;
          }
      }

      $fields = array_keys($values);
      $binds  = array_pad([], count($fields), '?');

      $query = 'INSERT INTO '.$this->table.
                ' ('.implode(',', $fields).') '.
                'VALUES ('.implode(',', $binds).')';

      $this->execute($query, array_values($values));

      return $this->connection->lastInsertId();
  }

  /**
   * Método responsável por executar uma consulta no banco
   * @param  string $where
   * @param  string $order
   * @param  string $limit
   * @param  string $offset
   * @param  string $fields
   * @param  string $join
   * @return PDOStatement
   */
  public function select($where = null, $order = null, $limit = null, $offset = null, $fields = '*', $join = null){

    $join   = !empty($join)   ? ' '.$join.' ' : '';
    $where  = !empty($where)
      ? 'WHERE '.$where.' AND '.$this->table.'.is_active = 1'
      : 'WHERE '.$this->table.'.is_active = 1';

    $order  = !empty($order)  ? 'ORDER BY '.$order : '';
    $limit  = !empty($limit)  ? 'LIMIT '.$limit : '';
    $offset = !empty($offset) ? 'OFFSET '.$offset : '';

    $query = 'SELECT '.$fields.
             ' FROM '.$this->table.
             $join.' '.
             $where.' '.
             $order.' '.
             $limit.' '.
             $offset;

    return $this->execute($query);
  }

  /**
   * Método responsável por executar atualizações no banco de dados
   * @param  string $where
   * @param  array $values [ field => value ]
   * @return boolean
   */
  public function update($where, $values){

    foreach ($values as $key => $value) {

      if (is_bool($value)) {
        $values[$key] = $value ? 1 : 0;
      }
    }

    $fields = array_keys($values);

    $query = 'UPDATE '.$this->table.
             ' SET '.implode('=?,', $fields).
             '=? WHERE '.$where.
             ' AND '.$this->table.'.is_active = 1';

    $this->execute($query, array_values($values));

    return true;
  }

  /**
   * Método responsável por excluir dados do banco
   * @param  string $where
   * @return boolean
   */
  public function delete($where){

    $query = 'DELETE FROM '.$this->table.
             ' WHERE '.$where.
             ' AND '.$this->table.'.is_active = 1';

    $this->execute($query);

    return true;
  }
}