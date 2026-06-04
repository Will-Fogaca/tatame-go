<?php

namespace App\Controllers\Admin;

use \App\Models\ClassSchedule;
use \App\Models\ClassModality;
use \App\Models\Academy;
use \App\Utils\View;
use \App\Utils\Pagination;
use \App\Utils\Page;
use \App\Session\LoginSession;

class ClassScheduleController {

    private const DEFAULT_PAGE_PATH = 'admin/class_schedule/';

    private static $days = [
        0 => 'Domingo',
        1 => 'Segunda-feira',
        2 => 'Terça-feira',
        3 => 'Quarta-feira',
        4 => 'Quinta-feira',
        5 => 'Sexta-feira',
        6 => 'Sábado',
    ];

    public static function getIndex($request) {

        $pagination  = null;
        $userId      = LoginSession::getUserId();
        $queryParams = $request->getQueryParams();
        $academyId   = $queryParams['academy_id'] ?? null;

        if (empty($academyId)) {
            $first = Academy::list("user_id = '" . $userId . "'", 'name ASC', 1)->fetchObject();
            if ($first) $academyId = $first->id;
        }

        $content = View::render(self::DEFAULT_PAGE_PATH . 'index', [
            'academies' => self::renderAcademyOptions($userId, $academyId),
            'schedules' => self::renderSchedules($request, $pagination, $academyId),
            'pagination'=> Page::getPagination($request, $pagination),
            'academy_id'=> $academyId ?? '',
        ]);

        return Page::getPage('Horários', $content);
    }

    public static function getCreate($request) {

        $userId    = LoginSession::getUserId();
        $academyId = $request->getQueryParams()['academy_id'] ?? null;

        $content = View::render(self::DEFAULT_PAGE_PATH . 'create', [
            'academies'  => self::renderAcademyOptions($userId, $academyId),
            'modalities' => self::renderModalityOptions($academyId),
            'weekdays'   => self::renderWeekdayOptions(),
            'academy_id' => $academyId ?? '',
            'start_time' => '',
            'end_time'   => '',
            'notes'      => '',
        ]);

        return Page::getPage('Novo Horário', $content);
    }

    public static function postCreate($request) {

        $postVars  = $request->getPostVars();
        $userId    = LoginSession::getUserId();
        $academyId = trim($postVars['academy_id'] ?? '');

        if (empty($academyId))           return Page::getPage('Erro', 'Academia não informada.');
        if (!isset($postVars['weekday'])) return Page::getPage('Erro', 'Dia da semana é obrigatório.');
        if (empty($postVars['start_time'])) return Page::getPage('Erro', 'Horário de início é obrigatório.');

        $academy = Academy::list("id = '" . $academyId . "' AND user_id = '" . $userId . "'")->fetchObject();
        if (!$academy) return Page::getPage('Erro', 'Academia não encontrada.');

        $schedule             = new ClassSchedule();
        $schedule->academy_id  = $academyId;
        $schedule->modality_id = $postVars['modality_id'] ?: null;
        $schedule->weekday     = (int) $postVars['weekday'];
        $schedule->start_time  = $postVars['start_time'];
        $schedule->end_time    = $postVars['end_time'] ?: null;
        $schedule->notes       = $postVars['notes']    ?: null;
        $schedule->save();

        $request->getRouter()->redirect('/admin/aula/horario?academy_id=' . $academyId);
    }

    public static function getUpdate($request) {

        $queryParams = $request->getQueryParams();
        $id          = $queryParams['id'] ?? null;
        $userId      = LoginSession::getUserId();

        if (empty($id)) return Page::getPage('Erro', 'Horário não informado.');

        $schedule = ClassSchedule::list("id = '" . $id . "'")->fetchObject(ClassSchedule::class);
        if (!$schedule) return Page::getPage('Erro', 'Horário não encontrado.');

        $content = View::render(self::DEFAULT_PAGE_PATH . 'update', [
            'id'         => $schedule->id,
            'academies'  => self::renderAcademyOptions($userId, $schedule->academy_id),
            'modalities' => self::renderModalityOptions($schedule->academy_id, $schedule->modality_id),
            'weekdays'   => self::renderWeekdayOptions($schedule->weekday),
            'academy_id' => $schedule->academy_id,
            'start_time' => $schedule->start_time ?? '',
            'end_time'   => $schedule->end_time   ?? '',
            'notes'      => $schedule->notes       ?? '',
        ]);

        return Page::getPage('Editar Horário', $content);
    }

