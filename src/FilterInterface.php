<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace org\majkel\dbase;

/**
 * Filter interface
 *
 * @author majkel
 */
interface FilterInterface {

    /**
     * @param mixed $value
     * @return mixed
     */
    public function fromValue($value);

    /**
     * @param mixed $value
     * @return mixed
     */
    public function toValue($value);

    /**
     * @param integer $type
     * @return boolean
     */
    public function supportsType($type);

}
