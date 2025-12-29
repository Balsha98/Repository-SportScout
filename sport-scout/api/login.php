<?php declare(strict_types=1);

require_once '../assets/classes/Session.php';
require_once '../assets/classes/Database.php';
require_once '../assets/classes/Sanitize.php';
require_once '../assets/classes/Encoder.php';

Session::commence();
$input = Encoder::fromJSON(file_get_contents('php://input'));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = Sanitize::stripString($input['username']);
    $password = Sanitize::stripString($input['password']);
    $data = ['status' => 'fail'];

    // Verify user credentials.
    $passwordHash = hash('sha256', $password);
    if ($db->verifyUser($username, $passwordHash)) {
        $data = ['status' => 'success'];

        // Save user details a session variables.
        ['user_id' => $userID] = $db->getCurrentUserData($username);
        Session::setSessionVar('username', $username);
        Session::setSessionVar('user_id', $userID);
        Session::setSessionVar('login', true);
    }

    echo Encoder::toJSON($data);
}
