<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace org\majkel\dbase;

use ArrayObject;

/**
 * Stores record data and status
 *
 * @author majkel
 */
class Record extends ArrayObject {

    use Flags;

    const FLAG_DELETED = 1;

    /**
     * @param array $array
     */
    public function __construct($array = []) {
        parent::__construct($array);
        $this->setFlags(ArrayObject::ARRAY_AS_PROPS | ArrayObject::STD_PROP_LIST);
    }

    /**
     * @return boolean;
     */
    public function isDeleted() {
        return $this->flagEnabled(self::FLAG_DELETED);
    }

    /**
     * @param boolean $deleted
     * @return \org\majkel\dbase\Record
     */
    public function setDeleted($deleted) {
        return $this->enableFlag(self::FLAG_DELETED, $deleted);
    }

    /**
     * @return array
     */
    public function toArray() {
        return $this->getArrayCopy();
    }
}
