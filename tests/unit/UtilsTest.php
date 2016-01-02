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
}
