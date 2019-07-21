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
 * @author stokescomp
 */
class FloatField extends Field {

    /**
     * FloatField constructor.
     */
    public function __construct() {
        $this->length = 16;
        $this->decimalCount = 14;
    }

    /**
     * {@inheritdoc}
     */
    public function toData($value) {
        if (is_null($value)) {
            return "";
        } else {
            $value = number_format((float) $value, $this->getDecimalCount(), '.', '');
            return substr(strval($value), 0, $this->getLength());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function fromData($data) {
        if (!$this->getDecimalCount()) {
            return (integer)substr($data, 0, $this->getLength());
        } else {
            return (float)number_format(substr($data, 0, $this->getLength()), $this->getDecimalCount(), '.', '');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getType() {
        return Field::TYPE_FLOAT;
    }

}
