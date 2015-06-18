<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace org\majkel\dbase;

/**
 * Description of Utils
 *
 * @author majkel
 */
trait Flags {

    /** @var integer */
    protected $flags = 0;

    /**
     * @param integer $flag
     * @param integer $enable
     * @return \org\majkel\dbase\Flags
     */
    protected function enableFlag($flag, $enable) {
        if ($enable) {
            $this->flags |= $flag;
        }
        else {
            $this->flags &= (~$flag);
        }
        return $this;
    }

    /**
     * @param integer $flag
     * @return boolean
     */
    public function flagEnabled($flag) {
        return ($this->flags & $flag) > 0;
    }

}
