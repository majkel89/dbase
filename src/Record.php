<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace org\majkel\dbase;

use ArrayObject;

/**
 * Description of Record
 *
 * @author majkel
 */
class Record extends ArrayObject {

    const FLAG_DELETED = 1;

    /** @var integer */
    protected $flags = 0;

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
        return $this->flags & self::FLAG_DELETED;
    }

    /**
     * @param boolean $deleted
     * @return Record
     */
    public function setDeleted($deleted) {
        if ($deleted) {
            $this->flags |= self::FLAG_DELETED;
        } else {
            $this->flags &= (~self::FLAG_DELETED);
        }
        return $this;
    }

    /**
     * @return array
     */
    public function toArray() {
        return $this->getArrayCopy();
    }
}
