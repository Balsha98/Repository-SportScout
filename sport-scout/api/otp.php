<?php declare(strict_types=1);

require_once '../assets/class/Session.php';
require_once '../assets/class/Database.php';
require_once '../assets/class/Encoder.php';

Session::commence();
$input = Encoder::fromJSON(file_get_contents('php://input'));
$data = ['status' => 'fail'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userID = Session::getSessionVar('user_id');
    ['otp_code' => $userOTPCode] = $db->getUserOTPCode($userID);

    // Verify OTP code.
    if ($input['otp'] === $userOTPCode) {
        $data = ['status' => 'success'];

        // Final session variable.
        Session::setSessionVar('login', true);
    }

    echo Encoder::toJSON($data);
}
