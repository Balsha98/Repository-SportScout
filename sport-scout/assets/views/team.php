<?php declare(strict_types=1);

$noneSelected = [
    'section' => $pageData['active'],
    'message' => 'teams from the <a href="/admin">Admin</a> page'
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

// User data.
$username = Session::getSessionVar('username');
$userData = $db->get_current_user_data($username);
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
    $teamData = $db->get_team_data_by_team_id('*', $teamID);

    // In case the team was deleted.
    if (count($teamData) > 0) {
        $teamName = $teamData[0]['team_name'];
        $leagueName = $teamData[0]['league_name'];
    } else {
        $teamID = 0;
    }
}

// Fetching the head.
echo Template::generate_page_head($pageData);

if ($roleID !== 5) {
    if ($teamID !== 0) {
        echo Template::generate_team_popups($teamData);
        echo Template::generate_popup_overlay();
    }
}

// Fetching the navigation.
echo Template::generate_page_header($pageData['active'], $roleID);
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
                if ($teamID === 0)
                    echo Template::generate_none_selected_div($noneSelected);
                else {
                    $players = $db->get_players_by_team_id($teamID);
                    echo Template::generate_team_players_data($players, $roleID, $leagueName);
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
                if ($teamID === 0)
                    echo Template::generate_none_selected_div($noneSelected);
                else {
                    $staff = $db->get_staff_by_team_id($teamID);
                    echo Template::generate_team_staff_data($staff, $roleID, $leagueName);
                }
                ?>
            </div>
            <?php if ($teamID !== 0) { ?>
            <div class="div-btn-schedule">
                <span class="btn-schedule-span">View Schedule</span>
                <a class="btn-schedule" href="schedule.php?team_id=<?php echo $teamID; ?>">
                    <ion-icon class="btn-schedule-icon" name="calendar-outline"></ion-icon>
                </a>
            </div>
            <?php } ?>
        </main>
    </div>
    
<?php
// Fetching the footer.
echo Template::generate_page_footer();
?>
