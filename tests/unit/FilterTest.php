<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace org\majkel\dbase\tests\integration;

use org\majkel\dbase\tests\utils\TestBase;

/**
 * Record class tests
 *
 * @author majkel
 *
 * @coversDefaultClass \org\majkel\dbase\Filter
 */
class FilterTest extends TestBase {

    /**
     * @covers ::fromValue
     */
    public function testFromValue() {
        self::assertSame('test', $this->getFilterMock()->fromValue('test'));
    }

    /**
     * @covers ::toValue
     */
    public function testToValue() {
        self::assertSame('test', $this->getFilterMock()->toValue('test'));
    }
}
