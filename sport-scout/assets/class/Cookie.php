<?php

class Cookie
{
    public static function setCookie($key, $value)
    {
        setcookie($key, $value, time() + 60 * 60 * 24 * 30);
    }

    public static function unsetCookie($key)
    {
        setcookie($key, '', time());
    }

    public static function unsetAllCookies()
    {
        foreach ($_COOKIE as $key => $value) {
            setcookie($key, '', time());
        }
    }
}
