<?php

namespace App\Controllers\Admin;

use \App\Models\ClassModel;
use \App\Models\Academy;
use \App\Models\ClassSchedule;
use \App\Models\ClassModality;
use \App\Utils\View;
use \App\Utils\Pagination;
use \App\Utils\Page;
use \App\Session\LoginSession;

class ClassController {

    private const DEFAULT_PAGE_PATH = 'admin/class/';

    /**
     * Método responsável por renderizar a tela inicial das aulas
     *
     * @param Request $request
     * @return string
     */
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
            'academies'  => self::renderAcademyOptions($userId, $academyId),
            'classes'    => self::renderClasses($request, $pagination, $academyId),
            'pagination' => Page::getPagination($request, $pagination),
            'academy_id' => $academyId ?? '',
        ]);

        return Page::getPage('Aulas', $content);
    }

    /**
     * Método responsável por renderizar a tela de lançamento de aulas
     *
     * @param Request $request
     * @return string
     */
    public static function getCreate($request) {

        $userId      = LoginSession::getUserId();
        $queryParams = $request->getQueryParams();
        $academyId   = $queryParams['academy_id'] ?? null;

        $content = View::render(self::DEFAULT_PAGE_PATH . 'create', [
            'academies'  => self::renderAcademyOptions($userId, $academyId),
            'schedules'  => self::renderScheduleOptions($academyId),
            'modalities' => self::renderModalityOptions($academyId),
            'academy_id' => $academyId ?? '',
            'schedule_id'=> '',
            'modality_id'=> '',
            'class_date' => date('Y-m-d'),
            'start_time' => '',
            'end_time'   => '',
            'notes'      => '',
            'error'      => '',
        ]);

        return Page::getPage('Nova Aula', $content);
    }

    /**
     * Método responsável por gravar uma aula
     *
     * @param Request $request
     * @return void
     */
    public static function postCreate($request) {

        $postVars  = $request->getPostVars();
        $userId    = LoginSession::getUserId();
        $academyId = trim($postVars['academy_id'] ?? '');

        if (empty($academyId)) {
            return Page::getPage('Erro', 'Academia não informada.');
        }

        // Verifica se a academia pertence ao usuário
        $academy = Academy::list("id = '" . $academyId . "' AND user_id = '" . $userId . "'")->fetchObject();
        if (!$academy) {
            return Page::getPage('Erro', 'Academia não encontrada.');
        }

        if (empty($postVars['class_date'])) {
            return Page::getPage('Erro', 'A data da aula é obrigatória.');
        }

        $class             = new ClassModel();
        $class->academy_id  = $academyId;
        $class->schedule_id = $postVars['schedule_id'] ?: null;
        $class->modality_id = $postVars['modality_id'] ?: null;
        $class->class_date  = $postVars['class_date'];
        $class->start_time  = $postVars['start_time'] ?: null;
        $class->end_time    = $postVars['end_time'] ?: null;
        $class->notes       = $postVars['notes'] ?: null;
        $class->save();

        $request->getRouter()->redirect('/admin/aula?academy_id=' . $academyId);
    }

    /**
     * Método responsável por renderizar a tela de edição da aula
     *
     * @param Request $request
     * @return string
     */
    public static function getUpdate($request) {

        $queryParams = $request->getQueryParams();
        $id          = $queryParams['id'] ?? null;
        $userId      = LoginSession::getUserId();

        if (empty($id)) return Page::getPage('Erro', 'Aula não informada.');

        $class = ClassModel::listWithDetails("classes.id = '" . $id . "'")->fetchObject();

        if (!$class) return Page::getPage('Erro', 'Aula não encontrada.');

        $content = View::render(self::DEFAULT_PAGE_PATH . 'update', [
            'id'         => $class->id,
            'academies'  => self::renderAcademyOptions($userId, $class->academy_id),
            'schedules'  => self::renderScheduleOptions($class->academy_id, $class->schedule_id),
            'modalities' => self::renderModalityOptions($class->academy_id, $class->modality_id),
            'academy_id' => $class->academy_id,
            'schedule_id'=> $class->schedule_id ?? '',
            'modality_id'=> $class->modality_id ?? '',
            'class_date' => $class->class_date,
            'start_time' => $class->start_time ?? '',
            'end_time'   => $class->end_time   ?? '',
            'notes'      => $class->notes      ?? '',
        ]);

        return Page::getPage('Editar Aula', $content);
    }
    /**
     * Método responsável por editar a aula
     *
     * @param Request $request
     * @return void
     */
    public static function postUpdate($request) {

        $postVars = $request->getPostVars();
        $id       = trim($postVars['id'] ?? '');
        $userId   = LoginSession::getUserId();

        if (empty($id)) return Page::getPage('Erro', 'ID da aula não informado.');

        $class = ClassModel::list("id = '" . $id . "'")->fetchObject(ClassModel::class);
        if (!$class) return Page::getPage('Erro', 'Aula não encontrada.');

        $class->schedule_id = $postVars['schedule_id'] ?: null;
        $class->modality_id = $postVars['modality_id'] ?: null;
        $class->class_date  = $postVars['class_date'];
        $class->start_time  = $postVars['start_time'] ?: null;
        $class->end_time    = $postVars['end_time']   ?: null;
        $class->notes       = $postVars['notes']      ?: null;
        $class->save();

        $request->getRouter()->redirect('/admin/aula?academy_id=' . $class->academy_id);
    }

    /**
     * Método responsável por excluir uma aula
     *
     * @param Request $request
     * @return void
     */
    public static function postDelete($request) {

        $postVars  = $request->getPostVars();
        $id        = trim($postVars['id'] ?? '');
        $academyId = trim($postVars['academy_id'] ?? '');

        if (empty($id)) return Page::getPage('Erro', 'ID da aula não informado.');

        $class = ClassModel::list("id = '" . $id . "'")->fetchObject(ClassModel::class);
        if (!$class) return Page::getPage('Erro', 'Aula não encontrada.');

        $class->delete();

        $request->getRouter()->redirect('/admin/aula?academy_id=' . $academyId);
    }

    
    /**
     * Método responsável por renderizar as aulas.
     *
     * @param Request $request
     * @param Pagination $pagination
     * @param string $academyId
     * @return string
     */
    private static function renderClasses($request, &$pagination, $academyId) {

        if (empty($academyId)) return '<tr><td colspan="6" class="text-center text-muted py-4">Selecione uma academia.</td></tr>';

        $where = "classes.academy_id = '" . $academyId . "'";
        $total = ClassModel::list($where, null, null, null, 'COUNT(*) as qtd')->fetchObject()->qtd;

        $queryParams = $request->getQueryParams();
        $currentPage = $queryParams['page'] ?? 1;
        $pagination  = new Pagination($total, $currentPage, 15);

        $results = ClassModel::listWithDetails(
            $where,
            'classes.class_date DESC, classes.start_time DESC',
            $pagination->getLimit(),
            $pagination->getOffset()
        );

        $items  = '';
        $number = 1;

        while ($row = $results->fetchObject()) {
            $items .= View::render(self::DEFAULT_PAGE_PATH . 'item', [
                'id'            => $row->id,
                'number'        => $number++,
                'academy_id'    => $row->academy_id,
                'class_date'    => date('d/m/Y', strtotime($row->class_date)),
                'start_time'    => $row->start_time ? substr($row->start_time, 0, 5) : '—',
                'end_time'      => $row->end_time   ? substr($row->end_time, 0, 5)   : '—',
                'modality_name' => $row->modality_name ?? '—',
                'notes'         => $row->notes ?? '—',
            ]);
        }

        return $items ?: '<tr><td colspan="6" class="text-center text-muted py-4">Nenhuma aula cadastrada.</td></tr>';
    }

    /**
     * Método responsável por renderizar as academias para a seleção.
     *
     * @param string $userId
     * @param string $selected
     * @return string
     */
    private static function renderAcademyOptions($userId, $selected = null) {
        $options = '';
        $results = Academy::list("user_id = '" . $userId . "'", 'name ASC');
        while ($a = $results->fetchObject()) {
            $sel      = ($a->id == $selected) ? 'selected' : '';
            $options .= '<option value="' . $a->id . '" ' . $sel . '>' . htmlspecialchars($a->name) . '</option>';
        }
        return $options;
    }

    /**
     * Método responsável por renderizar os horários definidos para seleção
     *
     * @param string $academyId
     * @param string $selected
     * @return string
     */
    private static function renderScheduleOptions($academyId, $selected = null) {
        if (empty($academyId)) return '';
        $options = '<option value="">— Nenhum horário —</option>';
        $results = ClassSchedule::list("academy_id = '" . $academyId . "'", 'weekday ASC, start_time ASC');
        $days    = ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb'];
        while ($s = $results->fetchObject()) {
            $sel      = ($s->id == $selected) ? 'selected' : '';
            $label    = $days[$s->weekday] . ' ' . substr($s->start_time, 0, 5);
            $options .= '<option value="' . $s->id . '" ' . $sel
                      . ' data-start="' . $s->start_time . '"'
                      . ' data-end="' . ($s->end_time ?? '') . '"'
                      . ' data-modality="' . ($s->modality_id ?? '') . '">'
                      . htmlspecialchars($label)
                      . '</option>';
        }
        return $options;
    }

    /**
     * Método responsável por renderizar as modalidades para seleção.
     *
     * @param string $academyId
     * @param string $selected
     * @return string
     */
    private static function renderModalityOptions($academyId, $selected = null) {
        if (empty($academyId)) return '';
        $options = '<option value="">— Nenhuma modalidade —</option>';
        $results = ClassModality::list("academy_id = '" . $academyId . "'", 'name ASC');
        while ($m = $results->fetchObject()) {
            $sel      = ($m->id == $selected) ? 'selected' : '';
            $options .= '<option value="' . $m->id . '" ' . $sel . '>' . htmlspecialchars($m->name) . '</option>';
        }
        return $options;
    }
}