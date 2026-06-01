<?php

namespace App\Controllers\Admin;

use App\Utils\View;
use App\Utils\Page;
use App\Models\BeltRank;
use App\Models\StudentBeltRanks;

class StudentBeltController {

    const DEFAULT_PAGE_PATH = 'admin/student/belt/';

    /**
     * Método responsável por renderizar a tela inicial das graduações dos alunos
     *
     * @param Request $request
     * @return string
     */
    public static function getIndex($request){

        $queryParams = $request->getQueryParams();

        $studentId = $queryParams['student_id'] ?? null;
        $academyId = $queryParams['academy_id'] ?? null;

        if(!$studentId || !$academyId){
            return Page::getPage('Erro', 'Aluno ou academia não informados');
        }

        $content = View::render(self::DEFAULT_PAGE_PATH.'index', [
            'belts' => self::renderStudentBelts($studentId, $academyId),
            'student_id' => $studentId,
            'academy_id' => $academyId
        ]);

        return Page::getPage('Graduações dos alunos', $content);
    }


    /**
     * Método responsável por renderizar a tela de cadastro das graduações dos alunos
     *
     * @param Request $request
     * @return string
     */
    public static function getCreate($request){

        $queryParams = $request->getQueryParams();

        $studentId = $queryParams['student_id'] ?? null;
        $academyId = $queryParams['academy_id'] ?? null;

        if(!$studentId || !$academyId){
            return Page::getPage('Erro', 'Aluno ou academia não informados');
        }

        $content = View::render(self::DEFAULT_PAGE_PATH.'create', [
            'student_id' => $studentId,
            'academy_id' => $academyId,
            'belt_options' => self::renderBeltOptions($academyId),
            'today' => date('Y-m-d')
        ]);

        return Page::getPage('Cadastrar graduação do aluno', $content);
    }

    /**
     * Método responsável por cadastrar uma nova graduação para o aluno
     *
     * @param Request $request
     * @return void
     */
    public static function postCreate($request){

        $postVars = $request->getPostVars();

        $studentId   = $postVars['student_id'] ?? null;
        $academyId   = $postVars['academy_id'] ?? null;
        $beltRankId  = $postVars['belt_rank_id'] ?? null;
        $awardedAt   = $postVars['awarded_at'] ?? date('Y-m-d');
        $notes       = $postVars['notes'] ?? null;

        if(!$studentId || !$academyId || !$beltRankId){
            return Page::getPage('Erro', 'Dados obrigatórios não informados');
        }

        $studentBelt = new StudentBeltRanks();
        $studentBelt->student_id   = $studentId;
        $studentBelt->academy_id   = $academyId;
        $studentBelt->belt_rank_id = $beltRankId;
        $studentBelt->awarded_at   = $awardedAt;
        $studentBelt->notes        = $notes;
        $studentBelt->created_at   = date('Y-m-d H:i:s');

        $studentBelt->save();

        $request->getRouter()->redirect(
            '/admin/aluno/graduacao?student_id='.$studentId.'&academy_id='.$academyId
        );
    }

    /**
     * Método responsável por renderizar a tela de edição das graduações dos alunos
     *
     * @param Request $request
     * @return string
     */
    public static function getUpdate($request){

        $queryParams = $request->getQueryParams();

        $id = $queryParams['id'] ?? null;

        if(!$id){
            return Page::getPage('Erro', 'Graduação não informada');
        }

        $graduation = StudentBeltRanks::getById($id);

        if(!$graduation){
            return Page::getPage('Erro', 'Graduação não encontrada');
        }

        $content = View::render(self::DEFAULT_PAGE_PATH.'update', [
            'id'                    => $graduation->id,
            'student_id'            => $graduation->student_id,
            'academy_id'            => $graduation->academy_id,
            'awarded_at'            => date('Y-m-d', strtotime($graduation->awarded_at)),
            'notes'                 => $graduation->notes,
            'belt_options_selected' => self::renderBeltOptions(
                $graduation->academy_id,
                $graduation->belt_rank_id
            )
        ]);

        return Page::getPage('Editar graduação', $content);
    }

