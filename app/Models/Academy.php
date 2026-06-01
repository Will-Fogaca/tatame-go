<?php 

namespace App\Models;

class Academy extends Model {

    /**
     * Nome da tabela
     * @var string
     */
    protected static string $table = 'academies';

  
    /**
     * Id do usuário
     * @var string
     */
    public string $user_id;

    /**
     * Nome da academia
     * @var string
     */
    public string $name;

    /**
     * Número de telefone da academia
     * @var string|null
     */
    public ?string $phone_number = null;


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
     * Método responsável por retornar os campos a serem gravados no banco
     *
     * @return array
     */
    protected function toArray(): array{
        return [
            'user_id'      => $this->user_id,
            'name'         => $this->name,
            'phone_number' => $this->phone_number,
            'is_active'    => $this->is_active,
        ];
    }
}