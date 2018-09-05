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
 * @author majkel
 *
 * @coversDefaultClass \org\majkel\dbase\field\NumericField
 */
class NumericFieldTest extends AbstractFieldTest {

    const CLS = '\org\majkel\dbase\field\NumericField';
    const TYPE = Field::TYPE_NUMERIC;

    /**
     * @return \org\majkel\dbase\Field
     */
    protected function getFieldObject() {
        return parent::getFieldObject()->setLength(3)->setDecimalCount(0);
    }

    /**
     * {@inheritdoc}
     */
    public function dataFromData() {
        return array(
            array('1234', 123),
            array('123', 123),
            array(123, 123),
            array(' 2', 2),
            array(null, 0),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function dataToData() {
        return array(
            array(123456, '123'),
            array(1,      '1'),
            array(null,   ''),
            array(false,  '0'),
            array('',     '0'),
        );
    }

    /**
     * @return integer
     */
    public function getDefaultLength() {
        return 10;
    }
}
