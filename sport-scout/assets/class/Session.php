<?php declare(strict_types=1);

require_once '../class/Debug.php';

class Session
{
    static function commence()
    {
        if (session_start() === PHP_SESSION_NONE)
            session_start();
    }

    static function login($value)
    {
        if (!isset($_SESSION['login']))
            $_SESSION['login'] = $value;
    }

    static function logout()
    {
        foreach ($_SESSION as $key => $value)
            unset($_SESSION[$key]);

        session_destroy();
    }

    static function is_logged_in()
    {
        return isset($_SESSION['login']);
    }

    static function get_username()
    {
        return $_SESSION['username'];
    }

    static function set_username($username)
    {
        $_SESSION['username'] = $username;
    }

    static function get_role_id()
    {
        return $_SESSION['role_id'];
    }

    static function set_role_id($role_id)
    {
        $_SESSION['role_id'] = $role_id;
    }
}
