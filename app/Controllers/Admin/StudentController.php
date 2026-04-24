<?php

namespace App\Controllers\Admin;
use \App\Models\Student;
use \App\Models\Academy;
use \App\Utils\View;
use \App\Utils\Pagination;
use \App\Utils\Page;

class StudentController{

  /**
   * Caminho das páginas relacionadas aos alunos
   * @var string
   */
  private const DEFAULT_PAGE_PATH = 'admin/student/';


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

        $where = "id IN (
            SELECT student_id 
            FROM academy_students 
            WHERE academy_id = '".$academyId."'
        )";

        // TOTAL
        $totalStudents = Student::list($where, null, null, null, 'COUNT(*) as qtd')
            ->fetchObject()->qtd;

        $pagination = new Pagination($totalStudents, $currentPage, 10);

        // LISTA
        $results = Student::list(
            $where,
            'name ASC',
            $pagination->getLimit(),
            $pagination->getOffset()
        );

        $number = 1;

        while($student = $results->fetchObject(Student::class)){
            $items .= View::render(self::DEFAULT_PAGE_PATH.'/item', [
                'number' => $number,
                'name'   => $student->name ?? '',
                'birth_date' => $student->birth_date ? date('d/m/Y', strtotime($student->birth_date)) : '—',
                'phone' => $student->phone_number ?? '—',
                'guardian_name' => $student->guardian_name ?? '—',
                'guardian_phone' => $student->guardian_phone ?? '—',
                'created_at'=> $student->created_at ? date('d/m/Y H:i', strtotime($student->created_at)) : '—',
            ]);

            $number++;
        }

        return $items;
    }

  /**
   * Método responsável por retornar o conteúdo (view) da página de alunos
   * @return string
   */
  public static function getIndex($request){

      $userId = \App\Session\LoginSession::getUser()['id'];

      $queryParams = $request->getQueryParams();
      $academyId = $queryParams['academy_id'] ?? null;

      // pega primeira academia automaticamente
      if(!$academyId){
          $first = Academy::list(
              "user_id = '".$userId."'",
              'name ASC',
              '1'
          )->fetchObject(\App\Models\Academy::class);

          if($first){
              $academyId = $first->id;
          }
      }

      // 🔥 ESSENCIAL: cria antes de passar por referência
      $pagination = new Pagination(0, 1, 10);

      $students = self::renderStudents($request, $pagination, $academyId);

      $content = View::render(self::DEFAULT_PAGE_PATH.'index', [
          'academies' => self::renderAcademies($userId, $academyId),
          'students'  => $students,
          'pagination'=> Page::getPagination($request, $pagination)
      ]);

      return Page::getPage('Alunos', $content);
  }

  /**
   * Método responsável por retornar o conteúdo (view) da página de cadastro de alunos
   * @return string
   */
  public static function getStore(){
    $content = View::render(self::DEFAULT_PAGE_PATH.'store',[]);
    return Page::getPage('Cadastro de alunos', $content);
  }

  /**
   * Método responsável por cadastrar um aluno
   * @param Request $request
   * @return string
   */
  public static function postStore($request){

      $postVars = $request->getPostVars();

      $student = new Student();
      $student->name = trim($postVars['name'] ?? '');
      $student->birth_date = !empty($postVars['birth_date']) ? $postVars['birth_date']: null;
      $student->phone_number = !empty($postVars['phone_number']) ? $postVars['phone_number']: null;
      $student->guardian_name = !empty($postVars['guardian_name']) ? $postVars['guardian_name']: null;
      $student->guardian_phone = !empty($postVars['guardian_phone']) ? $postVars['guardian_phone']: null;
      $student->graduation_id  = !empty($postVars['graduation_id']) ? $postVars['graduation_id']: null;
      $student->is_active  = true;
      $student->created_at = date('Y-m-d H:i:s');
      $student->updated_at = date('Y-m-d H:i:s');

      $student->save();

      return self::getIndex($request);
  }

  /**
   * Renderiza academias no select
   */
  private static function renderAcademies($userId, $selected = null){

    $results = Academy::list("user_id = '".$userId."'", 'name ASC');
    $items = '';

    while($academy = $results->fetchObject(\App\Models\Academy::class)){
        $items .= View::render(self::DEFAULT_PAGE_PATH.'/option', [
            'id'       => $academy->id,
            'name'     => $academy->name,
            'selected' => ($academy->id == $selected ? 'selected' : '')
        ]);
    }

    return $items;
  }
}