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
 * @coversDefaultClass \org\majkel\dbase\Header
 */
class HeaderTest extends TestBase {

    /**
     * @covers ::addField
     * @covers ::getFields
     * @covers \org\majkel\dbase\Field::setName
     */
    public function testAddField() {
        $fA = $this->getFieldStub()->setName('A');
        $fB = $this->getFieldStub()->setName('B');
        $header = $this->getHeaderStub();
        self::assertSame($header, $header->addField($fA));
        self::assertSame($header, $header->addField($fB));
        self::assertSame([$fA, $fB], $header->getFields());
    }

    /**
     * @covers ::getField
     * @covers ::addField
     * @covers ::offsetExists
     * @covers ::offsetGet
     * @uses \org\majkel\dbase\Field
     */
    public function testGetField() {
        $fA = $this->getFieldStub()->setName('A');
        $fB = $this->getFieldStub()->setName('B');
        $header = $this->getHeaderStub()
            ->addField($fA)
            ->addField($fB);
        self::assertSame($fA, $header->getField(0));
        self::assertSame($fB, $header->getField(1));
        self::assertSame($fA, $header->getField('A'));
        self::assertSame($fB, $header->getField('B'));
    }

    /**
     * @covers ::getField
     * @covers ::offsetExists
     * @expectedException \org\majkel\dbase\Exception
     * @expectedExceptionMessage Field `0` does not exists
     */
    public function testGetFieldDoesNotExists() {
        $this->getHeaderStub()->getField(0);
    }

    /**
     * @covers ::getFieldsNames
     * @covers ::addField
     * @covers ::getFields
     * @uses \org\majkel\dbase\Field
     */
    public function testGetFieldsNames() {
        $fA = $this->getFieldStub()->setName('A');
        $fB = $this->getFieldStub()->setName('B');
        $header = $this->getHeaderStub()
            ->addField($fA)
            ->addField($fB);
        self::assertSame(['A', 'B'], $header->getFieldsNames());
    }

    /**
     * @covers ::setVersion
     * @covers ::getVersion
     */
    public function testSetVersion() {
        $header = $this->getHeaderStub();
        self::assertSame($header, $header->setVersion('123'));
        self::assertSame(123, $header->getVersion());
    }

    /**
     * @covers ::setLastUpdate
     * @covers ::getLastUpdate
     */
    public function testSetLastUpdate() {
        $header = $this->getHeaderStub();
        $data = new \DateTime;
        self::assertSame($header, $header->setLastUpdate($data));
        self::assertSame($data, $header->getLastUpdate());
    }

    /**
     * @covers ::getFieldsCount
     * @covers ::addField
     * @covers ::getFields
     * @covers ::count
     */
    public function testGetFieldsCount() {
        $field = $this->getFieldStub();
        $header = $this->getHeaderStub()->addField($field)->addField($field);
        self::assertSame(2, $header->getFieldsCount());
        self::assertSame(2, $header->count());
        self::assertSame(2, count($header));
    }

    /**
     * @covers ::setPendingTransaction
     * @covers ::isPendingTransaction
     */
    public function testSetPendingTransaction() {
        $this->boolGetterSetterTest($this->getHeaderStub(),
                'isPendingTransaction', 'setPendingTransaction');
    }

    /**
     * @covers ::setRecordsCount
     * @covers ::getRecordsCount
     */
    public function testSetRecordsCount() {
        $header = $this->getHeaderStub();
        self::assertSame($header, $header->setRecordsCount('123'));
        self::assertSame(123, $header->getRecordsCount());
    }

    /**
     * @covers ::setRecordSize
     * @covers ::getRecordSize
     */
    public function testSetRecordSize() {
        $header = $this->getHeaderStub();
        self::assertSame($header, $header->setRecordSize('123'));
        self::assertSame(123, $header->getRecordSize());
    }

    /**
     * @covers ::setHeaderSize
     * @covers ::getHeaderSize
     */
    public function testSetHeaderSize() {
        $header = $this->getHeaderStub();
        self::assertSame($header, $header->setHeaderSize('123'));
        self::assertSame(123, $header->getHeaderSize());
    }

    /**
     * @covers ::setValid
     * @covers ::isValid
     */
    public function testSetValid() {
        $this->boolGetterSetterTest($this->getHeaderStub(),
                'isValid', 'setValid');
    }

    /**
     * @covers ::key
     * @covers ::current
     * @covers ::rewind
     * @covers ::next
     * @covers ::valid
     * @covers ::addField
     */
    public function testIterator() {
        $field = $this->getFieldStub();
        $header = $this->getHeaderStub()
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
     * @covers ::addField
     */
    public function testArrayAccess() {
        $fA = $this->getFieldStub();
        $fB = $this->getFieldStub();
        $fC = $this->getFieldStub();
        $header = $this->getHeaderStub()
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
        $header = $this->getHeaderStub();
        $header[] = new \stdClass();
    }
}
