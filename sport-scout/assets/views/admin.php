<?php declare(strict_types=1);

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
    Session::setSessionVar('role_id', $_COOKIE['new_role_id']);
    Cookie::unsetCookie('new_role_id');
}

// User data.
$username = Session::getSessionVar('username');
$userData = $db->getCurrentUserData($username);
$roleID = (int) Session::getSessionVar('role_id');
$roleName = $userData['role_name'];
$leagueID = (int) $userData['league_id'];
$teamID = (int) $userData['team_id'];

$noneSelected = [
    'section' => 'section',
    'message' => 'sections from the <span>sidebar</span> on the left'
];

// Fetching the head.
echo Template::generatePageHead($pageData);
?>
    <!-- GRID CONTAINER -->
    <div class="grid-container">
        <?php echo Template::generateAdminPopups($db, $roleID); ?>

        <!-- POPUP OVERLAY -->
        <div class="popup-overlay hide-element">&nbsp;</div>

        <!-- PAGE HEADER -->
        <header class="page-sidebar">
            <h2>Hello, <span class="span-username"><?php echo $username; ?></span>.</h2>
            <nav class="sidebar-nav">
                <ul class="sidebar-nav-list">
                    <?php echo Template::generateAdminLinks($roleID); ?>
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
                    echo Template::generateNoneSelectedDiv($noneSelected);

                    switch ($roleID) {
                        case 1:
                            $allUsers = $db->getAllUsers();
                            echo Template::generateAdminUsersDataContainer($db, $allUsers);

                            $allSports = $db->getAllSports();
                            echo Template::generateAdminSportsDataContainer($allSports);

                            $allLeagues = $db->getAllLeagues();
                            echo Template::generateAdminLeaguesDataContainer($db, $allLeagues, $roleID);

                            $allSeasons = $db->getAllSeasons();
                            echo Template::generateAdminSeasonsDataContainer($db, $allSeasons, $roleID);

                            $allTeams = $db->getAllTeams();
                            echo Template::generateAdminTeamsDataContainer($db, $allTeams, $roleID);

                            $allPositions = $db->getAllPositions();
                            echo Template::generateAdminPositionsDataContainer($db, $allPositions, $roleID);
                            break;
                        case 2:
                            $leaguesData = $db->getLeagueDataByLeagueId($leagueID);
                            echo Template::generateAdminLeaguesDataContainer($db, $leaguesData, $roleID);

                            $seasonsData = $db->getSeasonsByLeagueId($leagueID);
                            echo Template::generateAdminSeasonsDataContainer($db, $seasonsData, $roleID);

                            $teamsData = $db->getTeamsByLeagueId($leagueID);
                            echo Template::generateAdminTeamsDataContainer($db, $teamsData, $roleID);

                            if (count($leaguesData) > 0) {
                                [['sport_id' => $sportID]] = $leaguesData;
                                $positionsData = $db->getPositionsBySportId($sportID);
                                echo Template::generateAdminPositionsDataContainer($db, $positionsData, $roleID);
                            }
                            break;
                        case 3:
                        case 4:
                            $teamData = $db->getTeamDataByTeamId('*', $teamID);
                            echo Template::generateAdminTeamsDataContainer($db, $teamData, $roleID);

                            if (count($teamData) > 0) {
                                [['sport_id' => $sportID]] = $teamData;
                                $positionsData = $db->getPositionsBySportId($sportID);
                                echo Template::generateAdminPositionsDataContainer($db, $positionsData, $roleID);
                            }
                    }
                    ?>
                </div>
            </div>
        </main>
    </div>

<?php
// Fetching the footer.
echo Template::generatePageFooter();
?>
