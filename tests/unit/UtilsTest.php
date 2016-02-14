<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace org\majkel\dbase;

use org\majkel\dbase\tests\utils\TestBase;

use stdClass;

/**
 * Utils class tests
 *
 * @author majkel
 *
 * @coversDefaultClass \org\majkel\dbase\Utils
 */
class UtilsTest extends TestBase {

    /**
     * @return array
     */
    public function dataGetType() {
        return array(
            array('', 'string'),
            array(0, 'integer'),
            array(0.0, 'double'),
            array(false, 'boolean'),
            array(null, 'NULL'),
            array(new stdClass(), 'stdClass'),
            array(new Record(), 'org\majkel\dbase\Record'),
            array(array(), 'array'),
        );
    }

    /**
     * @dataProvider dataGetType
     */
    public function testGetType($variable, $excepted) {
        self::assertSame($excepted, Utils::getType($variable));
    }

    /**
     * @return array
     */
    public function dataToArray() {
        return array(
            array(
                array(1, 'x' => 2, 3),
                array(1, 'x' => 2, 3),
            ),
            array(
                new Record(array('x' => 1, 'y' => 2)),
                           array('x' => 1, 'y' => 2),
            ),
            array(
                new \ArrayObject(array(2 => 'x', 3 => 'z')),
                                 array(2 => 'x', 3 => 'z'),
            ),
            array(
                new \ArrayIterator(array(3 => 'x', 6 => 'z')),
                                   array(3 => 'x', 6 => 'z'),
            ),
        );
    }

    /**
     * @dataProvider dataToArray
     */
    public function testToArray($data, $excepted) {
        self::assertSame($excepted, Utils::toArray($data));
    }

    /**
     * @return array
     */
    public function dataToArrayInvalid() {
        return array(
            array(false),
            array(true),
            array(1),
            array('some text'),
            array(new stdClass()),
        );
    }

    /**
     * @dataProvider dataToArrayInvalid
     * @expectedException \org\majkel\dbase\Exception
     */
    public function testToArrayInvalid($data) {
        Utils::toArray($data);
    }
}
