<?php 

namespace App\Controllers\Admin;

use App\Models\Academy;
use App\Models\BeltRank;
use App\Utils\View;
use App\Utils\Page;
use App\Session\LoginSession;

class BeltRankController{

    private const DEFAULT_PAGE_PATH = 'admin/belt_rank/';

    /**
     * Página inicial das graduações
     */
    public static function getIndex($request){

        $userId = LoginSession::getUser()['id'];

        // pega academy selecionada via GET
        $queryParams = $request->getQueryParams();
        $academyId = $queryParams['academy_id'] ?? null;
      
        
        // se não tiver nenhuma, pega a primeira automaticamente
        if(!$academyId){
            $first = Academy::list("user_id = '".$userId."'", 'name ASC', '1')->fetchObject(Academy::class);

            if($first){
                $academyId = $first->id;
            }
        }

        $content = View::render(self::DEFAULT_PAGE_PATH.'index', [
            'academies' => self::renderAcademies($userId, $academyId),
            'belts' => self::renderBelts($academyId)
        ]);

       
      
        return Page::getPage('Graduações', $content);
    }

    /**
     * Método responsável por renderizar a tela de cadastro de graduações
     *
     * @param Request $request
     * @return void
     */
    public static function getCreate($request){
        $content = View::render(self::DEFAULT_PAGE_PATH.'create',[]);
        return Page::getPage('Cadastro de graduações', $content);
    }

    
    /**
     * Método responsável por renderizar a seleção das academias
     *
     * @param string $userId
     * @param string $selected
     * @return string
     */
    private static function renderAcademies($userId, $selected = null){
        $results = Academy::list("user_id = '".$userId."'", 'name ASC');
        $items = '';

        while($academy = $results->fetchObject(Academy::class)){
       
            $items .= View::render(self::DEFAULT_PAGE_PATH.'/option', [
                'id'       => $academy->id,
                'name'     => $academy->name,
                'selected' => ($academy->id == $selected ? 'selected' : '')
            ]);
        }

        return $items;
    }

    
    private static function renderBelts($academyId){

        
        if(!$academyId){
            return View::render(self::DEFAULT_PAGE_PATH.'/empty', [
                'message' => 'Selecione uma academia'
            ]);
        }

        $results = BeltRank::list("academy_id = '".$academyId."'", 'level ASC');
       
        
        $items = '';

        while($belt = $results->fetchObject(BeltRank::class)){
            $items .= View::render(self::DEFAULT_PAGE_PATH.'/item', [
                'name'  => $belt->description,
                'level' => $belt->level
            ]);
        }

        return $items;
    }
}