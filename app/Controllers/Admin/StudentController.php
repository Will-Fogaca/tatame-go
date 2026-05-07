<?php

namespace App\Controllers\Admin;
use \App\Models\Student;
use \App\Models\BeltRank;
use \App\Models\Academy;
use App\Utils\BusinessException;
use \App\Utils\View;
use \App\Utils\Pagination;
use \App\Utils\Page;
use \App\Session\LoginSession;

class StudentController{

    /**
     * Caminho das páginas relacionadas aos alunos
     * @var string
     */
    private const DEFAULT_PAGE_PATH = 'admin/student/';

    /**
     * Caminho dos componentes compartilhados
     * @var string
     */
    private const SHARED_PATH = 'shared/';


    /**
     * Método responsável por renderizar o alerta de erro inline
     * @param string|null $error
     * @return string
     */
    private static function renderError(?string $error): string {
        if(!$error) return '';
        return View::render(self::SHARED_PATH.'alert/error', ['error' => $error]);
    }


    /**
     * Método responsável por obter a renderização dos itens dos alunos para a página
     * @param Request $request
     * @param Pagination $pagination
     * @param string $academyId
     * @return string
     */
    private static function renderStudents($request, &$pagination, $academyId){

        $items = '';

        $queryParams = $request->getQueryParams();
        $currentPage = $queryParams['page'] ?? 1;

        if(!$pagination instanceof Pagination){
            $pagination = new Pagination(0, $currentPage, 10);
        }

        if(!$academyId){
            return '<tr><td colspan="7">Selecione uma academia</td></tr>';
        }

        $where = "id IN (SELECT student_id FROM academy_students WHERE academy_id = '".$academyId."')";
        $totalStudents = Student::list($where, null, null, null, 'COUNT(*) as qtd')->fetchObject()->qtd;

        $pagination = new Pagination($totalStudents, $currentPage, 10);

        $results = Student::list($where, 'name ASC', $pagination->getLimit(), $pagination->getOffset());

        $number = 1;

        while($student = $results->fetchObject(Student::class)){
            $items .= View::render(self::DEFAULT_PAGE_PATH.'/item', [
                'id'             => $student->id,
                'academy_id'     => $academyId,
                'number'         => $number,
                'name'           => $student->name ?? '',
                'birth_date'     => $student->birth_date ? date('d/m/Y', strtotime($student->birth_date)) : '—',
                'phone'          => $student->phone_number ?? '—',
                'guardian_name'  => $student->guardian_name ?? '—',
                'guardian_phone' => $student->guardian_phone ?? '—',
                'created_at'     => $student->created_at ? date('d/m/Y H:i', strtotime($student->created_at)) : '—',
            ]);

            $number++;
        }

        return $items;
    }

    /**
     * Método responsável por retornar o conteúdo (view) da página de alunos
     * @param Request $request
     * @return string
     */
    public static function getIndex($request){

        $userId = LoginSession::getUser()['id'];

        $queryParams = $request->getQueryParams();
        $academyId = $queryParams['academy_id'] ?? null;

        if(!$academyId){
            $first = Academy::list("user_id = '".$userId."'", 'name ASC', '1')->fetchObject(\App\Models\Academy::class);

            if($first){
                $academyId = $first->id;
            }
        }

        $pagination = new Pagination(0, 1, 10);

        $students = self::renderStudents($request, $pagination, $academyId);

        $content = View::render(self::DEFAULT_PAGE_PATH.'index', [
            'academies'  => self::renderAcademies($userId, $academyId),
            'students'   => $students,
            'pagination' => Page::getPagination($request, $pagination),
            'academy_id' => $academyId 
        ]);

        return Page::getPage('Alunos', $content);
    }

    /**
     * Método responsável por retornar o conteúdo (view) da página de cadastro de alunos
     * @param Request $request
     * @return string
     */
    public static function getCreate($request){

        $queryParams = $request->getQueryParams();
        $academyId = $queryParams['academy_id'] ?? null;

        if(!$academyId){
            return Page::getPage('Erro', 'Academia não informada');
        }

        $content = View::render(self::DEFAULT_PAGE_PATH.'create', [
            'academy_id'     => $academyId,
            'error'          => '',
            'name'           => '',
            'birth_date'     => '',
            'phone_number'   => '',
            'guardian_name'  => '',
            'guardian_phone' => '',
            'notes'          => '',
        ]);

        return Page::getPage('Cadastro de alunos', $content);
    }

    /**
     * Método responsável por cadastrar um aluno
     * @param Request $request
     * @return string
     */
    public static function postCreate($request){

        $postVars  = $request->getPostVars();
        $academyId = $postVars['academy_id'] ?? null;
        $error     = null;

        if(empty($academyId)){
            return Page::getError('Erro ao cadastrar aluno', 'Academia não informada', '/admin/aluno');
        }

        try {

            $student = new Student();
            $student->name           = trim($postVars['name'] ?? '');
            $student->birth_date     = $postVars['birth_date'] ?? null;
            $student->phone_number   = $postVars['phone_number'] ?? null;
            $student->guardian_name  = $postVars['guardian_name'] ?? null;
            $student->guardian_phone = $postVars['guardian_phone'] ?? null;
            $student->notes          = $postVars['notes'] ?? null;
            $student->is_active      = true;

            $student->save();

            $academyStudent = new \App\Models\AcademyStudents();
            $academyStudent->academy_id = $academyId;
            $academyStudent->student_id = $student->id;
            $academyStudent->save();

            $request->getRouter()->redirect('/admin/aluno?academy_id='.$academyId);

        } catch (BusinessException $e) {
            $error = $e->getMessage();
        }

        $content = View::render(self::DEFAULT_PAGE_PATH.'create', [
            'academy_id'     => $academyId,
            'error'          => self::renderError($error),
            'name'           => $postVars['name'] ?? '',
            'birth_date'     => $postVars['birth_date'] ?? '',
            'phone_number'   => $postVars['phone_number'] ?? '',
            'guardian_name'  => $postVars['guardian_name'] ?? '',
            'guardian_phone' => $postVars['guardian_phone'] ?? '',
            'notes'          => $postVars['notes'] ?? '',
        ]);

        return Page::getPage('Cadastro de alunos', $content);
    }

