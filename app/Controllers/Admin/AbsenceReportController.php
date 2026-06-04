<?php

namespace App\Controllers\Admin;

use \App\Models\Academy;
use \App\Utils\View;
use \App\Utils\Page;
use \App\Session\LoginSession;

class AbsenceReportController {

    private const DEFAULT_PAGE_PATH = 'admin/absence_report/';

    public static function getIndex($request) {

        $userId      = LoginSession::getUserId();
        $queryParams = $request->getQueryParams();
        $academyId   = $queryParams['academy_id'] ?? null;
        $tab         = $queryParams['tab'] ?? 'by_class';
        $search      = trim($queryParams['search'] ?? '');

        if (empty($academyId)) {
            $first = Academy::list("user_id = '" . $userId . "'", 'name ASC', 1)->fetchObject();
            if ($first) $academyId = $first->id;
        }

        $filterStudent = '';
        if ($tab === 'by_student') {
            $filterStudent = '<div class="input-group input-group-sm" style="width:260px;">'
                           . '<input type="text" name="search" class="form-control" placeholder="Filtrar aluno..." value="' . htmlspecialchars($search) . '">'
                           . '<button type="submit" class="btn btn-outline-secondary"><i class="bi bi-search"></i></button>'
                           . '</div>';
        }

        if ($tab === 'by_student') {
            $rows  = self::fetchByStudent($academyId, $search);
            $table = self::buildByStudentTable($rows);
        } else {
            $tab   = 'by_class';
            $rows  = self::fetchByClass($academyId);
            $table = self::buildByClassTable($rows);
        }

        $content = View::render(self::DEFAULT_PAGE_PATH . 'index', [
            'academies'      => self::renderAcademyOptions($userId, $academyId),
            'academy_id'     => $academyId ?? '',
            'tab'            => $tab,
            'tab_by_class'   => $tab === 'by_class'   ? 'active' : '',
            'tab_by_student' => $tab === 'by_student' ? 'active' : '',
            'filter_student' => $filterStudent,
            'table'          => $table,
        ]);

        return Page::getPage('Relatório de Faltas', $content);
    }

    // -------------------------------------------------------------------------
    // Fetch de dados
    // -------------------------------------------------------------------------

    private static function fetchByClass($academyId) {

        if (empty($academyId)) return [];

        $sql = "SELECT
                    c.class_date,
                    c.start_time,
                    cm.name  AS modality_name,
                    COUNT(ca.id)                                      AS total_students,
                    SUM(CASE WHEN ca.present = 0 THEN 1 ELSE 0 END)  AS total_absences,
                    SUM(CASE WHEN ca.present = 1 THEN 1 ELSE 0 END)  AS total_present
                FROM classes c
                LEFT JOIN class_modalities  cm ON cm.id = c.modality_id
                LEFT JOIN class_attendances ca ON ca.class_id = c.id AND ca.is_active = 1
                WHERE c.academy_id = '" . $academyId . "'
                  AND c.is_active  = 1
                GROUP BY c.id, c.class_date, c.start_time, cm.name
                ORDER BY c.class_date DESC, c.start_time DESC";

        $stmt = (new \App\Utils\Database('classes'))->execute($sql);
        $rows = [];
        while ($row = $stmt->fetchObject()) $rows[] = $row;
        return $rows;
    }

    private static function fetchByStudent($academyId, $search = '') {

        if (empty($academyId)) return [];

        $searchWhere = !empty($search)
            ? "AND s.name LIKE '%" . addslashes($search) . "%'"
            : '';

        $sql = "SELECT
                    s.name AS student_name,
                    COUNT(ca.id)                                      AS total_classes,
                    SUM(CASE WHEN ca.present = 0 THEN 1 ELSE 0 END)  AS total_absences,
                    SUM(CASE WHEN ca.present = 1 THEN 1 ELSE 0 END)  AS total_present
                FROM academy_students acs
                INNER JOIN students s ON s.id = acs.student_id AND s.is_active = 1 {$searchWhere}
                LEFT JOIN class_attendances ca ON ca.student_id = s.id AND ca.is_active = 1
                LEFT JOIN classes c ON c.id = ca.class_id AND c.academy_id = '" . $academyId . "' AND c.is_active = 1
                WHERE acs.academy_id = '" . $academyId . "'
                  AND acs.is_active  = 1
                GROUP BY s.id, s.name
                ORDER BY total_absences DESC, s.name ASC";

        $stmt = (new \App\Utils\Database('academy_students'))->execute($sql);
        $rows = [];
        while ($row = $stmt->fetchObject()) $rows[] = $row;
        return $rows;
    }

    // -------------------------------------------------------------------------
    // Build table — desktop (tabela) + mobile (cards)
    // -------------------------------------------------------------------------

