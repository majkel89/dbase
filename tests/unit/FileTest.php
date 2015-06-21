<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace org\majkel\dbase;

use org\majkel\dbase\tests\utils\TestBase;

/**
 * Record class tests
 *
 * @author majkel
 *
 * @coversDefaultClass \org\majkel\dbase\File
 */
class FileTest extends TestBase {

    /**
     * @return array
     */
    public function dataFread() {
        return [
            [-1, false],
            [0,  false],
            [17, "F1\x00\x00\x00\x00\x00\x00\x00\x00\x00C\x00\x00\x00\x00\x0A"],
        ];
    }

    /**
     * @covers ::fread
     * @dataProvider dataFread
     */
    public function testFread($length, $expected) {
        $file = new File('tests/fixtures/simple3.dbf', 'r');
        $file->fseek(32);
        self::assertSame($expected, $file->fread($length));
    }

    /**
     * @covers ::getObject
     */
    public function testGetObjectHasFread() {
        $file = File::getObject(__FILE__, 'r', '\org\majkel\dbase\File');
        self::assertTrue($file instanceof File);
    }

    /**
     * @covers ::getObject
     */
    public function testGetObjectHasNoFread() {
        $file = File::getObject(__FILE__, 'r', '\stdClass');
        self::assertTrue($file instanceof File);
    }

}
