<?php

namespace App\Model\Entity;

use Ramsey\Uuid\Uuid;
class Academy{

    /**
     * Id da academia
     * @var string -> UUID
     */
    private string $id; 

    /**
     * Nome da academia
     * @var string
     */
    private string $name;
    
    /**
     * Email da academia
     * @var string
     */
    private string $email;
    
    public function __construct($name, $email)
    {
        $this->id = Uuid::uuid4() -> toString();
        $this->name = $name;
        $this->email = $email;
    }

    public function getId(){
        return $this->id;
    }

    public function getName(){
        return $this->name;
    }

    public function getEmail(){
        return $this->email;
    }

}



