<?php 
    require __DIR__.'/../vendor/autoload.php';
    use App\Utils\Environment;
    use \App\Utils\View;
    use \App\Utils\Database;

    Environment::load(__DIR__.'/../'); 
    define('URL', getenv('URL'));


    Database::config(getenv('DB_HOST'), getenv('DB_NAME'), getenv('DB_USER'), getenv('DB_PASS'), getenv('DB_PORT'));

    View::init([
        'URL'=> URL
    ]);
    
    date_default_timezone_set('America/Sao_Paulo');