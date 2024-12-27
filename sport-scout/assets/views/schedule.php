<?php declare(strict_types=1);

$noneSelected = [
    'section' => $pageData['active'],
    'message' => 'teams from the <a href="admin.php">Admin</a> page'
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

if ($teamID !== 0) {
    $teamData = $db->get_team_data_by_team_id('team_name', $teamID);

    // In case the team was deleted.
    if (count($teamData) > 0) {
        $teamName = $teamData[0]['team_name'];
    } else {
        $teamID = 0;
    }
}

// Fetching the head.
echo Template::generate_page_head($pageData);

if ($roleID !== 5) {
    if ($teamID !== 0) {
        echo Template::generate_schedule_popups($teamID);
        echo Template::generate_popup_overlay();
    }
}

// Fetching the navigation.
echo Template::generate_page_header($pageData['active'], $roleID);
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
                    echo Template::generate_none_selected_div($noneSelected);
                else {
                    $teamData = $db->get_team_data_by_team_id('*', $teamID);
                    echo Template::generate_schedule_team_data($teamData, $roleID);
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
                    echo Template::generate_none_selected_div($noneSelected);
                else {
                    $schedule_data = $db->get_schedule_by_team_id($teamID);
                    echo Template::generate_schedule_game($schedule_data, $roleID);
                }
                ?>
            </div>
        </main>
    </div>

<?php
// Fetching the footer.
echo Template::generate_page_footer();
?>
