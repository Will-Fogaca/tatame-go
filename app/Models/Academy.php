<?php 

namespace App\Models;

use App\Utils\Database;

class Academy{

    /**
     * Id
     * @var string
     */
    public $id;

    /**
     * Id do usuário
     * @var string
     */
    public string $user_id;

    /**
     * Nome da academia
     *
     * @var string
     */
    public string $name;

    /**
     * Número de telefone da academia
     *
     * @var string
     */
    public ?string $phone_number = null;

    /**
     * Data de criação da academia
     *
     * @var string|null
     */
    public ?string $created_at = null;

    /**
     * Data de modificação da academia
     *
     * @var string|null
     */
    public ?string $updated_at = null;

    /**
     * Flag de ativo ou não da academia
     *
     * @var boolean
     */
    public bool $is_active = true;


    /**
     * Método responsável por retornar o id da academia
     *
     * @return string
     */
    public function getId(): string{
        return $this->id;
    }

    /**
     * Método responsável por retornar o id do usuário
     *
     * @return string
     */
    public function getUserId(): string{
        return $this->user_id;
    }

    /**
     * Método responsável por retornar o nome da academia
     *
     * @return string
     */
    public function getName(): string{
        return $this->name;
    }

    /**
     * Método responsável por retornar o telefone da academia
     *
     * @return string|null
     */
    public function getPhoneNumber(): ?string{
        return $this->phone_number;
    }

    /**
     * Método responsável por retornar a data de criação da academia
     *
     * @return string|null
     */
    public function getCreatedAt(): ?string{
        return $this->created_at;
    }

    /**
     * Método responsável por retornar a data de atualização da academia
     *
     * @return string|null
     */
    public function getUpdatedAt(): ?string{
        return $this->updated_at;
    }

    /**
     * Método responsável por retornar o status da academia
     *
     * @return boolean
     */
    public function isActive(): bool{
        return $this->is_active;
    }

    /**
     * Método responsável por retornar todas as academias
     *
     * @param string $where
     * @param string $order
     * @param string $limit
     * @param string $offset
     * @param string $fields
     * @return \PDOStatement
     */
    public static function list($where = null, $order = null, $limit = null, $offset = null, $fields = '*'){
        return (new Database('academies'))->select($where, $order, $limit, $offset, $fields);        
    }

    /**
     * Método responsável por salvar a academia
     *
     * @return void
     */
    public function save(){
        $this->id = (new Database('academies'))->insert([
            'user_id' => $this->user_id,
            'name' => $this->name, 
            'phone_number' => $this->phone_number,
            'is_active' => $this->is_active
        ]);
    }


    public function update(){

        return (new Database('academies'))->update(
            "id = '".$this->id."'",
            [
                'name' => $this->name,
                'phone_number' => $this->phone_number,
                'is_active' => $this->is_active,
                'updated_at' => date('Y-m-d H:i:s')
            ]
        );
    }

   public function delete(){

        return (new Database('academies'))->update(
            "id = '".$this->id."'",
            [
                'is_active' => false,
                'updated_at' => date('Y-m-d H:i:s')
            ]
        );
    }

}