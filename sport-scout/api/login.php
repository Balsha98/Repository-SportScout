<?php declare(strict_types=1);

require_once '../assets/class/Session.php';
require_once '../assets/class/Database.php';
require_once '../assets/class/Sanitize.php';
require_once '../assets/class/Encoder.php';

Session::commence();
$input = Encoder::fromJSON(file_get_contents('php://input'));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = Sanitize::stripString($input['username']);
    $password = Sanitize::stripString($input['password']);
    $data = ['status' => 'fail'];

    // Verify user credentials.
    $password_hash = hash('sha256', $password);
    if ($db->verifyUser($username, $password_hash)) {
        ['user_id' => $userID] = $db->getCurrentUserData($username);
        Session::setSessionVar('username', $username);
        Session::setSessionVar('user_ud', $userID);

        $db->insertNewOTPCode(hash('sha256', random_int(100000, 999999) . ''));
        ['id' => $otpID] = $db->getLastRowId('otp_codes', 'otp');
        Session::setSessionVar('otp_id', $otpID);

        $data = ['status' => 'success'];
    }

    echo json_encode($data);
}
