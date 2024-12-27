<?php declare(strict_types=1);

class Session
{
    public static function commence()
    {
        if (session_start() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function login($value)
    {
        if (!isset($_SESSION['login'])) {
            $_SESSION['login'] = $value;
        }
    }

    public static function logout()
    {
        foreach ($_SESSION as $key => $value) {
            unset($_SESSION[$key]);
        }

        session_destroy();
    }

    public static function is_logged_in()
    {
        return isset($_SESSION['login']);
    }

    public static function get_username()
    {
        return $_SESSION['username'];
    }

    public static function set_username($username)
    {
        $_SESSION['username'] = $username;
    }

    public static function get_role_id()
    {
        return $_SESSION['role_id'];
    }

    public static function set_role_id($role_id)
    {
        $_SESSION['role_id'] = $role_id;
    }
}
