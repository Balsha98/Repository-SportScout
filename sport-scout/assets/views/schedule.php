<?php declare(strict_types=1);

// Page data.
$data = [
    'active' => 'schedule',
    'title' => 'Schedule'
];

$none = [
    'section' => $data['active'],
    'message' => 'teams from the <a href="admin.php">Admin</a> page'
];

// Required classes.
require_once '../class/Session.php';
require_once '../class/Database.php';
require_once '../class/Redirect.php';
require_once '../class/Template.php';

Session::commence();

if (!Session::is_logged_in()) {
    Redirect::redirect_to('login');
}

// Check if the username was changed.
if (isset($_COOKIE['new_username'])) {
    Session::set_username($_COOKIE['new_username']);
    Cookie::unset_cookie('new_username');
}

// User data.
$username = Session::get_username();
$user_data = $db->get_current_user_data($username);
$role_id = (int) Session::get_role_id();

// Making sure a team exists,
// or was selected for viewing.
$team_name = 'Team';
$team_id = (int) $user_data['team_id'];
if ($role_id < 3) {
    if (isset($_GET['team_id'])) {
        if (!empty($_GET['team_id'])) {
            $team_id = (int) $_GET['team_id'];
        }
    } else if (isset($_COOKIE['view_team_by_id'])) {
        $team_id = (int) $_COOKIE['view_team_by_id'];
    }
}

if ($team_id !== 0) {
    $team_data = $db->get_team_data_by_team_id('team_name', $team_id);

    // In case the team was deleted.
    if (count($team_data) > 0) {
        $team_name = $team_data[0]['team_name'];
    } else {
        $team_id = 0;
    }
}

// Fetching the head.
echo Template::generate_page_head($data);

if ($role_id !== 5) {
    if ($team_id !== 0) {
        echo Template::generate_schedule_popups($team_id);
        echo Template::generate_popup_overlay();
    }
}

// Fetching the navigation.
echo Template::generate_page_header($data['active'], $role_id);
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
                    if ($team_id === 0)
                        echo Template::generate_none_selected_div($none);
                    else {
                        $team_data = $db->get_team_data_by_team_id('*', $team_id);
                        echo Template::generate_schedule_team_data($team_data, $role_id);
                    }
                ?>
            </div>
            <div class="div-schedule-container">
                <header class="data-container-header header-tertiary">
                    <ion-icon class="team-icon" name="calendar-outline"></ion-icon>
                    <h2 class="heading-tertiary">Schedule</h2>
                </header>
                <?php
                    if ($team_id === 0)
                        echo Template::generate_none_selected_div($none);
                    else {
                        $schedule_data = $db->get_schedule_by_team_id($team_id);
                        echo Template::generate_schedule_game($schedule_data, $role_id);
                    }
                ?>
            </div>
        </main>
    </div>

<?php
// Fetching the footer.
echo Template::generate_page_footer();
?>
