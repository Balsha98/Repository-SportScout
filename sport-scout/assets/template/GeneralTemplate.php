<?php declare(strict_types=1);

require_once 'assets/data/GeneralData.php';

class GeneralTemplate
{
    public static function generatePageHead($data)
    {
        // Timestamp.
        $timestamp = time();

        return "
            <!DOCTYPE html>
                <html lang='en'>

                <head>
                    <meta charset='UTF-8'>
                    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                    <link rel='icon' href='" . SERVER . "/assets/media/site-icon.ico'>
                    <link rel='stylesheet' href='" . SERVER . "/assets/css/general.css?ts={$timestamp}'>
                    <link rel='stylesheet' href='" . SERVER . "/assets/css/{$data['active']}.css?ts={$timestamp}'>
                    <script src='" . SERVER . "/assets/js/lib/jQuery.js' defer></script>
                    <script type='module' src='" . SERVER . "/assets/js/views/{$data['active']}.js' defer></script>
                    <title>SportScout | {$data['title']}</title>
                </head>

                <body>
        ";
    }

    public static function generatePopupOverlay()
    {
        return '
            <!-- POPUP OVERLAY -->
            <div class="popup-overlay hide-element">&nbsp;</div>
        ';
    }

    public static function generatePageHeader($page, $roleID)
    {
        $listItems = '';
        $active = self::isPageActive($page);
        $headerLinks = GeneralData::NAV_LINKS;

        if ($roleID === 5) {
            array_pop($headerLinks);
        }

        foreach ($headerLinks as $index => $link) {
            $pageName = ucfirst($link);
            $icon = self::getLinkIcon($link);

            $listItems .= "
                <li class='top-navbar-list-item {$active[$index]}'>
                    <p class='page-icon-text'>{$pageName}</p>
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
                            {$listItems}
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

    private static function isPageActive($currPage)
    {
        foreach (GeneralData::NAV_LINKS as $page) {
            $result[] = $page === $currPage ? 'active-page' : '';
        }

        return $result;
    }

    public static function generateThemeSection()
    {
        return '
            <!-- LEFT SECTION -->
            <section class="section-theme">
                <div class="div-section-theme-text">
                    <h1 class="heading-primary">SportScout</h1>
                    <p>Where <span>passion</span> meets the <span>game</span>.</p>
                </div>
                <div class="div-socials-container">
                    <p>For more info check us out on:</p>
                    <ul class="socials-icons-list">
                        <li><ion-icon class="socials-icon" name="logo-facebook"></ion-icon></li>
                        <li><ion-icon class="socials-icon" name="logo-twitter"></ion-icon></li>
                        <li><ion-icon class="socials-icon" name="logo-youtube"></ion-icon></li>
                    </ul>
                </div>
            </section>
        ';
    }

    public static function generateDashboardLinks($roleID)
    {
        $dashboardLinks = GeneralData::NAV_LINKS;
        array_shift($dashboardLinks);  // Remove dashboard.

        // Remove admin.
        if ($roleID === 5) {
            array_pop($dashboardLinks);
        }

        $return = '';
        foreach ($dashboardLinks as $link) {
            $pageName = ucfirst($link);
            $icon = self::getLinkIcon($link);

            $return .= "
                <li class='pages-list-item'>
                    <div class='div-pages-list-link'>
                        <ion-icon class='pages-icon' name='{$icon}-outline'></ion-icon>
                        <a class='page-link' href='/{$link}'>{$pageName}</a>
                    </div>
                    <ion-icon class='caret-right-icon' name='caret-forward-outline'></ion-icon>
                </li>
            ";
        }

        return $return;
    }

    private static function getLinkIcon($link)
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
    public static function generatePageFooter()
    {
        return "
                <!-- ION-ICONS SCRIPT -->
                <script type='module' src='https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js'></script>
            </body>

            </html>
        ";
    }
}
