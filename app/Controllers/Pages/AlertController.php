<?php 

namespace App\Controllers\Pages;
use \App\Utils\View;

class AlertController{

    /**
     * Método responsável por retornar uma mensagem de sucesso
     *
     * @param string $message
     * @return string
     */
    public static function sucess($message){
        return View::render('shared/alert/status', [
            'type' => 'sucess',
            'message' => $message
        ]); 
    }

    /**
     * Método responsável por retornar uma mensagem de erro
     *
     * @param string $message
     * @return string
     */
    public static function error($message){
        return View::render('shared/alert/status', [
            'type' => 'danger',
            'message' => $message
        ]); 
    }
} 