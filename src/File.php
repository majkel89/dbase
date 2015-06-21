<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace org\majkel\dbase;

use SplFileObject;
use ReflectionClass;

/**
 * Description of SplFileObject
 *
 * @author majkel
 */
class File extends SplFileObject {

    /**
     * @param integer $length
     * @return string|false
     */
    public function fread($length) {
        $result = false;
        while (!$this->eof() && $length > 0) {
            $result .= $this->fgetc();
            $length--;
        }
        return $result;
    }

    /**
     * @param string $path
     * @param string $mode
     * @param string $class
     * @return \SplFileObject
     */
    public static function getObject($path, $mode, $class = '\SplFileObject') {
        $reflection = new ReflectionClass($class);
        if ($reflection->hasMethod('fread')) {
            $fileObject = $reflection->newInstance($path, $mode);
        } else {
            $fileObject = new self($path, $mode);
        }
        return $fileObject;
    }

}
