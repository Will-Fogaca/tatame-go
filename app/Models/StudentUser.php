<?php

namespace App\Models;

use App\Utils\Database;

class StudentUser extends Model {

    protected static string $table = 'student_user';

    public $user_id    = null;
    public $student_id = null;

    // -------------------------------------------------------------------------
    // Getters
    // -------------------------------------------------------------------------

    public function getId()        { return $this->id; }
    public function getUserId()    { return $this->user_id; }
    public function getStudentId() { return $this->student_id; }
    public function getCreatedAt() { return $this->created_at; }
    public function isActive()     { return (bool) $this->is_active; }

    // -------------------------------------------------------------------------
    // Implementação obrigatória do Model
    // -------------------------------------------------------------------------

    protected function toArray(): array {
        return [
            'user_id'    => $this->user_id,
            'student_id' => $this->student_id,
            'is_active'  => $this->is_active,
        ];
    }
}