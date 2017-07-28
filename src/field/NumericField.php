<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace org\majkel\dbase\field;

use \org\majkel\dbase\Field;

/**
 * Description of Field
 *
 * @author majkel
 */
class NumericField extends Field {

    /**
     * CharacterField constructor.
     */
    public function __construct() {
        $this->length = 10;
        $this->decimalCount = 0;
    }

    /**
     * {@inheritdoc}
     */
    public function toData($value) {
        return substr(strval((float)number_format($value, $this->getDecimalCount())), 0, $this->getLength());
    }

    /**
     * {@inheritdoc}
     */
    public function fromData($data) {
        if (!$this->getDecimalCount()) {
            return (integer)substr($data, 0, $this->getLength());
        } else {
            return (float)number_format(substr($data, 0, $this->getLength()), $this->getDecimalCount());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getType() {
        return Field::TYPE_NUMERIC;
    }

}
