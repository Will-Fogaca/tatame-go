<?php

namespace App\Controllers\User;

use \App\Models\Academy;
use \App\Utils\View;
use \App\Utils\Page;
use \App\Session\LoginSession;

class AbsenceController {

    private const DEFAULT_PAGE_PATH = 'user/absence/';

    public static function getIndex($request) {

        $userId      = LoginSession::getUserId();
        $queryParams = $request->getQueryParams();
        $studentId   = $queryParams['student_id'] ?? null;

        // Busca os alunos vinculados ao usuário
        $students = self::getLinkedStudents($userId);

        // Se não veio student_id, pega o primeiro
        if (empty($studentId) && !empty($students)) {
            $studentId = $students[0]->student_id;
        }

        $summary  = '';
        $history  = '';

        if ($studentId) {
            $summary = self::renderSummary($studentId);
            $history = self::renderHistory($studentId);
        }

        $content = View::render(self::DEFAULT_PAGE_PATH . 'index', [
            'student_tabs' => self::renderStudentTabs($students, $studentId),
            'summary'      => $summary,
            'history'      => $history,
            'student_id'   => $studentId ?? '',
            'empty'        => empty($students) ? '' : 'd-none',
        ]);

        return Page::getPage('Minhas Presenças', $content);
    }

    // -------------------------------------------------------------------------
    // Privados
    // -------------------------------------------------------------------------

    private static function getLinkedStudents($userId) {

        $sql = "SELECT s.id AS student_id, s.name AS student_name
                FROM student_user su
                INNER JOIN students s ON s.id = su.student_id AND s.is_active = 1
                WHERE su.user_id  = '" . $userId . "'
                  AND su.is_active = 1
                ORDER BY s.name ASC";

        $stmt    = (new \App\Utils\Database('student_user'))->execute($sql);
        $results = [];
        while ($row = $stmt->fetchObject()) {
            $results[] = $row;
        }
        return $results;
    }

    private static function renderStudentTabs($students, $selectedId) {
        $tabs = '';
        foreach ($students as $s) {
            $active = ($s->student_id == $selectedId) ? 'active' : '';
            $tabs  .= View::render(self::DEFAULT_PAGE_PATH . 'student_tab', [
                'student_id'   => $s->student_id,
                'student_name' => $s->student_name,
                'active'       => $active,
            ]);
        }
        return $tabs;
    }

    private static function renderSummary($studentId) {

        $sql = "SELECT
                    a.name AS academy_name,
                    COUNT(ca.id)                                      AS total_classes,
                    SUM(CASE WHEN ca.present = 1 THEN 1 ELSE 0 END)  AS total_present,
                    SUM(CASE WHEN ca.present = 0 THEN 1 ELSE 0 END)  AS total_absences
                FROM class_attendances ca
                INNER JOIN classes c ON c.id = ca.class_id AND c.is_active = 1
                INNER JOIN academies a ON a.id = c.academy_id AND a.is_active = 1
                WHERE ca.student_id = '" . $studentId . "'
                  AND ca.is_active  = 1
                GROUP BY a.id, a.name
                ORDER BY a.name ASC";

        $results = (new \App\Utils\Database('class_attendances'))->execute($sql);
        $items   = '';

        while ($row = $results->fetchObject()) {
            $pct    = $row->total_classes > 0
                ? round(($row->total_present / $row->total_classes) * 100)
                : 0;
            $badge  = $pct >= 75 ? 'success' : ($pct >= 50 ? 'warning' : 'danger');
            $items .= View::render(self::DEFAULT_PAGE_PATH . 'summary_item', [
                'academy_name'  => $row->academy_name,
                'total_classes' => $row->total_classes,
                'total_present' => $row->total_present,
                'total_absences'=> $row->total_absences,
                'presence_pct'  => $pct,
                'badge'         => $badge,
            ]);
        }

        return $items ?: '<p class="text-muted">Nenhuma presença registrada ainda.</p>';
    }

    private static function renderHistory($studentId) {

        $sql = "SELECT
                    c.class_date,
                    c.start_time,
                    a.name  AS academy_name,
                    cm.name AS modality_name,
                    ca.present
                FROM class_attendances ca
                INNER JOIN classes c          ON c.id  = ca.class_id   AND c.is_active = 1
                INNER JOIN academies a         ON a.id  = c.academy_id  AND a.is_active = 1
                LEFT  JOIN class_modalities cm ON cm.id = c.modality_id
                WHERE ca.student_id = '" . $studentId . "'
                  AND ca.is_active  = 1
                ORDER BY c.class_date DESC, c.start_time DESC";

        $results = (new \App\Utils\Database('class_attendances'))->execute($sql);
        $items   = '';

        while ($row = $results->fetchObject()) {
            $items .= View::render(self::DEFAULT_PAGE_PATH . 'history_item', [
                'class_date'    => date('d/m/Y', strtotime($row->class_date)),
                'start_time'    => $row->start_time ? substr($row->start_time, 0, 5) : '—',
                'academy_name'  => $row->academy_name,
                'modality_name' => $row->modality_name ?? '—',
                'present'       => $row->present ? 'Presente' : 'Ausente',
                'badge'         => $row->present ? 'success' : 'danger',
            ]);
        }

        return $items ?: '<tr><td colspan="5" class="text-center text-muted py-4">Nenhum histórico encontrado.</td></tr>';
    }
}