<?php

namespace App\Controllers\Admin;

use \App\Models\StudentUser;
use \App\Models\User;
use \App\Models\Student;
use \App\Utils\View;
use \App\Utils\Pagination;
use \App\Utils\Page;

class StudentUserController {

    private const DEFAULT_PAGE_PATH = 'admin/student_user/';

    public static function getIndex($request) {

        $pagination = null;

        $content = View::render(self::DEFAULT_PAGE_PATH . 'index', [
            'links'      => self::renderLinks($request, $pagination),
            'pagination' => Page::getPagination($request, $pagination)
        ]);

        return Page::getPage('Vínculos Usuário-Aluno', $content);
    }

    public static function getCreate($request) {

        $content = View::render(self::DEFAULT_PAGE_PATH . 'create', [
            'error'       => '',
            'results'     => '',
            'document'    => '',
            'search_name' => '',
        ]);

        return Page::getPage('Novo Vínculo', $content);
    }

    /**
     * POST — busca usuário por documento e alunos por nome ao mesmo tempo
     */
    public static function postSearch($request) {

        $postVars   = $request->getPostVars();
        $document   = preg_replace('/\D/', '', $postVars['document'] ?? '');
        $searchName = trim($postVars['search_name'] ?? '');

        $error   = '';
        $results = '';

        if (empty($document) || empty($searchName)) {
            $error = 'Preencha o documento do usuário e o nome do aluno.';
        } else {

            $user = User::list(
                "document = '" . $document . "'",
                null, null, null,
                'id, name, email'
            )->fetchObject();

            if (!$user) {
                $error = 'Nenhum usuário encontrado com este documento.';
            } else {

                $studentsResult = Student::list(
                    "name LIKE '%" . $searchName . "%'",
                    'name ASC', 20, null,
                    'id, name, birth_date'
                );

                $options = '';
                while ($s = $studentsResult->fetchObject()) {
                    $age     = date_diff(date_create($s->birth_date), date_create('today'))->y;
                    $options .= View::render(self::DEFAULT_PAGE_PATH . 'student_option', [
                        'user_id'      => $user->id,
                        'student_id'   => $s->id,
                        'student_name' => $s->name,
                        'student_age'  => $age,
                    ]);
                }

                if (empty($options)) {
                    $error = 'Nenhum aluno encontrado com este nome.';
                } else {
                    $results = View::render(self::DEFAULT_PAGE_PATH . 'search_results', [
                        'user_name'  => $user->name,
                        'user_email' => $user->email,
                        'options'    => $options,
                    ]);
                }
            }
        }

        $content = View::render(self::DEFAULT_PAGE_PATH . 'create', [
            'error'       => $error ? '<div class="alert alert-danger">' . $error . '</div>' : '',
            'results'     => $results,
            'document'    => $postVars['document'] ?? '',
            'search_name' => $searchName,
        ]);

        return Page::getPage('Novo Vínculo', $content);
    }

    public static function postCreate($request) {

        $postVars  = $request->getPostVars();
        $userId    = trim($postVars['user_id'] ?? '');
        $studentId = trim($postVars['student_id'] ?? '');

        if (!$userId || !$studentId) {
            return Page::getPage('Erro', 'Usuário e aluno são obrigatórios.');
        }

        $existing = StudentUser::list(
            "user_id = '" . $userId . "' AND student_id = '" . $studentId . "'"
        )->fetchObject();

        if ($existing) {
            return Page::getPage('Erro', 'Este vínculo já existe.');
        }

        $link             = new StudentUser();
        $link->user_id    = $userId;
        $link->student_id = $studentId;
        $link->save();

        return self::getIndex($request);
    }

    public static function postDelete($request) {

        $postVars = $request->getPostVars();
        $id       = trim($postVars['id'] ?? '');

        if (!$id) {
            return Page::getPage('Erro', 'ID do vínculo não informado.');
        }

        $link = StudentUser::list("id = '" . $id . "'")->fetchObject(StudentUser::class);

        if (!$link) {
            return Page::getPage('Erro', 'Vínculo não encontrado.');
        }

        $link->delete();

        return self::getIndex($request);
    }

    private static function renderLinks($request, &$pagination) {

        $join = 'INNER JOIN users    u ON u.id = student_user.user_id'
              . ' INNER JOIN students s ON s.id = student_user.student_id';

        $fields = 'student_user.id, student_user.created_at,'
                . ' u.name AS user_name, u.email AS user_email,'
                . ' s.name AS student_name';

        $total = StudentUser::list(null, null, null, null, 'COUNT(*) as qtd')->fetchObject()->qtd;

        $queryParams = $request->getQueryParams();
        $currentPage = $queryParams['page'] ?? 1;

        $pagination = new Pagination($total, $currentPage, 10);

        $results = StudentUser::list(
            null,
            'student_user.created_at DESC',
            $pagination->getLimit(),
            $pagination->getOffset(),
            $fields,
            $join
        );

        $items  = '';
        $number = 1;

        while ($row = $results->fetchObject()) {
            $items .= View::render(self::DEFAULT_PAGE_PATH . 'item', [
                'id'           => $row->id,
                'number'       => $number++,
                'user_name'    => $row->user_name    ?? '—',
                'user_email'   => $row->user_email   ?? '—',
                'student_name' => $row->student_name ?? '—',
                'created_at'   => $row->created_at
                    ? date('d/m/Y H:i', strtotime($row->created_at))
                    : '—',
            ]);
        }

        return $items ?: View::render(self::DEFAULT_PAGE_PATH . 'empty', []);
    }
}