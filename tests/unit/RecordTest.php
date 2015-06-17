<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace org\majkel\dbase\tests\integration;

use org\majkel\dbase\tests\utils\TestBase;
use org\majkel\dbase\Record;
use ArrayObject;

/**
 * Record class tests
 *
 * @author majkel
 *
 * @coversDefaultClass \org\majkel\dbase\Record
 */
class RecordTest extends TestBase {

    /**
     * @covers ::__construct
     */
    public function testConstruct() {
        $record = new Record();
        $flags = $record->getFlags();
        self::assertFalse($record->isDeleted());
        self::assertTrue(($flags & ArrayObject::ARRAY_AS_PROPS) > 0);
        self::assertTrue(($flags & ArrayObject::STD_PROP_LIST) > 0);
    }

    /**
     * @covers ::setDeleted
     * @covers ::isDeleted
     */
    public function testSetDeleted() {
        $record = new Record();
        $record->setDeleted(true);
        self::assertTrue($record->isDeleted());
        $record->setDeleted(false);
        self::assertFalse($record->isDeleted());
    }

    /**
     * @covers ::toArray
     */
    public function testToArray() {
        $record = new Record();
        $record->a = 1;
        $record['b'] = 2;
        self::assertSame([
            'a' => 1,
            'b' => 2,
        ], $record->toArray());
    }
}
