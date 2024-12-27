<?php

class Cookie
{
    static function set_cookie($key, $value)
    {
        setcookie($key, $value, time() + 60 * 60 * 24 * 30);
    }

    static function unset_cookie($key)
    {
        setcookie($key, '', time());
    }

    static function unset_all()
    {
        foreach ($_COOKIE as $key => $value) {
            setcookie($key, '', time());
        }
    }
}
