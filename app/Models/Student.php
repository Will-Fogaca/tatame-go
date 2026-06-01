<?php

namespace App\Models;

use App\Utils\BusinessException;

class Student extends Model {

    /**
     * Nome da tabela
     * @var string
     */
    protected static string $table = 'students';

    /**
     * Nome do aluno
     * @var string
     */
    public $name;

    /**
     * Data de nascimento
     * @var string (YYYY-MM-DD)
     */
    public $birth_date;

    /**
     * Telefone do aluno
     * @var string|null
     */
    public $phone_number;

    /**
     * Nome do responsável (pai, mãe, etc)
     * @var string|null
     */
    public $guardian_name;

    /**
     * Telefone do responsável
     * @var string|null
     */
    public $guardian_phone;

    /**
     * Observações do aluno
     * @var string|null
     */
    public $notes;


    /**
     * Método responsável por remover tudo que não é dígito de um telefone
     *
     * @param string|null $phone
     * @return string|null
     */
    private static function sanitizePhone(?string $phone): ?string {
        if(empty($phone)) return null;
        $digits = preg_replace('/\D/', '', $phone);
        return $digits ?: null;
    }

    /**
     * Método responsável por validar os dados do aluno antes de salvar
     *
     * @throws BusinessException
     * @return void
     */
    private function validate(): void {

        if(empty(trim($this->name ?? ''))){
            throw new BusinessException('O nome do aluno é obrigatório.');
        }
        if(strlen(trim($this->name)) < 3){
            throw new BusinessException('O nome do aluno deve ter pelo menos 3 caracteres.');
        }
        if(strlen(trim($this->name)) > 100){
            throw new BusinessException('O nome do aluno deve ter no máximo 100 caracteres.');
        }

        if(empty($this->birth_date)){
            throw new BusinessException('A data de nascimento é obrigatória.');
        }

        $birthDate = \DateTime::createFromFormat('Y-m-d', $this->birth_date);
        if(!$birthDate){
            throw new BusinessException('A data de nascimento é inválida.');
        }

        $today = new \DateTime();
        if($birthDate >= $today){
            throw new BusinessException('A data de nascimento deve ser anterior à data atual.');
        }

        $age = $today->diff($birthDate)->y;
        if($age > 120){
            throw new BusinessException('A data de nascimento informada é inválida.');
        }

        if(!empty($this->phone_number)){
            $phoneDigits = preg_replace('/\D/', '', $this->phone_number);
            if(strlen($phoneDigits) < 10 || strlen($phoneDigits) > 11){
                throw new BusinessException('O telefone do aluno deve ter 10 ou 11 dígitos.');
            }
        }

        if($age < 18){
            if(empty(trim($this->guardian_name ?? ''))){
                throw new BusinessException('Para menores de 18 anos, o nome do responsável é obrigatório.');
            }
            if(empty(trim($this->guardian_phone ?? ''))){
                throw new BusinessException('Para menores de 18 anos, o telefone do responsável é obrigatório.');
            }
        }

        if(!empty($this->guardian_phone)){
            $guardianPhoneDigits = preg_replace('/\D/', '', $this->guardian_phone);
            if(strlen($guardianPhoneDigits) < 10 || strlen($guardianPhoneDigits) > 11){
                throw new BusinessException('O telefone do responsável deve ter 10 ou 11 dígitos.');
            }
        }

        if(!empty($this->guardian_name) && strlen(trim($this->guardian_name)) < 3){
            throw new BusinessException('O nome do responsável deve ter pelo menos 3 caracteres.');
        }

        if(!empty($this->notes) && strlen($this->notes) > 500){
            throw new BusinessException('As observações devem ter no máximo 500 caracteres.');
        }
    }

    /**
     * Método responsável por retornar os campos a serem gravados no banco
     *
     * @return array
     */
    protected function toArray(): array {
        return [
            'id'          => $this->id,
            'name'        => $this->name        ?: null,
            'birth_date'  => $this->birth_date  ?: null,
            'phone_number'  => $this->phone_number  ?: null,
            'guardian_name'  => $this->guardian_name  ?: null,
            'guardian_phone' => $this->guardian_phone ?: null,
            'notes'       => $this->notes       ?: null,
            'is_active'   => 1,
            'created_at'  => $this->created_at,
        ];
    }
    
    /**
     * Método responsável por salvar ou atualizar o aluno no banco
     *
     * @throws BusinessException
     * @return void
     */
    public function save(): void {

        $this->name           = trim($this->name ?? '');
        $this->guardian_name  = trim($this->guardian_name ?? '') ?: null;
        $this->notes          = trim($this->notes ?? '') ?: null;
        $this->phone_number   = self::sanitizePhone($this->phone_number);
        $this->guardian_phone = self::sanitizePhone($this->guardian_phone);

        $this->validate();

        $isNew = empty($this->id); 

        if($isNew){
            $this->id         = \Ramsey\Uuid\Uuid::uuid4()->toString();
            $this->created_at = date('Y-m-d H:i:s');
        }

        $isNew ? $this->performInsert() : $this->performUpdate();
    }
}