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
     * @return integer
     */
    public function getSize() {
        $currentPos = $this->ftell();
        $this->fseek(0, SEEK_END);
        $fileSize = $this->ftell();
        $this->fseek($currentPos, SEEK_SET);
        return $fileSize;
    }

    /**
     * @param string $path
     * @param string $mode
     * @param string $class
     * @return \SplFileObject
     */
    public static function getObject($path, $mode, $class = '\org\majkel\dbase\File') {
        $reflection = new ReflectionClass($class);
        if ($reflection->hasMethod('fread')) {
            $fileObject = $reflection->newInstance($path, $mode);
        } else {
            $fileObject = new FileFixed($path, $mode);
        }
        return $fileObject;
    }

}
