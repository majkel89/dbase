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
class MemoField extends Field {

    const CHAR_MASK = "\x00\x\x1A\x04\x02";

    /**
     * CharacterField constructor.
     */
    public function __construct() {
        $this->length = 10;
    }

    /**
     * {@inheritdoc}
     */
    public function toData($value) {
        return $this->fromData($value) . "\x1A\x1A";
    }

    /**
     * {@inheritdoc}
     */
    public function fromData($data) {
        return rtrim($data, self::CHAR_MASK);
    }

    /**
     * {@inheritdoc}
     */
    public function getType() {
        return Field::TYPE_MEMO;
    }

    /**
     * {@inheritdoc}
     */
    public function isMemoEntry() {
        return true;
    }

}
