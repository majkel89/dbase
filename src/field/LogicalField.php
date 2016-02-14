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
class LogicalField extends Field {

    /**
     * {@inheritdoc}
     */
    public function toData($value) {
        if (is_null($value)) {
            return '?';
        } else {
            return $value ? 'T' : 'F';
        }
    }

    /**
     * {@inheritdoc}
     */
    public function fromData($data) {
        if (in_array($data, array('T', 'Y'))) {
            $value = true;
        } else if (in_array($data, array('F', 'N'))) {
            $value = false;
        } else {
            $value = null;
        }
        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function getType() {
        return Field::TYPE_LOGICAL;
    }

}
