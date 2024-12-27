<?php declare(strict_types=1);

$noneSelected = [
    'section' => 'section',
    'message' => 'sections from the <span>sidebar</span> on the left'
];

Session::commence();
if (!Session::isSessionVarSet('login')) {
    Redirect::toPage('login');
}

// Check if the username was changed.
if (isset($_COOKIE['new_username'])) {
    Session::setSessionVar('username', $_COOKIE['new_username']);
    Cookie::unsetCookie('new_username');
}

// Check if the role was changed.
if (isset($_COOKIE['new_role_id'])) {
    Session::setSessionVar('username', $_COOKIE['new_role_id']);
    Cookie::unsetCookie('new_role_id');
}

// User data.
$username = Session::getSessionVar('username');
$user_data = $db->get_current_user_data($username);
$role_id = (int) Session::getSessionVar('role_id');
$role_name = $user_data['role_name'];
$league_id = (int) $user_data['league_id'];
$team_id = (int) $user_data['team_id'];

// Fetching the head.
echo Template::generate_page_head($pageData);
?>
    <!-- GRID CONTAINER -->
    <div class="grid-container">
        <?php
        echo Template::generate_admin_popups($role_id);
        ?>

        <!-- POPUP OVERLAY -->
        <div class="popup-overlay hide-element">&nbsp;</div>

        <!-- PAGE HEADER -->
        <header class="page-sidebar">
            <h2>Hello, <span class="span-username"><?php echo $username; ?></span>.</h2>
            <nav class="sidebar-nav">
                <ul class="sidebar-nav-list">
                    <?php
                    echo Template::generate_admin_links($role_id);
                    ?>
                </ul>
            </nav>
            <div class="div-back-to-dashboard">
                <a href="/dashboard" class="btn btn-full btn-sidebar">
                    <span>
                        <ion-icon class="sidebar-nav-icon" name="home-outline"></ion-icon>
                        <span>Dashboard</span>
                    </span>
                    <ion-icon class="open-icon" name="caret-forward-outline"></ion-icon>
                </a>
            </div>
        </header>

        <!-- MAIN DATA CONTAINER -->
        <main class="main-data-container">
            <header class="main-data-header">
                <ion-icon class="admin-icon" name="construct-outline"></ion-icon>
                <h2>Administrator</h2>
            </header>
            <div class="div-main-data-container">
                <div class="div-switchable-container">
                    <?php
                    echo Template::generate_none_selected_div($noneSelected);

                    switch ($role_id) {
                        case 1:
                            $all_users = $db->get_all_users();
                            echo Template::generate_admin_users_data_container($all_users);

                            $all_sports = $db->get_all_sports();
                            echo Template::generate_admin_sports_data_container($all_sports);

                            $all_leagues = $db->get_all_leagues();
                            echo Template::generate_admin_leagues_data_container($all_leagues, $role_id);

                            $all_seasons = $db->get_all_seasons();
                            echo Template::generate_admin_seasons_data_container($all_seasons, $role_id);

                            $all_teams = $db->get_all_teams();
                            echo Template::generate_admin_teams_data_container($all_teams, $role_id);

                            $all_positions = $db->get_all_positions();
                            echo Template::generate_admin_positions_data_container($all_positions, $role_id);
                            break;
                        case 2:
                            $leagues_data = $db->get_league_data_by_league_id($league_id);
                            echo Template::generate_admin_leagues_data_container($leagues_data, $role_id);

                            $seasons_data = $db->get_seasons_by_league_id($league_id);
                            echo Template::generate_admin_seasons_data_container($seasons_data, $role_id);

                            $teams_data = $db->get_teams_by_league_id($league_id);
                            echo Template::generate_admin_teams_data_container($teams_data, $role_id);

                            if (count($leagues_data) > 0) {
                                $sport_id = $leagues_data[0]['sport_id'];
                                $positions_data = $db->get_positions_by_sport_id($sport_id);
                                echo Template::generate_admin_positions_data_container($positions_data, $role_id);
                            }
                            break;
                        case 3:
                        case 4:
                            $team_data = $db->get_team_data_by_team_id('*', $team_id);
                            echo Template::generate_admin_teams_data_container($team_data, $role_id);

                            if (count($team_data) > 0) {
                                $sport_id = $team_data[0]['sport_id'];
                                $positions_data = $db->get_positions_by_sport_id($sport_id);
                                echo Template::generate_admin_positions_data_container($positions_data, $role_id);
                            }
                    }
                    ?>
                </div>
            </div>
        </main>
    </div>

<?php
// Fetching the footer.
echo Template::generate_page_footer();
?>
