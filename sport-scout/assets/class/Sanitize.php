<?php declare(strict_types=1);

require_once 'Debug.php';

class Sanitize
{
    public static function stripString($input)
    {
        return strip_tags(stripslashes(trim($input)));
    }

    public static function isYearFormatted($year)
    {
        if (str_contains($year, '/')) {
            $array = explode('/', $year);
            if (count($array) === 2) {
                $lengths = [4, 2];
                $valid_counter = 0;
                foreach ($array as $i => $value) {
                    if (!self::contains('/[a-zA-Z]/', $value)) {
                        if (strlen($value) === $lengths[$i]) {
                            $valid_counter++;
                        }
                    }
                }

                return count($array) === $valid_counter;
            }
        }

        return false;
    }

    public static function isExactly($input, $limit)
    {
        return (int) strlen($input) === $limit;
    }

    public static function isShorter($input, $limit)
    {
        return (int) strlen($input) <= $limit;
    }

    public static function contains($pattern, $input)
    {
        return (bool) preg_match($pattern, $input);
    }

    public static function fullStringSearch(&$status, &$input, $limit)
    {
        if ($input === '') {
            $status = 'fail';
        } else if (!self::isShorter($input, $limit)) {
            $status = 'fail';
            $input = '';
        } else if (self::contains('/[^a-zA-Z ]/', $input)) {
            $status = 'fail';
            $input = '';
        }
    }

    private static function areColorsFormatted($colors)
    {
        if (str_contains($colors, '/')) {
            $array = explode('/', $colors);
            if (count($array) === 2) {
                $valid_counter = 0;
                foreach ($array as $value) {
                    if (self::contains('/[^a-zA-Z]/', $value)) {
                        $valid_counter++;
                    }
                }

                return count($array) === $valid_counter;
            }
        }

        return false;
    }

    public static function fullColorSearch(&$status, &$colors, $limit)
    {
        if ($colors === '') {
            $status = 'fail';
        } else if (!self::isShorter($colors, $limit)) {
            $status = 'fail';
            $colors = '';
        } else if (!self::areColorsFormatted($colors)) {
            $status = 'fail';
            $colors = '';
        }
    }
}
