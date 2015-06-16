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
 * Description of dBase3Plus
 *
 * @author majkel
 */
class DBase3 extends Format {

    const NAME = 'dBASE III PLUS';

    /**
     * {@inheritdoc}
     */
    protected function createHeader($data) {
        $header = parent::createHeader($data);
        $header->setValid($header->getVersion() & 3);
        return $header;
    }

    /**
     * {@inheritdoc}
     */
    protected function supportsField($field) {
        return in_array($field, [Field::TYPE_CHARACTER, Field::TYPE_DATE,
            Field::TYPE_LOGICAL, Field::TYPE_MEMO, Field::TYPE_NUMERIC]);
    }

}
