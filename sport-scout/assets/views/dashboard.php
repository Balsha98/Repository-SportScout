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
$roleID = (int) $userData['role_id'];
Session::setSessionVar('role_id', $roleID);
Cookie::setCookie('role_id', $roleID);

// Fetching the head.
echo Template::generatePageHead($pageData);

// Fetching the navigation.
echo Template::generatePageHeader($pageData['active'], $roleID);
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
                    <a class="btn btn-hollow btn-logout" href="/logout">
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
                        echo Template::generateDashboardLinks($roleID);
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
echo Template::generatePageFooter();
?>