    private static function buildByClassTable($rows) {

        if (empty($rows)) {
            $empty = '<p class="text-muted text-center py-4">Nenhuma aula registrada.</p>';
            return '<div class="card"><div class="card-body">' . $empty . '</div></div>';
        }

        // Desktop
        $tbody = '';
        foreach ($rows as $row) {
            $pct    = $row->total_students > 0 ? round(($row->total_absences / $row->total_students) * 100) : 0;
            $tbody .= '<tr>'
                    . '<td>' . date('d/m/Y', strtotime($row->class_date)) . '</td>'
                    . '<td>' . ($row->start_time ? substr($row->start_time, 0, 5) : '—') . '</td>'
                    . '<td>' . ($row->modality_name ?? '—') . '</td>'
                    . '<td class="text-center">' . $row->total_students . '</td>'
                    . '<td class="text-center text-success fw-semibold">' . $row->total_present . '</td>'
                    . '<td class="text-center text-danger fw-semibold">' . $row->total_absences . '</td>'
                    . '<td class="text-center"><span class="badge bg-secondary">' . $pct . '%</span></td>'
                    . '</tr>';
        }

        $table = '<div class="card d-none d-md-block"><div class="card-body p-0">'
               . '<table class="table table-hover mb-0"><thead class="table-light"><tr>'
               . '<th>Data</th><th>Início</th><th>Modalidade</th>'
               . '<th class="text-center">Total</th><th class="text-center">Presentes</th>'
               . '<th class="text-center">Faltas</th><th class="text-center">% Falta</th>'
               . '</tr></thead><tbody>' . $tbody . '</tbody></table>'
               . '</div></div>';

        // Mobile
        $cards = '<div class="d-md-none d-flex flex-column gap-2">';
        foreach ($rows as $row) {
            $pct    = $row->total_students > 0 ? round(($row->total_absences / $row->total_students) * 100) : 0;
            $date   = date('d/m/Y', strtotime($row->class_date));
            $time   = $row->start_time ? substr($row->start_time, 0, 5) : '—';
            $mod    = $row->modality_name ?? '—';
            $cards .= '<div class="card border-0 shadow-sm">'
                    . '<div class="card-body py-2 px-3">'
                    . '<div class="d-flex justify-content-between align-items-start mb-1">'
                    . '<span class="fw-semibold">' . $date . ' · ' . $time . '</span>'
                    . '<span class="badge bg-secondary">' . $pct . '% falta</span>'
                    . '</div>'
                    . '<small class="text-muted">' . $mod . '</small>'
                    . '<div class="d-flex gap-3 mt-2">'
                    . '<span class="small">Total: <strong>' . $row->total_students . '</strong></span>'
                    . '<span class="small text-success">Presentes: <strong>' . $row->total_present . '</strong></span>'
                    . '<span class="small text-danger">Faltas: <strong>' . $row->total_absences . '</strong></span>'
                    . '</div>'
                    . '</div></div>';
        }
        $cards .= '</div>';

        return $table . $cards;
    }

    private static function buildByStudentTable($rows) {

        if (empty($rows)) {
            $empty = '<p class="text-muted text-center py-4">Nenhum aluno encontrado.</p>';
            return '<div class="card"><div class="card-body">' . $empty . '</div></div>';
        }

        // Desktop
        $tbody  = '';
        $number = 1;
        foreach ($rows as $row) {
            $pct   = $row->total_classes > 0 ? round(($row->total_absences / $row->total_classes) * 100) : 0;
            $badge = $pct >= 25 ? 'danger' : ($pct >= 10 ? 'warning' : 'success');
            $tbody .= '<tr>'
                    . '<td>' . $number++ . '</td>'
                    . '<td>' . htmlspecialchars($row->student_name) . '</td>'
                    . '<td class="text-center">' . $row->total_classes . '</td>'
                    . '<td class="text-center text-success fw-semibold">' . $row->total_present . '</td>'
                    . '<td class="text-center text-danger fw-semibold">' . $row->total_absences . '</td>'
                    . '<td class="text-center"><span class="badge bg-' . $badge . '">' . $pct . '%</span></td>'
                    . '</tr>';
        }

        $table = '<div class="card d-none d-md-block"><div class="card-body p-0">'
               . '<table class="table table-hover mb-0"><thead class="table-light"><tr>'
               . '<th>#</th><th>Aluno</th><th class="text-center">Aulas</th>'
               . '<th class="text-center">Presentes</th><th class="text-center">Faltas</th>'
               . '<th class="text-center">% Falta</th>'
               . '</tr></thead><tbody>' . $tbody . '</tbody></table>'
               . '</div></div>';

        // Mobile
        $cards = '<div class="d-md-none d-flex flex-column gap-2">';
        foreach ($rows as $row) {
            $pct   = $row->total_classes > 0 ? round(($row->total_absences / $row->total_classes) * 100) : 0;
            $badge = $pct >= 25 ? 'danger' : ($pct >= 10 ? 'warning' : 'success');
            $cards .= '<div class="card border-0 shadow-sm">'
                    . '<div class="card-body py-2 px-3">'
                    . '<div class="d-flex justify-content-between align-items-center mb-1">'
                    . '<span class="fw-semibold">' . htmlspecialchars($row->student_name) . '</span>'
                    . '<span class="badge bg-' . $badge . '">' . $pct . '% falta</span>'
                    . '</div>'
                    . '<div class="d-flex gap-3 mt-1">'
                    . '<span class="small">Aulas: <strong>' . $row->total_classes . '</strong></span>'
                    . '<span class="small text-success">Presentes: <strong>' . $row->total_present . '</strong></span>'
                    . '<span class="small text-danger">Faltas: <strong>' . $row->total_absences . '</strong></span>'
                    . '</div>'
                    . '</div></div>';
        }
        $cards .= '</div>';

        return $table . $cards;
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private static function renderAcademyOptions($userId, $selected = null) {
        $options = '';
        $results = Academy::list("user_id = '" . $userId . "'", 'name ASC');
        while ($a = $results->fetchObject()) {
            $sel      = ($a->id == $selected) ? 'selected' : '';
            $options .= '<option value="' . $a->id . '" ' . $sel . '>' . htmlspecialchars($a->name) . '</option>';
        }
        return $options;
    }
}