<?php

class Debug
{
    public static function printArray($array)
    {
        echo '<pre>';
        print_r($array);
        echo '</pre>';
        exit();
    }

    public static function print($data)
    {
        echo $data;
        exit();
    }
}
