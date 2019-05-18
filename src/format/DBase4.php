<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace org\majkel\dbase\format;

use \org\majkel\dbase\Format;
use org\majkel\dbase\Field;

/**
 * Description of dBase4
 *
 * @author stokescomp
 */
class DBase4 extends Format {

    const NAME = 'dBASE IV';

    /**
     * {@inheritdoc}
     */
    protected function createHeader($data) {
        $header = parent::createHeader($data);
        return $header->setValid($header->getVersion() & $this->getVersion());
    }

    /**
     * {@inheritdoc}
     */
    public function supportsType($type) {
        return in_array($type, array(Field::TYPE_CHARACTER, Field::TYPE_DATE,
            Field::TYPE_LOGICAL, Field::TYPE_MEMO, Field::TYPE_NUMERIC, Field::TYPE_FLOAT));
    }

    /**
     * @return string
     */
    public function getType() {
        return Format::DBASE4;
    }

    /**
     * @return integer
     */
    protected function getVersion() {
        return 4;
    }
}