    public static function postUpdate($request) {

        $postVars = $request->getPostVars();
        $id       = trim($postVars['id'] ?? '');

        if (empty($id)) return Page::getPage('Erro', 'ID não informado.');

        $schedule = ClassSchedule::list("id = '" . $id . "'")->fetchObject(ClassSchedule::class);
        if (!$schedule) return Page::getPage('Erro', 'Horário não encontrado.');

        $schedule->modality_id = $postVars['modality_id'] ?: null;
        $schedule->weekday     = (int) $postVars['weekday'];
        $schedule->start_time  = $postVars['start_time'];
        $schedule->end_time    = $postVars['end_time'] ?: null;
        $schedule->notes       = $postVars['notes']    ?: null;
        $schedule->save();

        $request->getRouter()->redirect('/admin/aula/horario?academy_id=' . $schedule->academy_id);
    }

    public static function postDelete($request) {

        $postVars  = $request->getPostVars();
        $id        = trim($postVars['id'] ?? '');
        $academyId = trim($postVars['academy_id'] ?? '');

        if (empty($id)) return Page::getPage('Erro', 'ID não informado.');

        $schedule = ClassSchedule::list("id = '" . $id . "'")->fetchObject(ClassSchedule::class);
        if (!$schedule) return Page::getPage('Erro', 'Horário não encontrado.');

        $schedule->delete();

        $request->getRouter()->redirect('/admin/aula/horario?academy_id=' . $academyId);
    }

    // -------------------------------------------------------------------------
    // Privados
    // -------------------------------------------------------------------------

    private static function renderSchedules($request, &$pagination, $academyId) {

        if (empty($academyId)) return '<tr><td colspan="5" class="text-center text-muted py-4">Selecione uma academia.</td></tr>';

        $join   = 'LEFT JOIN class_modalities cm ON cm.id = class_schedules.modality_id';
        $fields = 'class_schedules.id, class_schedules.academy_id, class_schedules.weekday,'
                . ' class_schedules.start_time, class_schedules.end_time, class_schedules.notes,'
                . ' cm.name AS modality_name';

        $where = "class_schedules.academy_id = '" . $academyId . "'";
        $total = ClassSchedule::list($where, null, null, null, 'COUNT(*) as qtd')->fetchObject()->qtd;

        $queryParams = $request->getQueryParams();
        $currentPage = $queryParams['page'] ?? 1;
        $pagination  = new Pagination($total, $currentPage, 15);

        $results = ClassSchedule::list(
            $where,
            'class_schedules.weekday ASC, class_schedules.start_time ASC',
            $pagination->getLimit(),
            $pagination->getOffset(),
            $fields,
            $join
        );

        $items  = '';
        $number = 1;

        while ($row = $results->fetchObject()) {
            $items .= View::render(self::DEFAULT_PAGE_PATH . 'item', [
                'id'            => $row->id,
                'number'        => $number++,
                'academy_id'    => $academyId,
                'weekday'       => self::$days[$row->weekday] ?? '—',
                'start_time'    => substr($row->start_time, 0, 5),
                'end_time'      => $row->end_time ? substr($row->end_time, 0, 5) : '—',
                'modality_name' => $row->modality_name ?? '—',
                'notes'         => $row->notes ?? '—',
            ]);
        }

        return $items ?: '<tr><td colspan="6" class="text-center text-muted py-4">Nenhum horário cadastrado.</td></tr>';
    }

    private static function renderAcademyOptions($userId, $selected = null) {
        $options = '';
        $results = Academy::list("user_id = '" . $userId . "'", 'name ASC');
        while ($a = $results->fetchObject()) {
            $sel      = ($a->id == $selected) ? 'selected' : '';
            $options .= '<option value="' . $a->id . '" ' . $sel . '>' . htmlspecialchars($a->name) . '</option>';
        }
        return $options;
    }

    private static function renderModalityOptions($academyId, $selected = null) {
        $options = '<option value="">— Nenhuma —</option>';
        if (empty($academyId)) return $options;
        $results = ClassModality::list("academy_id = '" . $academyId . "'", 'name ASC');
        while ($m = $results->fetchObject()) {
            $sel      = ($m->id == $selected) ? 'selected' : '';
            $options .= '<option value="' . $m->id . '" ' . $sel . '>' . htmlspecialchars($m->name) . '</option>';
        }
        return $options;
    }

    private static function renderWeekdayOptions($selected = null) {
        $options = '';
        foreach (self::$days as $value => $label) {
            $sel      = ($value == $selected) ? 'selected' : '';
            $options .= '<option value="' . $value . '" ' . $sel . '>' . $label . '</option>';
        }
        return $options;
    }
}