    /**
     * Método responsável por retornar o conteúdo (view) da página de edição de alunos
     * @param Request $request
     * @return string
     */
    public static function getUpdate($request){

        $queryParams = $request->getQueryParams();
        $id        = $queryParams['id'] ?? null;
        $academyId = $queryParams['academy_id'] ?? null;

        if(!$id){
            return Page::getPage('Erro', 'Aluno não encontrado');
        }

        $student = Student::list("id = '".$id."'")->fetchObject(Student::class);

        if(!$student){
            return Page::getPage('Erro', 'Aluno não encontrado');
        }

        $content = View::render(self::DEFAULT_PAGE_PATH.'update', [
            'id'             => $student->id,
            'academy_id'     => $academyId,
            'error'          => '',
            'name'           => $student->name,
            'birth_date'     => $student->birth_date,
            'phone_number'   => $student->phone_number,
            'guardian_name'  => $student->guardian_name,
            'guardian_phone' => $student->guardian_phone,
            'notes'          => $student->notes,
        ]);

        return Page::getPage('Editar aluno', $content);
    }

    /**
     * Método responsável por atualizar um aluno
     * @param Request $request
     * @return string
     */
    public static function postUpdate($request){

        $postVars  = $request->getPostVars();
        $id        = $postVars['id'] ?? null;
        $academyId = $postVars['academy_id'] ?? null;
        $error     = null;

        if(!$id){
            return Page::getError('Erro ao editar aluno', 'Aluno não informado', '/admin/aluno');
        }

        if(!$academyId){
            return Page::getError('Erro ao editar aluno', 'Academia não informada', '/admin/aluno');
        }

        $student = Student::list("id = '".$id."'")->fetchObject(Student::class);

        if(!$student){
            return Page::getError('Erro ao editar aluno', 'Aluno não encontrado', '/admin/aluno');
        }

        try {

            $student->name           = trim($postVars['name'] ?? '');
            $student->birth_date     = $postVars['birth_date'] ?? null;
            $student->phone_number   = $postVars['phone_number'] ?? null;
            $student->guardian_name  = $postVars['guardian_name'] ?? null;
            $student->guardian_phone = $postVars['guardian_phone'] ?? null;
            $student->notes          = $postVars['notes'] ?? null;
            $student->updated_at     = date('Y-m-d H:i:s');

            $student->save();

            $request->getRouter()->redirect('/admin/aluno?academy_id='.$academyId);

        } catch (BusinessException $e) {
            $error = $e->getMessage();
        }

        $content = View::render(self::DEFAULT_PAGE_PATH.'update', [
            'id'             => $id,
            'academy_id'     => $academyId,
            'error'          => self::renderError($error),
            'name'           => $postVars['name'] ?? '',
            'birth_date'     => $postVars['birth_date'] ?? '',
            'phone_number'   => $postVars['phone_number'] ?? '',
            'guardian_name'  => $postVars['guardian_name'] ?? '',
            'guardian_phone' => $postVars['guardian_phone'] ?? '',
            'notes'          => $postVars['notes'] ?? '',
        ]);

        return Page::getPage('Editar aluno', $content);
    }

    /**
     * Método responsável por excluir um aluno
     * @param Request $request
     * @return string
     */
    public static function postDelete($request){

        $postVars  = $request->getPostVars();
        $id        = trim($postVars['id'] ?? '');
        $academyId = $postVars['academy_id'] ?? null;

        try {

            $student = Student::list("id = '".$id."'")->fetchObject(Student::class);

            if(!$student){
                return Page::getPage('Erro', 'Aluno não encontrado');
            }

            $student->delete();

            $request->getRouter()->redirect('/admin/aluno?academy_id='.$academyId);

        } catch (BusinessException $e) {
            return Page::getError('Erro ao excluir aluno', $e->getMessage(), '/admin/aluno?academy_id='.$academyId);
        }
    }

    /**
     * Método responsável por renderizar as academias
     * @param string $userId
     * @param string $selected
     * @return string
     */
    private static function renderAcademies($userId, $selected = null){

        $results = Academy::list("user_id = '".$userId."'", 'name ASC');
        $items   = '';

        while($academy = $results->fetchObject(\App\Models\Academy::class)){
            $items .= View::render(self::DEFAULT_PAGE_PATH.'/academies', [
                'id'       => $academy->id,
                'name'     => $academy->name,
                'selected' => ($academy->id == $selected ? 'selected' : '')
            ]);
        }

        return $items;
    }

    /**
     * Método responsável por renderizar as faixas
     * @param string $academyId
     * @param string $selected
     * @return string
     */
    private static function renderBelts($academyId, $selected = null){

        if(!$academyId){
            return '<option value="">Selecione a academia</option>';
        }

        $results = BeltRank::list("academy_id = '".$academyId."'", 'level ASC');
        $items   = '';

        while($belt = $results->fetchObject(\App\Models\BeltRank::class)){
            $items .= View::render(self::DEFAULT_PAGE_PATH.'/belts', [
                'id'          => $belt->id,
                'description' => $belt->description,
                'selected'    => ($belt->id == $selected ? 'selected' : '')
            ]);
        }

        return $items;
    }
}