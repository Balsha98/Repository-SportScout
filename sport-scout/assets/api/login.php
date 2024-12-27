<?php declare(strict_types=1);

require_once '../class/Session.php';
require_once '../class/Database.php';
require_once '../class/Sanitize.php';

Session::commence();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = Sanitize::strip_string($_POST['login_username']);
    $password = Sanitize::strip_string($_POST['login_password']);
    $data = [];

    // VERIFY USER
    if ($db->verify_user($username, $password)) {
        Session::login(true);
        Session::set_username($username);

        $user_id = $db->get_current_user_data($username)['user_id'];

        $data = [
            'message' => 'success',
            'user_id' => $user_id,
            'login_username' => $username,
            'login_password' => $password
        ];
    } else {
        $data = [
            'message' => 'fail',
            'user_id' => '',
            'login_username' => '',
            'login_password' => ''
        ];
    }

    echo json_encode($data);
}
