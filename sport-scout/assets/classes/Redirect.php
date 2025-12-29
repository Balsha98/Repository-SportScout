<?php

class Redirect
{
    public static function toPage($page)
    {
        header("location:{$page}");
    }
}
