<?php

class Debug
{
    static function print_array($array)
    {
        echo '<pre>';
        print_r($array);
        echo '</pre>';
        exit();
    }

    static function print($data)
    {
        echo $data;
        exit();
    }
}
