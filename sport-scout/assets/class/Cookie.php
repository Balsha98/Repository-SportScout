<?php

class Cookie
{
    public static function set_cookie($key, $value)
    {
        setcookie($key, $value, time() + 60 * 60 * 24 * 30);
    }

    public static function unset_cookie($key)
    {
        setcookie($key, '', time());
    }

    public static function unset_all()
    {
        foreach ($_COOKIE as $key => $value) {
            setcookie($key, '', time());
        }
    }
}
