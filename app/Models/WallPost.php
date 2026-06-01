<?php

namespace App\Models;

use App\Utils\BusinessException;

class WallPost extends Model {

    /**
     * Nome da tabela
     * @var string
     */
    protected static string $table = 'wall_posts';

    /**
     * ID da academia
     * @var string
     */
    public $academy_id;

    /**
     * ID do usuário (autor)
     * @var string
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
     * Método responsável por validar os dados do post
     *
     * @throws BusinessException
     * @return void
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
     * Método responsável por retornar os campos a serem gravados no banco
     *
     * @return array
     */
    protected function toArray(): array {
        return [
            'academy_id' => $this->academy_id,
            'user_id'    => $this->user_id,
            'title'      => $this->title,
            'content'    => $this->content,
            'is_active'  => $this->is_active,
            'updated_at' => date('Y-m-d H:i:s'),
        ];
    }

    /**
     * Método responsável por salvar ou atualizar o post
     *
     * @throws BusinessException
     * @return void
     */
    public function save(): void {

        $this->title   = trim($this->title ?? '') ?: null;
        $this->content = trim($this->content ?? '');

        $this->validate();

        if(!$this->id){
            $this->created_at = date('Y-m-d H:i:s');
        }

        parent::save();
    }

    /**
     * Método responsável por listar os posts pela academia com o nome do autor
     *
     * @param string $academyId
     * @return \PDOStatement
     */
    public static function listByAcademy($academyId){
        return static::db()->select(
            "wall_posts.academy_id = '".$academyId."' AND wall_posts.is_active = TRUE",
            "wall_posts.created_at DESC",
            null,
            null,
            "wall_posts.*, users.name AS author",
            "INNER JOIN users ON users.id = wall_posts.user_id"
        );
    }
}