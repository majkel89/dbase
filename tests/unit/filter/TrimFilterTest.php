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
        return array(
            array(' some text ', 'some text'),
            array('some text', 'some text'),
            array(false, ''),
            array(123, '123'),
            array(null, ''),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function dataFromValue() {
        return array(
            array(' some text ', 'some text'),
            array('some text', 'some text'),
            array(false, ''),
            array(123, '123'),
            array(null, ''),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getSupportedTypes() {
        return array(
            Field::TYPE_CHARACTER,
            Field::TYPE_MEMO,
        );
    }

    /**
     * @covers ::setFilterInput
     * @covers ::isFilterInput
     * @covers ::getFlags
     */
    public function testSetFilterInput() {
        $this->boolGetterSetterTest($this->getFilterObject(),
                'isFilterInput', 'setFilterInput', true);
    }

    /**
     * @covers ::setFilterOutput
     * @covers ::isFilterOutput
     * @covers ::getFlags
     */
    public function testSetFilterOutput() {
        $this->boolGetterSetterTest($this->getFilterObject(),
                'isFilterOutput', 'setFilterOutput', true);
    }

}
