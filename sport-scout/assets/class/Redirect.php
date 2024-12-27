<?php

class Redirect
{
    public static function redirect_to($page)
    {
        header("location:{$page}");
    }
}
