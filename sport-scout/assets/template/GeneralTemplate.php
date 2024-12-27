<?php declare(strict_types=1);

require_once 'assets/data/GeneralData.php';

class GeneralTemplate
{
    public static function generate_page_head($data)
    {
        // Timestamp.
        $timestamp = time();

        return "
            <!DOCTYPE html>
                <html lang='en'>

                <head>
                    <meta charset='UTF-8'>
                    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                    <link rel='icon' href='assets/media/site-icon.ico'>
                    <link rel='stylesheet' href='assets/css/general.css?ts={$timestamp}'>
                    <link rel='stylesheet' href='assets/css/{$data['active']}.css?ts={$timestamp}'>
                    <script src='assets/js/jQuery.js' defer></script>
                    <script src='assets/js/cookie.js' defer></script>
                    <script src='assets/js/{$data['active']}.js' defer></script>
                    <title>SportScout | {$data['title']}</title>
                </head>

                <body>
        ";
    }

    public static function generate_popup_overlay()
    {
        return '
            <!-- POPUP OVERLAY -->
            <div class="popup-overlay hide-element">&nbsp;</div>
        ';
    }

    public static function generate_page_header($page, $role_id)
    {
        $list_items = '';
        $active = self::is_page_active($page);
        $header_links = GeneralData::NAV_LINKS;

        if ($role_id === 5) {
            array_pop($header_links);
        }

        foreach ($header_links as $index => $link) {
            $page_name = ucfirst($link);
            $icon = self::get_link_icon($link);

            $list_items .= "
                <li class='top-navbar-list-item {$active[$index]}'>
                    <p class='page-icon-text'>{$page_name}</p>
                    <a href='/{$link}'>
                        <ion-icon class='top-navbar-icon' name='{$icon}-outline'></ion-icon>
                    </a>
                </li>
            ";
        }

        return "
            <!-- PAGE HEADER -->
            <header class='page-header'>
                <div class='div-centered-container'>
                    <div class='div-logo-container'>
                        <a href='/dashboard'>
                            <ion-icon class='logo-icon' name='glasses-outline'></ion-icon>
                            <h2>SportScout</h2>
                        </a>
                    </div>
                    <nav class='top-navbar'>
                        <ul class='top-navbar-list'>
                            {$list_items}
                            <li class='top-navbar-list-item'>
                                <p class='page-icon-text'>Log Out</p>
                                <a href='/logout'>
                                    <ion-icon class='top-navbar-icon' name='log-out-outline'></ion-icon>
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </header>
        ";
    }

    private static function is_page_active($curr_page)
    {
        foreach (GeneralData::NAV_LINKS as $page) {
            $result[] = $page === $curr_page ? 'active-page' : '';
        }

        return $result;
    }

    public static function generate_dashboard_links($role_id)
    {
        $dashboard_links = GeneralData::NAV_LINKS;
        array_shift($dashboard_links);  // Remove dashboard.

        // Remove admin.
        if ($role_id === 5) {
            array_pop($dashboard_links);
        }

        $return = '';
        foreach ($dashboard_links as $link) {
            $page_name = ucfirst($link);
            $icon = self::get_link_icon($link);

            $return .= "
                <li class='pages-list-item'>
                    <div class='div-pages-list-link'>
                        <ion-icon class='pages-icon' name='{$icon}-outline'></ion-icon>
                        <a class='page-link' href='/{$link}'>{$page_name}</a>
                    </div>
                    <ion-icon class='caret-right-icon' name='caret-forward-outline'></ion-icon>
                </li>
            ";
        }

        return $return;
    }

    private static function get_link_icon($link)
    {
        $icon = '';
        switch ($link) {
            case 'dashboard';
                $icon = 'home';
                break;
            case 'team':
                $icon = 'people-circle';
                break;
            case 'schedule':
                $icon = 'calendar';
                break;
            default:
                $icon = 'construct';
        }

        return $icon;
    }

    /**
     * Display the footer.
     * @return string - page's footer.
     */
    public static function generate_page_footer()
    {
        return "
                <!-- ION-ICONS SCRIPT -->
                <script type='module' src='https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js'></script>
            </body>

            </html>
        ";
    }
}
