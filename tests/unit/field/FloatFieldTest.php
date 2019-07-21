<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace org\majkel\dbase\field;

use org\majkel\dbase\tests\utils\AbstractFieldTest;
use org\majkel\dbase\Field;

/**
 * Record class tests
 *
 * @author stokescomp
 *
 * @coversDefaultClass \org\majkel\dbase\field\FloatField
 */
class FloatFieldTest extends AbstractFieldTest {

    const CLS = '\org\majkel\dbase\field\FloatField';
    const TYPE = Field::TYPE_FLOAT;

    /**
     * @return \org\majkel\dbase\Field
     */
    protected function getFieldObject() {
        return parent::getFieldObject()->setLength(3)->setDecimalCount(2);
    }

    /**
     * {@inheritdoc}
     */
    public function dataFromData() {
        return array(
            array('1234', 123.0),
            array('123', 123.0),
            array(123, 123.0),
            array(' 2', 2.0),
            array('1234.1234', 123.0),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function dataToData() {
        return array(
            array(123.456, '123'),
            array(1.0,      '1.0'),
            array(null,   ''),
            array(false,  '0.0'),
            array('',     '0.0'),
        );
    }

    /**
     * @return integer
     */
    public function getDefaultLength() {
        return 16;
    }
}
