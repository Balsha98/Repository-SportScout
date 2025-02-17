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
        $data = ['status' => 'success'];

        // Save user details a session variables.
        ['user_id' => $userID] = $db->getCurrentUserData($username);
        Session::setSessionVar('username', $username);
        Session::setSessionVar('user_id', $userID);

        // Generate new OTP code.
        $db->insertNewOTPCode($userID);
    }

    echo Encoder::toJSON($data);
}
