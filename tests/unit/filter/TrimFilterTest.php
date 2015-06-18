<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace org\majkel\dbase\filter;

use org\majkel\dbase\tests\utils\AbstractFilterTest;
use org\majkel\dbase\Field;

/**
 * Record class tests
 *
 * @author majkel
 *
 * @coversDefaultClass \org\majkel\dbase\filter\TrimFilter
 */
class TrimFilterTest extends AbstractFilterTest {

    /**
     * {@inheritdoc}
     */
    public function getFilterObject() {
        return new TrimFilter;
    }

    /**
     * {@inheritdoc}
     */
    public function dataToValue() {
        return [
            [' some text ', 'some text'],
            ['some text', 'some text'],
            [false, ''],
            [123, '123'],
            [null, ''],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function dataFromValue() {
        return [
            [' some text ', 'some text'],
            ['some text', 'some text'],
            [false, ''],
            [123, '123'],
            [null, ''],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getSupportedTypes() {
        return [
            Field::TYPE_CHARACTER,
            Field::TYPE_MEMO,
        ];
    }

    /**
     * @covers ::setFilterInput
     * @covers ::isFilterInput
     * @covers ::__construct
     */
    public function testSetFilterInput() {
        $this->boolGetterSetterTest($this->getFilterObject(),
                'isFilterInput', 'setFilterInput', true);
    }

    /**
     * @covers ::setFilterOutput
     * @covers ::isFilterOutput
     * @covers ::__construct
     */
    public function testSetFilterOutput() {
        $this->boolGetterSetterTest($this->getFilterObject(),
                'isFilterOutput', 'setFilterOutput', true);
    }

}
