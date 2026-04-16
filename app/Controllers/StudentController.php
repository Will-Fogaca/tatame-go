<?php

namespace App\Controllers;
use \App\Models\Student;
use \App\Utils\View;
use \App\Utils\Pagination;

class StudentController extends PageController{

  /**
   * Método responsável por obter a renderização dos itens dos alunos para a página
   * @param Request $request
   * @return string
   */
  private static function renderStudents($request){
      $items = '';
      $totalStudents = Student::list(null, null, null, null, 'COUNT(*) as qtd')->fetchObject()->qtd;
      $queryParams = $request->getQueryParams();
      $currentPage = $queryParams['page'] ?? 1;

      $pagination = new Pagination($totalStudents, $currentPage, 10);

      $results = Student::list(null, 'name ASC', $pagination->getLimit(), $pagination->getOffset());
      $number = 1;
      while($student = $results->fetchObject(Student::class)){
        $items .= View::render('students/item', [
          'number' => $number,
          'name'   => $student->name ?? '',
          'birth_date' => $student->birth_date ? date('d/m/Y', strtotime($student->birth_date)): '—',
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
  public static function index($request){
    $content = View::render('students/index', [
      'students' => self::renderStudents($request)
    ]);

    return parent::getPage('Alunos', $content);
  }

  /**
   * Método responsável por retornar o conteúdo (view) da página de cadastro de alunos
   * @return string
   */
  public static function getStore(){
    $content = View::render('students/store',[]);
    return parent::getPage('Cadastro de alunos', $content);
  }

  /**
   * Método responsável por cadastrar um aluno
   * @param Request $request
   * @return string
   */
  public static function store($request){

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

      return self::index($request);
  }
}