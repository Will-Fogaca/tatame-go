<?php 

namespace App\Controllers\Admin;

use \App\Models\Academy;
use \App\Utils\View;
use \App\Utils\Pagination;
use \App\Utils\Page;
use \App\Session\LoginSession;

class AcademyController{

    /**
     * Caminho das páginas relacionadas às academias
     * @var string
     */
    private const DEFAULT_PAGE_PATH = 'admin/academy/';

    

    /**
     * Método responsável por retornar a página inicial das academias
     *
     * @param Request $request
     * @return string
     */
    public static function getIndex($request){

        $pagination = null;

        $content = View::render(self::DEFAULT_PAGE_PATH.'index', [
            'academies' => self::renderAcademies($request, $pagination),
            'pagination' => Page::getPagination($request, $pagination)
        ]);
      
        return Page::getPage('Academias', $content);
    }

    /**
     * Método resposnável por renderizar a tela de cadastro de academia
     *
     * @param Request $request
     * @return void
     */
    public static function getCreate($request){
        $content = View::render(self::DEFAULT_PAGE_PATH.'create',[]);
        return Page::getPage('Cadastro de academias', $content);
    }


    public static function postCreate($request){
        $postVars = $request->getPostVars();
        
        $userId = LoginSession::getUserId();

        $academy = new Academy();
        $academy->user_id = $userId;
        $academy->name = $postVars['name'];
        $academy->phone_number = $postVars['phone_number'];

        $academy->save();

        return self::getIndex($request);    
    }

   /**
    * Método responsável por renderizar a tela de edição de academias
    *
    * @param Request $request
    * @return string
    */
    public static function getUpdate($request){

        $queryParams = $request->getQueryParams();
        $id = $queryParams['id'] ?? null;

        if(!$id){
            // pode redirecionar ou mostrar erro
            return Page::getPage('Erro', 'Academia não encontrada');
        }

        $academy = Academy::list("id = '".$id."'")->fetchObject(Academy::class);

        if(!$academy){
            return Page::getPage('Erro', 'Academia não encontrada');
        }

        $content = View::render(self::DEFAULT_PAGE_PATH.'update', [
            'id' => $academy->id,
            'name' => $academy->name,
            'phone_number' => $academy->phone_number
        ]);

        return Page::getPage('Editar academia', $content);
    }

    /**
     * Método responsável por renderizar o conteúdo da página inicial das academias
     *
     * @param Request $request
     * @param Pagination $pagination
     * @return string
     */
    private static function renderAcademies($request, &$pagination){    
        
        $user = LoginSession::getUser();
        $userId = $user['id'];

        $items = '';
        $where = "user_id = '".$userId."' and is_active = true";

        $totalAcademies = Academy::list($where, null, null, null, 'COUNT(*) as qtd')->fetchObject()->qtd;

        $queryParams = $request->getQueryParams();
        $currentPage = $queryParams['page'] ?? 1;

        $pagination = new Pagination($totalAcademies, $currentPage, 10);

        $results = Academy::list($where, 'name ASC', $pagination->getLimit(), $pagination->getOffset());

        $number = 1;

        while($academy = $results->fetchObject(Academy::class)){
           $items .= View::render(self::DEFAULT_PAGE_PATH.'item', [
            'id' => $academy->getId(), 
            'number' => $number,
            'name'   => $academy->getName() ?? '',
            'phone_number' => $academy->getPhoneNumber() ?? '—',
            'created_at'=> $academy->getCreatedAt() ? date('d/m/Y H:i', strtotime($academy->getCreatedAt())) : '—',
            'is_active' => $academy->isActive() ? 'Ativo' : 'Inativo'
        ]);

            $number++;
        }

        return $items;
    }

    /**
     * Método responsável por editar os dados da academia
     *
     * @param Request $request
     * @return void
     */
     public static function postUpdate($request){
    
        $postVars = $request->getPostVars();
       
        $id = trim($postVars['id'] ?? '');

        if(!$id){
            return Page::getPage('Erro', 'ID da academia não informado');
        }

        $academy = Academy::list("id = '".$id."'")->fetchObject(Academy::class);

        if(!$academy){
            return Page::getPage('Erro', 'Academia não encontrada');
        }

        $academy->name = $postVars['name'] ?? $academy->name;
        $academy->phone_number = $postVars['phone_number'] ?? $academy->phone_number;

        (new \App\Utils\Database('academies'))->update(
            "id = '".$academy->id."'",
            [
                'name' => $academy->name,
                'phone_number' => $academy->phone_number,
                'updated_at' => date('Y-m-d H:i:s')
            ]
        );

        return self::getIndex($request);
    }

    public static function postDelete($request){

        $postVars = $request->getPostVars();

        $id = trim($postVars['id'] ?? '');

        if(!$id){
            return Page::getPage('Erro', 'ID da academia não informado');
        }

        $userId = LoginSession::getUserId();

        $academy = Academy::list(
            "id = '".$id."' AND user_id = '".$userId."'"
        )->fetchObject(Academy::class);

        if(!$academy){
            return Page::getPage('Erro', 'Academia não encontrada');
        }

        // soft delete
        $academy->delete();

        return self::getIndex($request);
    }
    
}