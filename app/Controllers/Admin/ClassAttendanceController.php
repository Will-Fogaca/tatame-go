<?php

namespace App\Controllers\Admin;

use \App\Models\ClassModel;
use \App\Models\ClassAttendance;
use \App\Utils\View;
use \App\Utils\Page;
use \App\Utils\Database;
use \App\Session\LoginSession;

class ClassAttendanceController {

    private const DEFAULT_PAGE_PATH = 'admin/class_attendance/';

    /**
    * Retorna a tela inicial das presenças em aulas
    *
    * @param [type] $request
    * @return void
    */
    public static function getIndex($request) {

        $queryParams = $request->getQueryParams();
        $classId     = $queryParams['id'] ?? null;

        if (empty($classId)) return Page::getPage('Erro', 'Aula não informada.');

        $class = ClassModel::listWithDetails("classes.id = '" . $classId . "'")->fetchObject();
        if (!$class) return Page::getPage('Erro', 'Aula não encontrada.');

        $content = View::render(self::DEFAULT_PAGE_PATH . 'index', [
            'class_id'      => $class->id,
            'academy_id'    => $class->academy_id,
            'class_date'    => date('d/m/Y', strtotime($class->class_date)),
            'start_time'    => $class->start_time ? substr($class->start_time, 0, 5) : '—',
            'modality_name' => $class->modality_name ?? '—',
            'students'      => self::renderStudents($class->id, $class->academy_id),
        ]);

        return Page::getPage('Presença', $content);
    }

    /**
     * Método responsável por gravar uma presença em aula
     *
     * @param [type] $request
     * @return void
     */
    public static function postSave($request) {

        $postVars = $request->getPostVars();
        $classId  = trim($postVars['class_id'] ?? '');
        $academyId= trim($postVars['academy_id'] ?? '');

        if (empty($classId)) return Page::getPage('Erro', 'Aula não informada.');

        $presentIds = $postVars['present'] ?? [];
        $results = ClassAttendance::getStudentsByAcademy($classId, $academyId);

        while ($row = $results->fetchObject()) {

            $isPresent = in_array($row->student_id, $presentIds) ? 1 : 0;

            $existing = ClassAttendance::list("class_id = '" . $classId . "' AND student_id = '" . $row->student_id . "'")->fetchObject(ClassAttendance::class);

            if ($existing) {
                (new Database('class_attendances'))->update("id = '" . $existing->id . "'", ['present' => $isPresent, 'updated_at' => date('Y-m-d H:i:s')]);
            } else {
                $attendance             = new ClassAttendance();
                $attendance->class_id   = $classId;
                $attendance->student_id = $row->student_id;
                $attendance->present    = $isPresent;
                $attendance->save();
            }
        }

        $request->getRouter()->redirect('/admin/aula?academy_id=' . $academyId);
    }

    /**
     * Método responsável por renderizar os alunos
     *
     * @param string $classId
     * @param string $academyId
     * @return void
     */
    private static function renderStudents($classId, $academyId) {

        $results = ClassAttendance::getStudentsByAcademy($classId, $academyId);
        $items   = '';

        while ($row = $results->fetchObject()) {
            $items .= View::render(self::DEFAULT_PAGE_PATH . 'item', [
                'student_id'   => $row->student_id,
                'student_name' => $row->student_name,
                'present'      => $row->present ? 'checked' : '',
            ]);
        }

        return $items ?: '<p class="text-muted">Nenhum aluno vinculado a esta academia.</p>';
    }
}