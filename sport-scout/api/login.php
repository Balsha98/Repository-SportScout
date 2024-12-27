<?php declare(strict_types=1);

require_once '../assets/class/Session.php';
require_once '../assets/class/Database.php';
require_once '../assets/class/Sanitize.php';

Session::commence();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = Sanitize::strip_string($_POST['login_username']);
    $password = Sanitize::strip_string($_POST['login_password']);
    $data = ['status' => 'fail'];

    // Verify user credentials.
    if ($db->verify_user($username, $password)) {
        Session::login(true);
        Session::set_username($username);

        $user_id = $db->get_current_user_data($username)['user_id'];

        $data = [
            'status' => 'success',
            'user_id' => $user_id
        ];
    }

    echo json_encode($data);
}
