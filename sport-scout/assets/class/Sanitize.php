<?php declare(strict_types=1);

require_once 'Debug.php';

class Sanitize
{
    public static function strip_string($input)
    {
        return strip_tags(stripslashes(trim($input)));
    }

    public static function is_year_formatted($year)
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

    public static function is_exactly($input, $limit)
    {
        return (int) strlen($input) === $limit;
    }

    public static function is_shorter($input, $limit)
    {
        return (int) strlen($input) <= $limit;
    }

    public static function contains($pattern, $input)
    {
        return (bool) preg_match($pattern, $input);
    }

    public static function full_string_search(&$message, &$input, $limit)
    {
        if ($input === '') {
            $message = 'fail';
        } else if (!self::is_shorter($input, $limit)) {
            $message = 'fail';
            $input = '';
        } else if (self::contains('/[^a-zA-Z]/', $input)) {
            $message = 'fail';
            $input = '';
        }
    }

    private static function are_colors_formatted($colors)
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

    public static function full_color_search(&$message, &$colors, $limit)
    {
        if ($colors === '') {
            $message = 'fail';
        } else if (!self::is_shorter($colors, $limit)) {
            $message = 'fail';
            $colors = '';
        } else if (!self::are_colors_formatted($colors)) {
            $message = 'fail';
            $colors = '';
        }
    }
}