    /**
     * Método responsável por atualizar os dados de uma graduação dos alunos
     *
     * @param Request $request
     * @return void
     */
    public static function postUpdate($request){

        $postVars = $request->getPostVars();

        $id         = $postVars['id'] ?? null;
        $studentId  = $postVars['student_id'] ?? null;
        $academyId  = $postVars['academy_id'] ?? null;
        $beltRankId = $postVars['belt_rank_id'] ?? null;
        $awardedAt  = $postVars['awarded_at'] ?? null;
        $notes      = $postVars['notes'] ?? null;

        if(!$id || !$beltRankId){
            return Page::getPage('Erro', 'Dados inválidos');
        }

        $graduation = StudentBeltRanks::getById($id);

        if(!$graduation){
            return Page::getPage('Erro', 'Graduação não encontrada');
        }

        $graduation->belt_rank_id = $beltRankId;
        $graduation->awarded_at   = $awardedAt;
        $graduation->notes        = $notes;

        $graduation->save();

        $request->getRouter()->redirect(
            '/admin/aluno/graduacao?student_id='.$studentId.'&academy_id='.$academyId
        );
    }

    /**
     * Método responsável por excluir uma graduação do aluno
     *
     * @param Request $request
     * @return void
     */
    public static function postDelete($request){

        $postVars = $request->getPostVars();

        $id        = $postVars['id'] ?? null;
        $studentId = $postVars['student_id'] ?? null;
        $academyId = $postVars['academy_id'] ?? null;

        if(!$id){
            return Page::getPage('Erro', 'ID não informado');
        }

        $graduation = new StudentBeltRanks();
        $graduation->id = $id;
        $graduation->delete();

        $request->getRouter()->redirect(
            '/admin/aluno/graduacao?student_id='.$studentId.'&academy_id='.$academyId
        );
    }

    /**
     * Método responsável por listar as graduações dos alunos
     *
     * @param string $studentId
     * @param string $academyId
     * @return string
     */
    private static function renderStudentBelts($studentId, $academyId){

        $items = '';
        $number = 1;

        $results = StudentBeltRanks::list(
            "student_id = '".$studentId."' AND academy_id = '".$academyId."'",
            'awarded_at DESC'
        );

        while($row = $results->fetchObject(StudentBeltRanks::class)){

            $belt = BeltRank::list(
                "id = '".$row->belt_rank_id."'"
            )->fetchObject(BeltRank::class);

            $items .= View::render(self::DEFAULT_PAGE_PATH.'item', [
                'id'         => $row->id,
                'student_id' => $studentId,
                'academy_id' => $academyId,
                'number'     => $number,
                'belt'       => $belt->description ?? '—',
                'date'       => $row->awarded_at ? date('d/m/Y', strtotime($row->awarded_at)) : '—',
                'notes'      => $row->notes ?? '—'
            ]);

            $number++;
        }

        if($items == ''){
            return '<tr><td colspan="5">Nenhuma graduação encontrada</td></tr>';
        }

        return $items;
    }


    /**
     * Método responsável por renderizar os itens das faixas
     *
     * @param string $academyId
     * @param string $selectedId
     * @return string
     */
    private static function renderBeltOptions($academyId, $selectedId = null){

        if(!$academyId){
            return '<option value="">Selecione a academia</option>';
        }

        $results = BeltRank::list(
            "academy_id = '".$academyId."'",
            'level ASC'
        );

        $items = '';

        while($belt = $results->fetchObject(BeltRank::class)){

            $selected = ($belt->id == $selectedId) ? 'selected' : '';

            $items .= '<option value="'.$belt->id.'" '.$selected.'>'
                        .$belt->description.
                      '</option>';
        }

        return $items;
    }
}