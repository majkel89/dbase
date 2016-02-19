<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace org\majkel\dbase;

/**
 * Description of SplFileObject
 *
 * @author majkel
 */
class FileFixed extends File {

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

}
