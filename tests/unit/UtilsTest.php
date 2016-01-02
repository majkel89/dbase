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
        return [
            ['', 'string'],
            [0, 'integer'],
            [0.0, 'double'],
            [false, 'boolean'],
            [null, 'NULL'],
            [new stdClass(), 'stdClass'],
            [new Record(), 'org\majkel\dbase\Record'],
            [[], 'array'],
        ];
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
        return [
            [
                [1, 'x' => 2, 3],
                [1, 'x' => 2, 3],
            ],
            [
                new Record(['x' => 1, 'y' => 2]),
                           ['x' => 1, 'y' => 2],
            ],
            [
                new \ArrayObject([2 => 'x', 3 => 'z']),
                                 [2 => 'x', 3 => 'z'],
            ],
            [
                new \ArrayIterator([3 => 'x', 6 => 'z']),
                                   [3 => 'x', 6 => 'z'],
            ],
        ];
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
        return [
            [false],
            [true],
            [1],
            ['some text'],
            [new stdClass()],
        ];
    }

    /**
     * @dataProvider dataToArrayInvalid
     * @expectedException \org\majkel\dbase\Exception
     */
    public function testToArrayInvalid($data) {
        Utils::toArray($data);
    }
}
