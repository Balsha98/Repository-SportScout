<?php declare(strict_types=1);

class Router
{
    public static function renderPage($api)
    {
        $page = '';
        if ($api === '/') {
            $page = 'login';
        } else {
            $url = explode('/', $api);

            // If & Else logic...

            $page = $url[0];
        }

        ob_start();

        require_once 'assets/class/Cookie.php';
        require_once 'assets/class/Session.php';
        require_once 'assets/class/Database.php';
        require_once 'assets/class/Template.php';
        require_once 'assets/class/Redirect.php';
        require_once "assets/views/{$page}.php";

        return ob_get_clean();
    }
}
