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
         * Método que renderiza a tela inicial das graduações
         *
         * @param Request $request
         * @return string
         */
        public static function getIndex($request){

            $userId = LoginSession::getUser()['id'];

            $queryParams = $request->getQueryParams();
            $academyId = $queryParams['academy_id'] ?? null;

            if(!$academyId){
                $first = Academy::list("user_id = '".$userId."'", 'name ASC', '1')->fetchObject(Academy::class);

                if($first){
                    $academyId = $first->id;
                }
            }

            $content = View::render(self::DEFAULT_PAGE_PATH.'index', [
                'academies'  => self::renderAcademies($userId, $academyId),
                'belts'      => self::renderBelts($academyId),
                'academy_id' => $academyId 
            ]);

            return Page::getPage('Graduações', $content);
        }
        
        /**
         * Método responsável por cadastrar uma graduação
         *
         * @param Request $request
         * @return void
         */
        public static function postCreate($request){

            $postVars = $request->getPostVars();

            $academyId = $postVars['academy_id'] ?? null;
            $description = $postVars['description'] ?? null;
            $level = $postVars['level'] ?? null;

            if(!$academyId || !$description || !$level){
                return Page::getPage('Erro', 'Dados obrigatórios não informados');
            }

            $belt = new \App\Models\BeltRank();
            $belt->academy_id = $academyId;
            $belt->description = $description;
            $belt->level = (int)$level;

            $belt->save();

            $request->getRouter()->redirect('/admin/graduacao?academy_id='.$academyId);
        }

        /**
         * Método responsável por renderizar a tela de cadastro de graduações
         *
         * @param Request $request
         * @return void
         */
        public static function getCreate($request){

            $queryParams = $request->getQueryParams();
            $academyId = $queryParams['academy_id'] ?? null;

            if(!$academyId){
                return Page::getPage('Erro', 'Academia não informada');
            }

            $content = View::render(self::DEFAULT_PAGE_PATH.'create', [
                'error' => '',
                'academy_id' => $academyId
            ]);

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

        
        /**
         * Método responsável por renderizar as faixas para seleções
         *
         * @param string $academyId
         * @return string
         */
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