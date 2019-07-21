<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace org\majkel\dbase\field;

use \org\majkel\dbase\Field;
use DateTime;

/**
 * Description of Field
 *
 * @author majkel
 */
class DateField extends Field {

    /**
     * DateField constructor.
     */
    public function __construct() {
        $this->length = 8;
    }

    /**
     * {@inheritdoc}
     */
    public function toData($value) {
        if (is_null($value)) {
            return "";
        } else if ($value instanceof DateTime) {
            return $value->format('Ymd');
        } else if (is_string($value)) {
            return date('Ymd', strtotime($value));
        } else {
            return date('Ymd', $value);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function fromData($data) {
        $date = new DateTime();
        if (preg_match('/^[0-9]+$/', $data)) {
            $date->setDate(
                    (integer)substr($data, 0, 4),
                    (integer)substr($data, 4, 2),
                    (integer)substr($data, 6, 2)
            );
            $date->setTime(0, 0, 0);
        } else {
            $date->setTimestamp(0);
        }
        return $date;
    }

    /**
     * {@inheritdoc}
     */
    public function getType() {
        return Field::TYPE_DATE;
    }

}
