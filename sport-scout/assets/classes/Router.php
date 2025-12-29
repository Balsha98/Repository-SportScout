<?php declare(strict_types=1);

class Router
{
    private static $validRoutes = [
        'logout',
        'login',
        'otp',
        'dashboard',
        'team',
        'schedule',
        'admin',
    ];

    private static $validViews = [
        'login' => [
            'active' => 'login',
            'title' => 'Login'
        ],
        'otp' => [
            'active' => 'otp',
            'title' => 'One-Time Password'
        ],
        'dashboard' => [
            'active' => 'dashboard',
            'title' => 'Dashboard'
        ],
        'team' => [
            'active' => 'team',
            'title' => 'Team'
        ],
        'schedule' => [
            'active' => 'schedule',
            'title' => 'Schedule'
        ],
        'admin' => [
            'active' => 'admin',
            'title' => 'Administrator'
        ],
    ];

    public static function renderPage($api)
    {
        $page = '';
        if ($api === '/') {
            $page = 'login';
        } else {
            $url = explode('/', $api);
            if (!in_array($url[0], self::$validRoutes)) {
                return 'Invalid route.';
            }

            $page = $url[0];
        }

        $pageData = [];
        if (array_key_exists($page, self::$validViews)) {
            $pageData = self::$validViews[$page];
        }

        ob_start();

        require_once 'assets/classes/Cookie.php';
        require_once 'assets/classes/Session.php';
        require_once 'assets/classes/Database.php';
        require_once 'assets/classes/Template.php';
        require_once 'assets/classes/Redirect.php';
        require_once "assets/views/{$page}.php";

        return ob_get_clean();
    }
}
