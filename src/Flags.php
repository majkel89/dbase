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
class Flags {

    /** @var integer */
    private $flags = 0;

    /**
     * Flags constructor.
     *
     * @param integer $flags
     */
    public function __construct($flags = 0) {
        $this->flags = (integer) $flags;
    }

    /**
     * @param integer $flag
     * @param integer $enable
     * @return void
     */
    public function enableFlag($flag, $enable) {
        if ($enable) {
            $this->flags |= $flag;
        } else {
            $this->flags &= (~$flag);
        }
    }

    /**
     * @param integer $flag
     * @return boolean
     */
    public function flagEnabled($flag) {
        return ($this->flags & $flag) > 0;
    }

}
