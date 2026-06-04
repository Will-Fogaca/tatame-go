<?php

namespace App\Models;

use App\Utils\Database;

class ClassAttendance extends Model {

    protected static string $table = 'class_attendances';

    public $class_id   = null;
    public $student_id = null;
    public $present    = 0;

    protected function toArray(): array {
        return [
            'class_id'   => $this->class_id,
            'student_id' => $this->student_id,
            'present'    => $this->present,
            'is_active'  => 1,
        ];
    }

    /**
     * Busca todos os alunos vinculados à academia com status de presença na aula
     * Se ainda não há registro de presença, retorna present = 0 (ausente por padrão)
     *
     * @param string $classId
     * @param string $academyId
     * @return \PDOStatement
     */
    public static function getStudentsByAcademy($classId, $academyId) {

        $sql = "SELECT
                    s.id   AS student_id,
                    s.name AS student_name,
                    COALESCE(ca.present, 0) AS present
                FROM academy_students acs
                INNER JOIN students s ON s.id = acs.student_id AND s.is_active = 1
                LEFT JOIN class_attendances ca
                    ON ca.class_id   = '" . $classId . "'
                    AND ca.student_id = s.id
                    AND ca.is_active  = 1
                WHERE acs.academy_id = '" . $academyId . "'
                  AND acs.is_active  = 1
                ORDER BY s.name ASC";

        return (new Database('academy_students'))->execute($sql);
    }
}