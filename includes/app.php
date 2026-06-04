<?php 
    require __DIR__.'/../vendor/autoload.php';

    use App\Http\Middleware\Middleware;
    use App\Utils\Environment;
    use \App\Utils\View;
    use \App\Utils\Database;
    use \App\Http\Middleware\Queue;

    Environment::load(__DIR__.'/../'); 

    define('URL', getenv('URL'));
    

   Database::config(
    $_ENV['DB_HOST'] ?? '',
    $_ENV['DB_NAME'] ?? '',
    $_ENV['DB_USER'] ?? '',
    $_ENV['DB_PASS'] ?? '',
    $_ENV['DB_PORT'] ?? '3306'
);

    View::init([
        'URL'=> URL
    ]);
    
    Queue::setMap([
        'maintenance' => \App\Http\Middleware\Maintenance::class,
        'isAdmin' => \App\Http\Middleware\IsAdmin::class,
        'required-logout' => \App\Http\Middleware\RequireLogout::class,
        'required-login' => \App\Http\Middleware\RequireLogin::class

    ]);

    Queue::setDefault([
        'maintenance'
    ]);

    date_default_timezone_set('America/Sao_Paulo');

    