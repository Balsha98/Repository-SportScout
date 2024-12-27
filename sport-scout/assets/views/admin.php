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
$userData = $db->get_current_user_data($username);
$roleID = (int) Session::getSessionVar('role_id');
$roleName = $userData['role_name'];
$leagueID = (int) $userData['league_id'];
$teamID = (int) $userData['team_id'];

// Fetching the head.
echo Template::generate_page_head($pageData);
?>
    <!-- GRID CONTAINER -->
    <div class="grid-container">
        <?php
        echo Template::generate_admin_popups($roleID);
        ?>

        <!-- POPUP OVERLAY -->
        <div class="popup-overlay hide-element">&nbsp;</div>

        <!-- PAGE HEADER -->
        <header class="page-sidebar">
            <h2>Hello, <span class="span-username"><?php echo $username; ?></span>.</h2>
            <nav class="sidebar-nav">
                <ul class="sidebar-nav-list">
                    <?php
                    echo Template::generate_admin_links($roleID);
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

                    switch ($roleID) {
                        case 1:
                            $all_users = $db->get_all_users();
                            echo Template::generate_admin_users_data_container($all_users);

                            $all_sports = $db->get_all_sports();
                            echo Template::generate_admin_sports_data_container($all_sports);

                            $all_leagues = $db->get_all_leagues();
                            echo Template::generate_admin_leagues_data_container($all_leagues, $roleID);

                            $all_seasons = $db->get_all_seasons();
                            echo Template::generate_admin_seasons_data_container($all_seasons, $roleID);

                            $all_teams = $db->get_all_teams();
                            echo Template::generate_admin_teams_data_container($all_teams, $roleID);

                            $all_positions = $db->get_all_positions();
                            echo Template::generate_admin_positions_data_container($all_positions, $roleID);
                            break;
                        case 2:
                            $leagues_data = $db->get_league_data_by_league_id($leagueID);
                            echo Template::generate_admin_leagues_data_container($leagues_data, $roleID);

                            $seasons_data = $db->get_seasons_by_league_id($leagueID);
                            echo Template::generate_admin_seasons_data_container($seasons_data, $roleID);

                            $teams_data = $db->get_teams_by_league_id($leagueID);
                            echo Template::generate_admin_teams_data_container($teams_data, $roleID);

                            if (count($leagues_data) > 0) {
                                $sport_id = $leagues_data[0]['sport_id'];
                                $positions_data = $db->get_positions_by_sport_id($sport_id);
                                echo Template::generate_admin_positions_data_container($positions_data, $roleID);
                            }
                            break;
                        case 3:
                        case 4:
                            $team_data = $db->get_team_data_by_team_id('*', $teamID);
                            echo Template::generate_admin_teams_data_container($team_data, $roleID);

                            if (count($team_data) > 0) {
                                $sport_id = $team_data[0]['sport_id'];
                                $positions_data = $db->get_positions_by_sport_id($sport_id);
                                echo Template::generate_admin_positions_data_container($positions_data, $roleID);
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
