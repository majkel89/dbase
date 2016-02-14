<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace org\majkel\dbase;

use org\majkel\dbase\tests\utils\TestBase;
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
     * @covers ::isDeleted
     * @covers ::getFlags
     */
    public function testConstruct() {
        $record = new Record();
        $flags = $record->getFlags();
        self::assertFalse($record->isDeleted());
        self::assertTrue(($flags & ArrayObject::ARRAY_AS_PROPS) > 0);
        self::assertTrue(($flags & ArrayObject::STD_PROP_LIST) > 0);
    }

    /**
     * @covers ::__construct
     * @covers ::setDeleted
     * @covers ::isDeleted
     * @covers ::getFlagsField
     */
    public function testSetDeleted() {
        $this->boolGetterSetterTest(new Record(), 'isDeleted', 'setDeleted');
    }

    /**
     * @covers ::__construct
     * @covers ::toArray
     */
    public function testToArray() {
        $record = new Record();
        $record->a = 1;
        $record['b'] = 2;
        self::assertSame(array(
            'a' => 1,
            'b' => 2,
        ), $record->toArray());
    }

    /**
     * @covers ::getMemoEntryId
     * @covers ::setMemoEntryId
     */
    public function testMemoEntries() {
        $record = new Record();
        self::assertNull($record->getMemoEntryId('field'));
        $record->setMemoEntryId('field', 123);
        self::assertSame(123, $record->getMemoEntryId('field'));
    }
}
