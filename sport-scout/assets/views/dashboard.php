<?php declare(strict_types=1);

// Page data.
$data = [
    'active' => 'dashboard',
    'title' => 'Dashboard'
];

// Required classes.
require_once '../class/Session.php';
require_once '../class/Cookie.php';
require_once '../class/Redirect.php';
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
$role_id = (int) $user_data['role_id'];
Session::set_role_id($role_id);
Cookie::set_cookie('role_id', $role_id);

// Fetching the head.
echo Template::generate_page_head($data);

// Fetching the navigation.
echo Template::generate_page_header($data['active'], $role_id);

?>
    <!-- CENTERED CONTAINER -->
    <div class="div-centered-container">
        <!-- DASHBOARD GRID -->
        <main class="grid-dashboard">
            <!-- WELCOME GRID SECTION (LEFT) -->
            <div class="div-welcome-info-container out-shadow-sm">
                <div class="div-welcome-text-container">
                    <h2 class="heading-secondary">Hello, <?php echo $username; ?>.</h2>
                    <p class="welcome-text">
                        Welcome to <span>SportScout</span> &mdash; your ultimate platform for managing sports
                        leagues, teams, and schedules. Whether you're an organizer or a coach, streamline your operations,
                        stay on top of game day logistics, and organize your teams effortlessly.
                    </p>
                </div>
                <div class="div-icons-list-container">
                    <ul class="sports-icons-list">
                        <li class="sports-icons-list-item">
                            <ion-icon class="sport-icon" name="football-outline"></ion-icon>
                        </li>
                        <li class="sports-icons-list-item">
                            <ion-icon class="sport-icon" name="basketball-outline"></ion-icon>
                        </li>
                        <li class="sports-icons-list-item">
                            <ion-icon class="sport-icon" name="american-football-outline"></ion-icon>
                        </li>
                        <li class="sports-icons-list-item">
                            <ion-icon class="sport-icon" name="baseball-outline"></ion-icon>
                        </li>
                        <li class="sports-icons-list-item">
                            <ion-icon class="sport-icon" name="tennisball-outline"></ion-icon>
                        </li>
                    </ul>
                    <p>Any <span>sport</span> imaginable.</p>
                </div>
                <div class="grid-btn-container">
                    <a class="btn btn-hollow btn-logout" href="logout.php">
                        <ion-icon class="arrow-icon" name="arrow-back-outline"></ion-icon>
                        <span>Log Out</span>
                    </a>
                    <button class="btn btn-full btn-start">Get Started</button>
                </div>
            </div>
            <!-- PAGES LIST CONTAINER (RIGHT) -->
            <div class="div-pages-container">
                <div class="div-pages-hero-container">
                    <div class="div-hero-text">
                        <h2>SportScout</h2>
                        <p>Where <span>passion</span> meets the <span>game</span>.</p>
                    </div>
                    <div class="div-hero-socials-container">
                        <p>For more info check us out on:</p>
                        <ul class="hero-icons-list">
                            <li class="hero-icons-list-item">
                                <ion-icon class="hero-socials-icon" name="logo-facebook"></ion-icon>
                            </li>
                            <li class="hero-icons-list-item">
                                <ion-icon class="hero-socials-icon" name="logo-twitter"></ion-icon>
                            </li>
                            <li class="hero-icons-list-item">
                                <ion-icon class="hero-socials-icon" name="logo-youtube"></ion-icon>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="div-pages-list-container transform-y">
                    <h2 class="heading-secondary">Available Pages</h2>
                    <ul class="pages-list">
                        <?php
                            echo Template::generate_dashboard_links($role_id);
                        ?>
                    </ul>
                    <p class="date-text">&nbsp;</p>
                </div>
            </div>
            <div class="page-footer">
                <p>&copy; 2024 <span>SportScout</span>, Inc. | All rights reserved.</p>
            </div>
        </main>
    </div>

<?php
// Fetching the footer.
echo Template::generate_page_footer();
?>