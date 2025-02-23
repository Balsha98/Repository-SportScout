<?php declare(strict_types=1);

Session::commence();
if (Session::isSessionVarSet('login')) {
    Redirect::toPage('dashboard');
}

// Fetching the head.
echo Template::generatePageHead($pageData);
?>
    <div class="grid-container">
        <!-- WARNING POPUP -->
        <div class="popup-warning hide-element">
            <button class="popup-btn-close">
                <ion-icon class="close-icon" name="close-outline"></ion-icon>
            </button>
            <div class="div-warning-container">
                <ion-icon class="warning-icon" name="alert-circle-outline"></ion-icon>
                <div class="popup-text-container">
                    <h4>Invalid Login Credentials</h4>
                    <p>Make sure you login in with <strong>valid</strong> credentials.</p>
                </div>
            </div>
            <button class="btn btn-full btn-try-again">Try Again</button>
        </div>
        <!-- POPUP OVERLAY -->
        <div class="popup-overlay hide-element">&nbsp;</div>
        <!-- LEFT SECTION (THEME) -->
        <section class="section-theme">
            <div class="div-section-theme-text">
                <h1 class="heading-primary">SportScout</h1>
                <p>Where <span>passion</span> meets the <span>game</span>.</p>
            </div>
            <div class="div-socials-container">
                <p>For more info check us out on:</p>
                <ul class="socials-icons-list">
                    <li><ion-icon class="socials-icon" name="logo-facebook"></ion-icon></li>
                    <li><ion-icon class="socials-icon" name="logo-twitter"></ion-icon></li>
                    <li><ion-icon class="socials-icon" name="logo-youtube"></ion-icon></li>
                </ul>
            </div>
        </section>
        <!-- RIGHT SECTION (FORM) -->
        <section class="section-login">
            <div class="div-centered-absolute">
                <header class="section-login-header">
                    <h2>Welcome back!</h2>
                    <p>Please provide your <span>credentials</span> in the below form.</p>
                </header>
                <form 
                    class="form form-login" 
                    action="/api/login.php" 
                    method="POST"
                >
                    <div class="div-inputs">
                        <div class="div-input-container">
                            <label for="username">Username:</label>
                            <input 
                                id="username" 
                                type="text" 
                                name="username" 
                                autocomplete="off" 
                                placeholder="John Doe" 
                                autofocus
                            >
                        </div>
                        <div class="div-input-container">
                            <label for="password">Password:</label>
                            <input 
                                id="password" 
                                type="password" 
                                name="password" 
                                autocomplete="off" 
                                placeholder="●●●●●●●●"
                            >
                        </div>
                    </div>
                    <div class="div-btn-container">
                        <button class="btn btn-full btn-login">
                            <span>Log In</span>
                            <ion-icon class="arrow-icon" name="arrow-forward-outline"></ion-icon>
                        </button>
                    </div>
                </form>
            </div>
            <footer class="page-footer">
                <p>&copy; <?php echo date('Y'); ?> <span>SportScout</span>, Inc.</p>
            </footer>
        </section>
    </div>

<?php
// Fetching the footer.
echo Template::generatePageFooter();
?>
