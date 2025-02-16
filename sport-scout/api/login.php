<?php declare(strict_types=1);

require_once '../assets/class/Session.php';
require_once '../assets/class/Database.php';
require_once '../assets/class/Sanitize.php';

Session::commence();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = Sanitize::stripString($_POST['login_username']);
    $password = Sanitize::stripString($_POST['login_password']);
    $data = ['status' => 'fail'];

    // Verify user credentials.
    $password_hash = hash('sha256', $password);
    if ($db->verifyUser($username, $password_hash)) {
        Session::setSessionVar('login', true);
        Session::setSessionVar('username', $username);
        ['user_id' => $userID] = $db->getCurrentUserData($username);

        $data = [
            'status' => 'success',
            'user_id' => $userID
        ];
    }

    echo json_encode($data);
}
