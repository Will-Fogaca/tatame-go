<?php

namespace App\Controllers\Pages;

use App\Http\Response;
use App\Utils\View;
use App\Utils\Page;

class ErrorController
{
    public static function get403()
    {
        return new Response(
            403,
            Page::getPage(
                '403 - Acesso Negado',
                View::render('shared/error/403')
            )
        );
    }
}