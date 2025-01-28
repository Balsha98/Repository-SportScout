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

// User data.
$username = Session::getSessionVar('username');
$userData = $db->getCurrentUserData($username);
$roleID = (int) Session::getSessionVar('role_id');

// Making sure a team exists,
// or was selected for viewing.
$teamName = 'Team';
$teamID = (int) $userData['team_id'];
if ($roleID < 3) {
    if (isset($url[1])) {
        if (is_numeric($url[1])) {
            $teamID = (int) $url[1];
        }
    } else if (isset($_COOKIE['view_team_by_id'])) {
        $teamID = (int) $_COOKIE['view_team_by_id'];
    }
}

$leagueName = '';
if ($teamID !== 0) {
    $teamData = $db->getTeamDataByTeamId('*', $teamID);

    // In case the team was deleted.
    if (count($teamData) > 0) {
        [['team_name' => $teamName]] = $teamData;
        [['league_name' => $leagueName]] = $teamData;
    } else {
        $teamID = 0;
    }
}

$noneSelected = [
    'section' => $pageData['active'],
    'message' => 'teams from the <a href="/admin">Admin</a> page'
];

// Fetching the head.
echo Template::generatePageHead($pageData);

if ($roleID !== 5) {
    if ($teamID !== 0) {
        echo Template::generateTeamPopups($db, $teamData);
        echo Template::generatePopupOverlay();
    }
}

// Fetching the navigation.
echo Template::generatePageHeader($pageData['active'], $roleID);
?>
    <!-- CENTERED CONTAINER -->
    <div class="div-centered-container">
        <!-- MAIN GRID LAYOUT -->
        <main class="grid-layout">
            <!-- TEAM INFO DIV (TOP LEFT) -->
            <div class="div-data-container div-team-info-container">
                <header class="data-container-header">
                    <ion-icon class="team-icon" name="people-circle-outline"></ion-icon>
                    <h2 class="heading-secondary"><?php echo $teamName; ?></h2>
                </header>
            </div>
            <!-- PLAYERS CONTAINER (RIGHT) -->
            <div class="div-data-container div-players-container">
                <header class="data-container-header header-tertiary">
                    <ion-icon class="team-icon" name="people-outline"></ion-icon>
                    <h2 class="heading-tertiary">Players</h2>
                </header>
                <?php
                if ($teamID === 0) {
                    echo Template::generateNoneSelectedDiv($noneSelected);
                } else {
                    $players = $db->getPlayersByTeamId($teamID);
                    echo Template::generateTeamPlayersData($db, $players, $roleID, $leagueName);
                }
                ?>
            </div>
            <!-- STAFF CONTAINER (BOTTOM LEFT) -->
            <div class="div-data-container div-staff-container">
                <header class="data-container-header header-tertiary">
                    <ion-icon class="team-icon" name="person-outline"></ion-icon>
                    <h2 class="heading-tertiary">Staff & Fans</h2>
                </header>
                <?php
                if ($teamID === 0) {
                    echo Template::generateNoneSelectedDiv($noneSelected);
                } else {
                    $staff = $db->getStaffByTeamId($teamID);
                    echo Template::generateTeamStaffData($staff, $roleID, $leagueName);
                }
                ?>
            </div>
            <?php if ($teamID !== 0) { ?>
            <div class="div-btn-schedule">
                <span class="btn-schedule-span">View Schedule</span>
                <a class="btn-schedule" href="/schedule/<?php echo $teamID; ?>">
                    <ion-icon class="btn-schedule-icon" name="calendar-outline"></ion-icon>
                </a>
            </div>
            <?php } ?>
        </main>
    </div>
    
<?php
// Fetching the footer.
echo Template::generatePageFooter();
?>
