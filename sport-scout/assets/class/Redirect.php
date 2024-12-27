<?php

class Redirect
{
    static function redirect_to($page)
    {
        header("location:{$page}.php");
        exit();
    }
}
