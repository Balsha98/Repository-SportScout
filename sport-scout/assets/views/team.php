<?php declare(strict_types=1);

// Page data.
$data = [
    'active' => 'team',
    'title' => 'Team'
];

$none = [
    'section' => $data['active'],
    'message' => 'teams from the <a href="admin.php">Admin</a> page'
];

// Required classes.
require_once '../class/Redirect.php';
require_once '../class/Session.php';
require_once '../class/Database.php';
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

$league_name = '';
if ($team_id !== 0) {
    $team_data = $db->get_team_data_by_team_id('*', $team_id);

    // In case the team was deleted.
    if (count($team_data) > 0) {
        $team_name = $team_data[0]['team_name'];
        $league_name = $team_data[0]['league_name'];
    } else {
        $team_id = 0;
    }
}

// Fetching the head.
echo Template::generate_page_head($data);

if ($role_id !== 5) {
    if ($team_id !== 0) {
        echo Template::generate_team_popups($team_data);
        echo Template::generate_popup_overlay();
    }
}

// Fetching the navigation.
echo Template::generate_page_header($data['active'], $role_id);

?>
    <!-- CENTERED CONTAINER -->
    <div class="div-centered-container">
        <!-- MAIN GRID LAYOUT -->
        <main class="grid-layout">
            <!-- TEAM INFO DIV (TOP LEFT) -->
            <div class="div-data-container div-team-info-container">
                <header class="data-container-header">
                    <ion-icon class="team-icon" name="people-circle-outline"></ion-icon>
                    <h2 class="heading-secondary"><?php echo $team_name; ?></h2>
                </header>
            </div>
            <!-- PLAYERS CONTAINER (RIGHT) -->
            <div class="div-data-container div-players-container">
                <header class="data-container-header header-tertiary">
                    <ion-icon class="team-icon" name="people-outline"></ion-icon>
                    <h2 class="heading-tertiary">Players</h2>
                </header>
                <?php
                    if ($team_id === 0)
                        echo Template::generate_none_selected_div($none);
                    else {
                        $players = $db->get_players_by_team_id($team_id);
                        echo Template::generate_team_players_data($players, $role_id, $league_name);
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
                    if ($team_id === 0)
                        echo Template::generate_none_selected_div($none);
                    else {
                        $staff = $db->get_staff_by_team_id($team_id);
                        echo Template::generate_team_staff_data($staff, $role_id, $league_name);
                    }
                ?>
            </div>
            <div class="div-btn-schedule">
                <span class="btn-schedule-span">View Schedule</span>
                <a class="btn-schedule" href="schedule.php?team_id=<?php echo $team_id; ?>">
                    <ion-icon class="btn-schedule-icon" name="calendar-outline"></ion-icon>
                </a>
            </div>
        </main>
    </div>
    
<?php
// Fetching the footer.
echo Template::generate_page_footer();
?>
