<?php

namespace App\Controllers\Admin;

use App\Utils\View;
use App\Models\ClassModel;

class ClassController
{
    public static function index($request)
    {
        $classes = ClassModel::getAll();

        return View::render('admin/classes/index', [
            'classes' => $classes
        ]);
    }

    public static function create($request)
    {
        return View::render('admin/classes/create');
    }

    public static function store($request)
    {
        $data = $request->getPostVars();

        ClassModel::create($data);

        return header('Location: /admin/aulas');
    }
}