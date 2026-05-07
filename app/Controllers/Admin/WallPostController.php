<?php 

namespace App\Controllers\Admin;

use App\Models\Academy;
use App\Models\WallPost;
use App\Session\LoginSession;
use App\Utils\View;
use App\Utils\Page;
use App\Http\Router;
use App\Utils\BusinessException;

class WallPostController{

    private const DEFAULT_PAGE_PATH = 'admin/wall_post/';

    /**
     * Método responsável por renderizar a tela inicial do mural
     *
     * @param Request $request
     * @return string
     */
    public static function getIndex($request){

        $userId = LoginSession::getUserId();
        $queryParams = $request->getQueryParams();

        $academyId = $queryParams['academy_id'] ?? null;

        if(empty($academyId)){
            $first = Academy::list("user_id = '".$userId."'", 'name ASC', 1)->fetchObject(Academy::class);

            if($first){
                $academyId = $first->id;
            }
        }

        $posts = '';
        $empty = '';

        if($academyId){

            $results = WallPost::listByAcademy($academyId);

            while($post = $results->fetchObject()){
                $posts .= View::render(self::DEFAULT_PAGE_PATH.'item',[
                    'id'         => $post->id,
                    'academy_id' => $post->academy_id,
                    'title'      => $post->title,
                    'content'    => $post->content,
                    'author'     => $post->author,
                    'date'       => date('d/m/Y H:i', strtotime($post->created_at))
                ]);
            }

            $empty = ($posts == '') ? '' : 'd-none';
        }

        $content = View::render(self::DEFAULT_PAGE_PATH.'index', [
            'academies'  => self::renderAcademies($userId, $academyId),
            'posts'      => $posts,
            'academy_id' => $academyId,
            'empty'      => $empty
        ]);

        return Page::getPage('Mural', $content);
    }

    /**
     * Método responsável por renderizar a tela de criação de post
     *
     * @param Request $request
     * @return string
     */
    public static function getCreate($request){

        $academyId = $request->getQueryParams()['academy_id'] ?? null;

        if(empty($academyId)){
            return Page::getError('Erro', 'Academia não informada', '/admin/mural');
        }

        $content = View::render(self::DEFAULT_PAGE_PATH.'create', [
            'academy_id' => $academyId,
            'title'      => '',
            'content'    => '',
            'action'     => '/admin/mural/cadastrar'
        ]);

        return Page::getPage('Novo aviso', $content);
    }

    /**
     * Método responsável por criar um post
     *
     * @param Request $request
     * @return void
     */
    public static function postCreate($request){

        $postVars = $request->getPostVars();
        $userId   = LoginSession::getUserId();

        try{

            $post = new WallPost();

            $post->academy_id = $postVars['academy_id'] ?? null;
            $post->user_id    = $userId;
            $post->title      = $postVars['title'] ?? null;
            $post->content    = $postVars['content'] ?? null;

            $post->save();

            $request->getRouter()->redirect('/admin/mural?academy_id='.$post->academy_id);

        } catch(BusinessException $e){

            return Page::getError('Erro', $e->getMessage(), '/admin/mural');
        }
    }

    /**
     * Método responsável por renderizar a tela de edição de post
     *
     * @param Request $request
     * @return string
     */
    public static function getUpdate($request){

        $queryParams = $request->getQueryParams();
        $id          = $queryParams['id'] ?? null;
        $academyId   = $queryParams['academy_id'] ?? null;

        if(empty($id)){
            return Page::getError('Erro', 'Post não informado', '/admin/mural');
        }

        if(empty($academyId)){
            return Page::getError('Erro', 'Academia não informada', '/admin/mural');
        }

        $post = WallPost::list("id = '".$id."' AND academy_id = '".$academyId."'", null, 1)
            ->fetchObject(WallPost::class);

        if(!$post){
            return Page::getError('Erro', 'Post não encontrado', '/admin/mural?academy_id='.$academyId);
        }

        $content = View::render(self::DEFAULT_PAGE_PATH.'update', [
            'id'         => $id,
            'academy_id' => $academyId,
            'title'      => $post->title,
            'content'    => $post->content,
            'action'     => '/admin/mural/editar'
        ]);

        return Page::getPage('Editar aviso', $content);
    }

    /**
     * Método responsável por atualizar um post
     *
     * @param Request $request
     * @return void
     */
    public static function postUpdate($request){

        $postVars  = $request->getPostVars();
        $userId    = LoginSession::getUserId();

        $id        = $postVars['id'] ?? null;
        $academyId = $postVars['academy_id'] ?? null;

        try{

            if(empty($id)){
                throw new BusinessException('Post não informado.');
            }

            if(empty($academyId)){
                throw new BusinessException('Academia não informada.');
            }

            $post = WallPost::list("id = '".$id."' AND academy_id = '".$academyId."'", null, 1)
                ->fetchObject(WallPost::class);

            if(!$post){
                return Page::getError('Erro', 'Post não encontrado', '/admin/mural?academy_id='.$academyId);
            }

            $post->user_id  = $userId;
            $post->title    = $postVars['title'] ?? null;
            $post->content  = $postVars['content'] ?? null;

            $post->save();

            $request->getRouter()->redirect('/admin/mural?academy_id='.$post->academy_id);

        } catch(BusinessException $e){

            return Page::getError('Erro', $e->getMessage(), '/admin/mural?academy_id='.$academyId);
        }
    }

    /**
     * Método responsável por excluir um post do mural
     *
     * @param Request $request
     * @return void
     */
    public static function postDelete($request){

        $postVars  = $request->getPostVars();
        $id        = $postVars['id'] ?? null;
        $academyId = $postVars['academy_id'] ?? null;

        try{

            if(empty($id)){
                throw new BusinessException('Post não informado para exclusão.');
            }

            $post = new WallPost();
            $post->id = $id;

            $post->delete();

            $request->getRouter()->redirect('/admin/mural?academy_id='.$academyId);

        } catch(BusinessException $e){

            return Page::getError('Erro ao excluir', $e->getMessage(), '/admin/mural?academy_id='.$academyId);
        }
    }

    /**
     * Método responsável por renderizar academias
     *
     * @param string $userId
     * @param string $selected
     * @return string
     */
    private static function renderAcademies($userId, $selected = null){

        $results = Academy::list("user_id = '".$userId."'", 'name ASC');
        $items   = '';

        while($academy = $results->fetchObject(Academy::class)){
            $items .= View::render(self::DEFAULT_PAGE_PATH.'academies', [
                'id'       => $academy->id,
                'name'     => $academy->name,
                'selected' => ($academy->id == $selected ? 'selected' : '')
            ]);
        }

        return $items;
    }
}