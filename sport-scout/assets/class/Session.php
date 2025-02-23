<?php declare(strict_types=1);

class Session
{
    public static function commence()
    {
        if (PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function logout()
    {
        foreach ($_SESSION as $key => $value) {
            unset($_SESSION[$key]);
        }

        session_destroy();
    }

    public static function getSessionVar($key)
    {
        return $_SESSION[$key];
    }

    public static function setSessionVar($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    public static function isSessionVarSet($key)
    {
        return isset($_SESSION[$key]);
    }
}
