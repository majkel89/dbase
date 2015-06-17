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
 * @coversDefaultClass \org\majkel\dbase\Header
 */
class HeaderTest extends TestBase {

    /**
     * @covers ::addField
     * @covers ::getFields
     */
    public function testAddField() {
        $fA = $this->getField()->setName('A');
        $fB = $this->getField()->setName('B');
        $header = $this->getHeader();
        self::assertSame($header, $header->addField($fA));
        self::assertSame($header, $header->addField($fB));
        self::assertSame([$fA, $fB], $header->getFields());
    }

    /**
     * @covers ::getField
     */
    public function testGetField() {
        $fA = $this->getField()->setName('A');
        $fB = $this->getField()->setName('B');
        $header = $this->getHeader()
            ->addField($fA)
            ->addField($fB);
        self::assertSame($fA, $header->getField(0));
        self::assertSame($fB, $header->getField(1));
        self::assertSame($fA, $header->getField('A'));
        self::assertSame($fB, $header->getField('B'));
    }

    /**
     * @covers ::getField
     * @expectedException \org\majkel\dbase\Exception
     * @expectedExceptionMessage Field `0` does not exists
     */
    public function testGetFieldDoesNotExists() {
        $this->getHeader()->getField(0);
    }

    /**
     * @covers ::getFieldsNames
     */
    public function testGetFieldsNames() {
        $fA = $this->getField()->setName('A');
        $fB = $this->getField()->setName('B');
        $header = $this->getHeader()
            ->addField($fA)
            ->addField($fB);
        self::assertSame(['A', 'B'], $header->getFieldsNames());
    }

    /**
     * @covers ::setVersion
     * @covers ::getVersion
     */
    public function testSetVersion() {
        $header = $this->getHeader();
        self::assertSame($header, $header->setVersion('123'));
        self::assertSame(123, $header->getVersion());
    }

    /**
     * @covers ::setLastUpdate
     * @covers ::getLastUpdate
     */
    public function testSetLastUpdate() {
        $header = $this->getHeader();
        $data = new \DateTime;
        self::assertSame($header, $header->setLastUpdate($data));
        self::assertSame($data, $header->getLastUpdate());
    }

    /**
     * @covers ::getFieldsCount
     * @covers ::count
     */
    public function testGetFieldsCount() {
        $field = $this->getField();
        $header = $this->getHeader()->addField($field)->addField($field);
        self::assertSame(2, $header->getFieldsCount());
        self::assertSame(2, $header->count());
        self::assertSame(2, count($header));
    }

    /**
     * @covers ::setPendingTransaction
     * @covers ::isPendingTransaction
     */
    public function testSetPendingTransaction() {
        $header = $this->getHeader();
        self::assertFalse($header->isPendingTransaction());
        self::assertSame($header, $header->setPendingTransaction(true));
        self::assertTrue($header->isPendingTransaction());
        self::assertSame($header, $header->setPendingTransaction(false));
        self::assertFalse($header->isPendingTransaction());
    }

    /**
     * @covers ::setRecordsCount
     * @covers ::getRecordsCount
     */
    public function testSetRecordsCount() {
        $header = $this->getHeader();
        self::assertSame($header, $header->setRecordsCount('123'));
        self::assertSame(123, $header->getRecordsCount());
    }

    /**
     * @covers ::setRecordSize
     * @covers ::getRecordSize
     */
    public function testSetRecordSize() {
        $header = $this->getHeader();
        self::assertSame($header, $header->setRecordSize('123'));
        self::assertSame(123, $header->getRecordSize());
    }

    /**
     * @covers ::setHeaderSize
     * @covers ::getHeaderSize
     */
    public function testSetHeaderSize() {
        $header = $this->getHeader();
        self::assertSame($header, $header->setHeaderSize('123'));
        self::assertSame(123, $header->getHeaderSize());
    }

    /**
     * @covers ::setValid
     * @covers ::isValid
     */
    public function testSetValid() {
        $header = $this->getHeader();
        self::assertFalse($header->isValid());
        self::assertSame($header, $header->setValid(true));
        self::assertTrue($header->isValid());
        self::assertSame($header, $header->setValid(false));
        self::assertFalse($header->isValid());
    }

    /**
     * @covers ::key
     * @covers ::current
     * @covers ::rewind
     * @covers ::next
     * @covers ::valid
     */
    public function testIterator() {
        $field = $this->getField();
        $header = $this->getHeader()
            ->addField($field)
            ->addField($field);
        foreach ($header as $nextField) {
            self::assertSame($field, $nextField);
        }
    }

    /**
     * @covers ::offsetExists
     * @covers ::offsetGet
     * @covers ::offsetSet
     * @covers ::offsetUnset
     */
    public function testArrayAccess() {
        $fA = $this->getField();
        $fB = $this->getField();
        $fC = $this->getField();
        $header = $this->getHeader()
            ->addField($fA)
            ->addField($fB);
        self::assertSame($fA, $header[0]);
        self::assertSame($fB, $header[1]);
        self::assertTrue(isset($header[0]));
        $header[0] = $fC;
        self::assertSame($fC, $header[0]);
        unset($header[1]);
        self::assertFalse(isset($header[1]));
    }

    /**
     * @covers ::offsetSet
     * @expectedException \org\majkel\dbase\Exception
     * @expectedExceptionMessage Header can contain only Field elements
     */
    public function testOffsetSetInvalidType() {
        $header = $this->getHeader();
        $header[] = new \stdClass();
    }
}
