<?php

namespace App\Models;

use App\Utils\Database;
use App\Utils\BusinessException;

class WallPost
{
    /**
     * ID do post
     * @var string UUID
     */
    public $id;

    /**
     * ID da academia
     * @var string UUID
     */
    public $academy_id;

    /**
     * ID do usuário (autor)
     * @var string UUID
     */
    public $user_id;

    /**
     * Título do post
     * @var string|null
     */
    public $title;

    /**
     * Conteúdo do post
     * @var string
     */
    public $content;

    /**
     * Data de criação
     * @var string
     */
    public $created_at;

    /**
     * Data de atualização
     * @var string
     */
    public $updated_at;

    /**
     * Status ativo/inativo
     * @var bool
     */
    public $is_active;

    /**
     * Valida os dados do post
     * @throws BusinessException
     */
    private function validate(): void {

        if(empty($this->academy_id)){
            throw new BusinessException('A academia é obrigatória.');
        }

        if(empty($this->user_id)){
            throw new BusinessException('O autor do post é obrigatório.');
        }

        if(!empty($this->title)){
            if(strlen(trim($this->title)) < 3){
                throw new BusinessException('O título deve ter pelo menos 3 caracteres.');
            }

            if(strlen($this->title) > 150){
                throw new BusinessException('O título deve ter no máximo 150 caracteres.');
            }
        }

        if(empty(trim($this->content ?? ''))){
            throw new BusinessException('O conteúdo do aviso é obrigatório.');
        }

        if(strlen($this->content) > 2000){
            throw new BusinessException('O conteúdo deve ter no máximo 2000 caracteres.');
        }
    }

    /**
     * Método responsável por salvar ou atualizar o post
     * @throws BusinessException
     */
    public function save(): void {

        $this->title   = trim($this->title ?? '') ?: null;
        $this->content = trim($this->content ?? '');

        $this->validate();

        $now = date('Y-m-d H:i:s');

        $data = [
            'academy_id' => $this->academy_id,
            'user_id'    => $this->user_id,
            'title'      => $this->title,
            'content'    => $this->content,
            'is_active'  => $this->is_active ?? true,
            'updated_at' => $now
        ];

        if($this->id){
            (new Database('wall_posts'))->update("id = '{$this->id}'", $data);
        } else {
            $this->created_at   = $now;
            $data['created_at'] = $this->created_at;

            $this->id = (new Database('wall_posts'))->insert($data);
        }
    }

    /**
     * Método responsável por listar posts
     * @param string $where
     * @param string $order
     * @param string $limit
     * @param string $offset
     * @param string $fields
     * @return \PDOStatement
     */
    public static function list($where = null, $order = null, $limit = null, $offset = null, $fields = '*'){
        return (new Database('wall_posts'))->select($where, $order, $limit, $offset, $fields);
    }


    /**
     * Método responsável por listar os posts pela academia
     *
     * @param string $academyId
     * @return \PDOStatement
     */
    public static function listByAcademy($academyId){

        return (new Database('wall_posts'))->select(
            "wall_posts.academy_id = '".$academyId."' AND wall_posts.is_active = TRUE",
            "wall_posts.created_at DESC",
            null,
            null,
            "wall_posts.*, users.name AS author",
            "INNER JOIN users ON users.id = wall_posts.user_id"
        );
    }

    /**
     * Método responsável por excluir (soft delete) o post
     * @throws BusinessException
     */
    public function delete(): void {

        if(empty($this->id)){
            throw new BusinessException('Post não informado para exclusão.');
        }

        $now = date('Y-m-d H:i:s');

        (new Database('wall_posts'))->update(
            "id = '{$this->id}'",
            [
                'is_active' => false,
                'updated_at' => $now
            ]
        );
    }
}