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
 * @coversDefaultClass \org\majkel\dbase\field\MemoField
 */
class MemoFieldTest extends AbstractFieldTest {

    const CLS = '\org\majkel\dbase\field\MemoField';
    const TYPE = Field::TYPE_MEMO;

    /**
     * {@inheritdoc}
     */
    public function dataFromData() {
        return [
            [" some data\x1A\x1A\0\0\0\0", ' some data'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function dataToData() {
        return [
            ["some data", "some data\x1A\x1A"],
            ["some data\x1A", "some data\x1A\x1A"],
            ["some data\x1A\x1A", "some data\x1A\x1A"],
        ];
    }

}
