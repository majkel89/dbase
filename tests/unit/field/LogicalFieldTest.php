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
        return [
            ['T', true],
            ['F', false],
            ['Y', true],
            ['N', false],
            ['?', null],
            ['X', null],
            [  1, null],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function dataToData() {
        return [
            [true,  'T'],
            [false, 'F'],
            [null,  '?'],
            [0,     'F'],
            ['',    'F'],
            [[],    'F'],
            [123,   'T'],
        ];
    }

}
