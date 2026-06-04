<?php

namespace App\Controllers\Admin;

use \App\Models\ClassModality;
use \App\Models\Academy;
use \App\Utils\View;
use \App\Utils\Pagination;
use \App\Utils\Page;
use \App\Session\LoginSession;

class ClassModalityController {

    private const DEFAULT_PAGE_PATH = 'admin/class_modality/';

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
            'modalities' => self::renderModalities($request, $pagination, $academyId),
            'pagination' => Page::getPagination($request, $pagination),
            'academy_id' => $academyId ?? '',
        ]);

        return Page::getPage('Modalidades', $content);
    }

    public static function getCreate($request) {

        $userId    = LoginSession::getUserId();
        $academyId = $request->getQueryParams()['academy_id'] ?? null;

        $content = View::render(self::DEFAULT_PAGE_PATH . 'create', [
            'academies'  => self::renderAcademyOptions($userId, $academyId),
            'academy_id' => $academyId ?? '',
            'name'       => '',
        ]);

        return Page::getPage('Nova Modalidade', $content);
    }

    public static function postCreate($request) {

        $postVars  = $request->getPostVars();
        $userId    = LoginSession::getUserId();
        $academyId = trim($postVars['academy_id'] ?? '');
        $name      = trim($postVars['name'] ?? '');

        if (empty($academyId)) return Page::getPage('Erro', 'Academia não informada.');
        if (empty($name))      return Page::getPage('Erro', 'Nome da modalidade é obrigatório.');

        $academy = Academy::list("id = '" . $academyId . "' AND user_id = '" . $userId . "'")->fetchObject();
        if (!$academy) return Page::getPage('Erro', 'Academia não encontrada.');

        $modality             = new ClassModality();
        $modality->academy_id = $academyId;
        $modality->name       = $name;
        $modality->save();

        $request->getRouter()->redirect('/admin/aula/modalidade?academy_id=' . $academyId);
    }

    public static function getUpdate($request) {

        $queryParams = $request->getQueryParams();
        $id          = $queryParams['id'] ?? null;
        $userId      = LoginSession::getUserId();

        if (empty($id)) return Page::getPage('Erro', 'Modalidade não informada.');

        $modality = ClassModality::list("id = '" . $id . "'")->fetchObject(ClassModality::class);
        if (!$modality) return Page::getPage('Erro', 'Modalidade não encontrada.');

        $content = View::render(self::DEFAULT_PAGE_PATH . 'update', [
            'id'         => $modality->id,
            'academies'  => self::renderAcademyOptions($userId, $modality->academy_id),
            'academy_id' => $modality->academy_id,
            'name'       => $modality->name,
        ]);

        return Page::getPage('Editar Modalidade', $content);
    }

    public static function postUpdate($request) {

        $postVars = $request->getPostVars();
        $id       = trim($postVars['id'] ?? '');
        $name     = trim($postVars['name'] ?? '');

        if (empty($id))   return Page::getPage('Erro', 'ID não informado.');
        if (empty($name)) return Page::getPage('Erro', 'Nome da modalidade é obrigatório.');

        $modality = ClassModality::list("id = '" . $id . "'")->fetchObject(ClassModality::class);
        if (!$modality) return Page::getPage('Erro', 'Modalidade não encontrada.');

        $modality->name = $name;
        $modality->save();

        $request->getRouter()->redirect('/admin/aula/modalidade?academy_id=' . $modality->academy_id);
    }

    public static function postDelete($request) {

        $postVars  = $request->getPostVars();
        $id        = trim($postVars['id'] ?? '');
        $academyId = trim($postVars['academy_id'] ?? '');

        if (empty($id)) return Page::getPage('Erro', 'ID não informado.');

        $modality = ClassModality::list("id = '" . $id . "'")->fetchObject(ClassModality::class);
        if (!$modality) return Page::getPage('Erro', 'Modalidade não encontrada.');

        $modality->delete();

        $request->getRouter()->redirect('/admin/aula/modalidade?academy_id=' . $academyId);
    }

    // -------------------------------------------------------------------------
    // Privados
    // -------------------------------------------------------------------------

    private static function renderModalities($request, &$pagination, $academyId) {

        if (empty($academyId)) return '<tr><td colspan="3" class="text-center text-muted py-4">Selecione uma academia.</td></tr>';

        $where = "academy_id = '" . $academyId . "'";
        $total = ClassModality::list($where, null, null, null, 'COUNT(*) as qtd')->fetchObject()->qtd;

        $queryParams = $request->getQueryParams();
        $currentPage = $queryParams['page'] ?? 1;
        $pagination  = new Pagination($total, $currentPage, 15);

        $results = ClassModality::list($where, 'name ASC', $pagination->getLimit(), $pagination->getOffset());

        $items  = '';
        $number = 1;

        while ($row = $results->fetchObject()) {
            $items .= View::render(self::DEFAULT_PAGE_PATH . 'item', [
                'id'         => $row->id,
                'number'     => $number++,
                'academy_id' => $academyId,
                'name'       => $row->name,
            ]);
        }

        return $items ?: '<tr><td colspan="3" class="text-center text-muted py-4">Nenhuma modalidade cadastrada.</td></tr>';
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
}