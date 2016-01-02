<?php
/**
 * Created by PhpStorm.
 * User: majkel
 * Date: 02-Jan-16
 * Time: 12:35
 */

namespace org\majkel\dbase;

final class Utils {

    /**
     * Utils constructor.
     * @codeCoverageIgnore
     */
    private function __construct() {
    }

    /**
     * @param $variable
     * @return string
     */
    public static function getType($variable) {
        return is_object($variable) ? get_class($variable) : gettype($variable);
    }

}
