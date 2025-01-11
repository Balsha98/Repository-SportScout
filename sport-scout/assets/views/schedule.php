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

if ($teamID !== 0) {
    $teamData = $db->getTeamDataByTeamId('team_name', $teamID);

    // In case the team was deleted.
    if (count($teamData) > 0) {
        $teamName = $teamData[0]['team_name'];
    } else {
        $teamID = 0;
    }
}

$noneSelected = [
    'section' => $pageData['active'],
    'message' => 'teams from the <a href="admin.php">Admin</a> page'
];

// Fetching the head.
echo Template::generatePageHead($pageData);

if ($roleID !== 5) {
    if ($teamID !== 0) {
        echo Template::generateSchedulePopups($teamID);
        echo Template::generatePopupOverlay();
    }
}

// Fetching the navigation.
echo Template::generatePageHeader($pageData['active'], $roleID);
?>

    <!-- CENTERED CONTAINER -->
    <div class="div-centered-container">
        <!-- MAIN CONTAINER -->
        <main class="main-grid-container">
            <div class="div-team-info-container">
                <header class="data-container-header header-tertiary">
                    <ion-icon class="team-icon" name="people-circle-outline"></ion-icon>
                    <h2 class="heading-tertiary">Team</h2>
                </header>
                <?php
                if ($teamID === 0)
                    echo Template::generateNoneSelectedDiv($noneSelected);
                else {
                    $teamData = $db->getTeamDataByTeamId('*', $teamID);
                    echo Template::generateScheduleTeamData($teamData, $roleID);
                }
                ?>
            </div>
            <div class="div-schedule-container">
                <header class="data-container-header header-tertiary">
                    <ion-icon class="team-icon" name="calendar-outline"></ion-icon>
                    <h2 class="heading-tertiary">Schedule</h2>
                </header>
                <?php
                if ($teamID === 0)
                    echo Template::generateNoneSelectedDiv($noneSelected);
                else {
                    $schedule_data = $db->getScheduleByTeamId($teamID);
                    echo Template::generateScheduleGame($schedule_data, $roleID);
                }
                ?>
            </div>
        </main>
    </div>

<?php
// Fetching the footer.
echo Template::generatePageFooter();
?>
