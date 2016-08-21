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

    /**
     * @param array|\ArrayObject|\Traversable $data
     * @return array
     * @throws \org\majkel\dbase\Exception
     */
    public static function toArray($data) {
        if (is_array($data)) {
            return $data;
        } else if ($data instanceof \ArrayObject) {
            return $data->getArrayCopy();
        } else if ($data instanceof \Traversable) {
            $result = array();
            foreach ($data as $key => $value) {
                $result[$key] = $value;
            }
            return $result;
        } else {
            throw new Exception('Unable to convert ' . self::getType($data) . ' to array');
        }
    }

    /**
     * @return bool
     */
    public static function isWindows()
    {
        return strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
    }
}
