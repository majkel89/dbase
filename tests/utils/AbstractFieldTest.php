<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace org\majkel\dbase\tests\utils;

/**
 * Record class tests
 *
 * @author majkel
 */
abstract class AbstractFieldTest extends TestBase {

    /**
     * @return \org\majkel\dbase\Field
     */
    protected function getFieldObject() {
        return $this->mock(static::CLS)->new();
    }

    /**
     * @return integer
     */
    protected function getFieldType() {
        return static::TYPE;
    }

    /**
     * @covers ::getType
     */
    public function testGetType() {
        self::assertSame($this->getFieldType(), $this->getFieldObject()->getType());
    }

    abstract public function dataToData();

    /**
     * @param mixed $input
     * @param string $expected
     * @covers ::toData
     * @dataProvider dataToData
     */
    public function testToData($input, $expected) {
        self::assertSame($expected, $this->getFieldObject()->toData($input));
    }

    abstract public function dataFromData();


    /**
     * @param string $data
     * @param mixed $expected
     * @covers ::fromData
     * @dataProvider dataFromData
     */
    public function testFromData($data, $expected) {
        self::assertSame($expected, $this->getFieldObject()->fromData($data));
    }

}
