<?php

namespace Nibble\NibbleForms;

class Useful
{

    /**
     * Strip out all empty characters from a string
     *
     * @param string $val
     *
     * @return string
     */
    public static function stripper($val)
    {
        foreach (array(' ', '&nbsp;', '\n', '\t', '\r') as $strip) {
            $val = str_replace($strip, '', (string) $val);
        }

        return $val === '' ? false : $val;
    }

    /**
     * Slugify a string using a specified replacement for empty characters
     *
     * @param string $text
     * @param string $replacement
     *
     * @return string
     */
    public static function slugify($text, $replacement = '-')
    {
        return strtolower(trim(preg_replace('/\W+/', $replacement, $text), '-'));
    }

    /**
     * Return a random string of specified length
     *
     * @param int    $length
     * @param string $return
     *
     * @return string
     */
    public static function randomString($length = 10, $return = '')
    {
        $string = 'qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM1234567890';
        while ($length-- > 0){
            $return .= $string[mt_rand(0, strlen($string) - 1)];
        }

        return $return;
    }

}
