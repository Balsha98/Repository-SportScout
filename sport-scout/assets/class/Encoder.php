<?php

class Encoder
{
    public static function toJSON($data)
    {
        return json_encode($data);
    }

    public static function fromJSON($data, $isASSOC = true)
    {
        return json_decode($data, $isASSOC);
    }
}
