<?php declare(strict_types=1);

echo Template::generatePageHead($pageData);
?>

    <!-- ONE-TIME PASSWORD POPUP -->
    <div class="popup-otp-verification">
        <div class="div-popup-content-container">
            <header class="div-popup-content-header">
                <h4>Verify OTP Code</h4>
                <p>Enter the number <strong>sent</strong> to your email.</p>
            </header>
            <form class="form" action="/api/otp.php" method="POST">
                <div class="div-inputs">
                    <input id="otp_num_1" class="otp-input" type="number" name="otp_num_1" data-next="2" autofocus>
                    <input id="otp_num_2" class="otp-input" type="number" name="otp_num_2" data-next="3">
                    <input id="otp_num_3" class="otp-input" type="number" name="otp_num_3" data-next="4">
                    <input id="otp_num_4" class="otp-input" type="number" name="otp_num_4" data-next="5">
                    <input id="otp_num_5" class="otp-input" type="number" name="otp_num_5" data-next="6">
                    <input id="otp_num_6" class="otp-input" type="number" name="otp_num_6" data-next="7">
                </div>
                <div class="div-btn-container">
                    <button class="btn btn-full btn-verify">
                        <span>Verify</span>
                        <ion-icon class="arrow-icon" name="arrow-forward-outline"></ion-icon>
                    </button>
                </div>
            </form>
        </div>
    </div>
    
<?php
echo Template::generatePageFooter();
?>
