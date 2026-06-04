<?php

    namespace App\Controllers\Admin;

    use App\Models\Academy;
    use App\Models\BeltRank;
    use App\Utils\View;
    use App\Utils\Page;
    use App\Session\LoginSession;

    class BeltRankController{

        private const DEFAULT_PAGE_PATH = 'admin/belt_rank/';

        public static function getIndex($request){

            $userId = LoginSession::getUser()['id'];

            $queryParams = $request->getQueryParams();
            $academyId = $queryParams['academy_id'] ?? null;

            if(!$academyId){
                $first = Academy::list("user_id = '".$userId."'", 'name ASC', '1')->fetchObject(Academy::class);
                if($first) $academyId = $first->id;
            }

            $content = View::render(self::DEFAULT_PAGE_PATH.'index', [
                'academies'  => self::renderAcademies($userId, $academyId),
                'belts'      => self::renderBelts($academyId),
                'academy_id' => $academyId
            ]);

            return Page::getPage('Graduações', $content);
        }

        public static function getCreate($request){

            $queryParams = $request->getQueryParams();
            $academyId = $queryParams['academy_id'] ?? null;

            if(!$academyId){
                return Page::getPage('Erro', 'Academia não informada');
            }

           $content = View::render(self::DEFAULT_PAGE_PATH.'create', [
                'error'      => '',
                'academy_id' => $academyId,
                'next_level' => BeltRank::getNextLevel($academyId)
            ]);

            return Page::getPage('Cadastro de graduações', $content);
        }

        public static function postCreate($request){

            $postVars    = $request->getPostVars();
            $academyId   = $postVars['academy_id']  ?? null;
            $description = $postVars['description'] ?? null;
            $level       = $postVars['level']        ?? null;

            if(!$academyId || !$description || !$level){
                return Page::getPage('Erro', 'Dados obrigatórios não informados');
            }

            $belt              = new BeltRank();
            $belt->academy_id  = $academyId;
            $belt->description = $description;
            $belt->level       = (int)$level;
            $belt->save();

            $request->getRouter()->redirect('/admin/graduacao?academy_id='.$academyId);
        }

        public static function getUpdate($request){

            $queryParams = $request->getQueryParams();
            $id          = $queryParams['id']         ?? null;
            $academyId   = $queryParams['academy_id'] ?? null;

            if(!$id) return Page::getPage('Erro', 'Graduação não informada');

            $belt = BeltRank::list("id = '".$id."'")->fetchObject(BeltRank::class);
            if(!$belt) return Page::getPage('Erro', 'Graduação não encontrada');

            $content = View::render(self::DEFAULT_PAGE_PATH.'update', [
                'id'          => $belt->id,
                'academy_id'  => $belt->academy_id,
                'description' => $belt->description,
                'level'       => $belt->level,
            ]);

            return Page::getPage('Editar graduação', $content);
        }

        public static function postUpdate($request){

            $postVars    = $request->getPostVars();
            $id          = trim($postVars['id']          ?? '');
            $description = trim($postVars['description'] ?? '');
            $level       = $postVars['level']             ?? null;

            if(!$id)          return Page::getPage('Erro', 'ID não informado');
            if(!$description) return Page::getPage('Erro', 'Descrição obrigatória');

            $belt = BeltRank::list("id = '".$id."'")->fetchObject(BeltRank::class);
            if(!$belt) return Page::getPage('Erro', 'Graduação não encontrada');

            $belt->description = $description;
            $belt->level       = (int)$level;
            $belt->save();

            $request->getRouter()->redirect('/admin/graduacao?academy_id='.$belt->academy_id);
        }

        public static function postDelete($request){

            $postVars  = $request->getPostVars();
            $id        = trim($postVars['id']         ?? '');
            $academyId = trim($postVars['academy_id'] ?? '');

            if(!$id) return Page::getPage('Erro', 'ID não informado');

            $belt = BeltRank::list("id = '".$id."'")->fetchObject(BeltRank::class);
            if(!$belt) return Page::getPage('Erro', 'Graduação não encontrada');

            $belt->delete();

            $request->getRouter()->redirect('/admin/graduacao?academy_id='.$academyId);
        }

        private static function renderAcademies($userId, $selected = null){
            $results = Academy::list("user_id = '".$userId."'", 'name ASC');
            $items   = '';
            while($academy = $results->fetchObject(Academy::class)){
                $items .= View::render(self::DEFAULT_PAGE_PATH.'option', [
                    'id'       => $academy->id,
                    'name'     => $academy->name,
                    'selected' => ($academy->id == $selected ? 'selected' : '')
                ]);
            }
            return $items;
        }

        private static function renderBelts($academyId){

            if(!$academyId){
                return View::render(self::DEFAULT_PAGE_PATH.'empty', [
                    'message' => 'Selecione uma academia'
                ]);
            }

            $results = BeltRank::list("academy_id = '".$academyId."'", 'level ASC');
            $items   = '';

            while($belt = $results->fetchObject(BeltRank::class)){
                $items .= View::render(self::DEFAULT_PAGE_PATH.'item', [
                    'id'         => $belt->id,
                    'academy_id' => $belt->academy_id,
                    'name'       => $belt->description,
                    'level'      => $belt->level,
                ]);
            }

            return $items ?: View::render(self::DEFAULT_PAGE_PATH.'empty', [
                'message' => 'Nenhuma graduação cadastrada'
            ]);
        }

        public static function getNextLevel($request)
        {
            $userId = LoginSession::getUser()['id'];

            $academy = Academy::list(
                "user_id = '".$userId."'",
                'name ASC',
                '1'
            )->fetchObject(Academy::class);

            if(!$academy){
                return json_encode([
                    'success' => false,
                    'message' => 'Academia não encontrada'
                ]);
            }

            $nextLevel = BeltRank::getNextLevel($academy->id);

            return json_encode([
                'success' => true,
                'level' => $nextLevel
            ]);
        }
    }