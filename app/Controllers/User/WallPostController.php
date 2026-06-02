<?php 

namespace App\Controllers\User;

use App\Models\Academy;
use App\Models\WallPost;
use App\Session\LoginSession;
use App\Utils\View;
use App\Utils\Page;

class WallPostController{

    private const DEFAULT_PAGE_PATH = 'user/wall_post/';

    /**
     * Método responsável por renderizar a tela inicial do mural
     *
     * @param Request $request
     * @return string
     */
    public static function getIndex($request){

        $userId      = LoginSession::getUserId();
        $queryParams = $request->getQueryParams();
        $academyId   = $queryParams['academy_id'] ?? null;

        if(empty($academyId)){
            $first = Academy::listByUser($userId, 1)->fetchObject();
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
     * Método responsável por renderizar academias vinculadas ao usuário
     *
     * @param string $userId
     * @param string $selected
     * @return string
     */
    private static function renderAcademies($userId, $selected = null){

        $results = Academy::listByUser($userId);
        $items   = '';

        while($academy = $results->fetchObject()){
            $items .= View::render(self::DEFAULT_PAGE_PATH.'academies', [
                'id'       => $academy->id,
                'name'     => $academy->name,
                'selected' => ($academy->id == $selected ? 'selected' : '')
            ]);
        }

        return $items;
    }
}