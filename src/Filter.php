<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace org\majkel\dbase;

/**
 * Filter base class
 *
 * @author majkel
 */
abstract class Filter implements FilterInterface {

    /**
     * {@inheritdoc}
     */
    public function fromValue($value) {
        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function toValue($value) {
        return $value;
    }

}
