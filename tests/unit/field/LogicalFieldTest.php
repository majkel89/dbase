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
 * @coversDefaultClass \org\majkel\dbase\field\LogicalField
 */
class LogicalFieldTest extends AbstractFieldTest {

    const CLS = '\org\majkel\dbase\field\LogicalField';
    const TYPE = Field::TYPE_LOGICAL;

    /**
     * {@inheritdoc}
     */
    public function dataFromData() {
        return array(
            array('T', true),
            array('F', false),
            array('Y', true),
            array('N', false),
            array('?', null),
            array('X', null),
            array(  1, null),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function dataToData() {
        return array(
            array(true,    'T'),
            array(false,   'F'),
            array(null,    '?'),
            array(0,       'F'),
            array('',      'F'),
            array(array(), 'F'),
            array(123,     'T'),
        );
    }

    /**
     * @return integer
     */
    public function getDefaultLength() {
        return 1;
    }
}
