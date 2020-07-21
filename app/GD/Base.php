<?php

namespace App\GD;

use Symfony\Component\HttpFoundation\File\Exception\NoFileException;

class Base
{
    public static function res($path, $type)
    {
        $realpath = realpath(resource_path('gd/' . $type . '/' . $path));
        $realrequire = realpath(resource_path('gd/' . $type . '/'));
        if (substr($realpath, 0, strlen($realrequire)) == $realrequire) {
            return $realpath;
        }
        throw new NoFileException();
    }

    public static function color_code($color)
    {
        $color_code = [
            '0' => 'black',
            'black' => '0',
            '1' => 'dark_blue',
            'dark_blue' => '1',
            '2' => 'dark_green',
            'dark_green' => '2',
            '3' => 'dark_aqua',
            'dark_aqua' => '3',
            '4' => 'dark_red',
            'dark_red' => '4',
            '5' => 'dark_purple',
            'dark_purple' => '5',
            '6' => 'gold',
            'gold' => '6',
            '7' => 'gray',
            'gray' => '7',
            '8' => 'dark_gray',
            'dark_gray' => '8',
            '9' => 'blue',
            'blue' => '9',
            'a' => 'green',
            'green' => 'a',
            'b' => 'aqua',
            'aqua' => 'b',
            'c' => 'red',
            'red' => 'c',
            'd' => 'light_purple',
            'light_purple' => 'd',
            'e' => 'yellow',
            'yellow' => 'e',
            'f' => 'white',
            'white' => 'f',
            'r' => 'white'
        ];
        return $color_code[$color];
    }

    public static function code_color($color)
    {
        $code_color = [
            'black' => [0, 0, 0],
            'dark_blue' => [0, 0, 170],
            'dark_green' => [0, 170, 0],
            'dark_aqua' => [0, 170, 170],
            'dark_red' => [170, 0, 0],
            'dark_purple' => [170, 0, 170],
            'gold' => [255, 170, 0],
            'gray' => [170, 170, 170],
            'dark_gray' => [85, 85, 85],
            'blue' => [85, 85, 255],
            'green' => [85, 255, 85],
            'aqua' => [85, 255, 255],
            'red' => [255, 85, 85],
            'light_purple' => [255, 85, 255],
            'yellow' => [255, 255, 85],
            'white' => [255, 255, 255],
        ];
        return $code_color[$color];
    }
}
