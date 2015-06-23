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
 * Record class tests
 *
 * @author majkel
 *
 * @coversDefaultClass \org\majkel\dbase\Table
 */
class TableTest extends TestBase {

    const CLS = '\org\majkel\dbase\Table';

    /**
     * @param string $methodName
     */
    protected function headerProxyTest($methodName) {
        $header = $this->getHeaderMock()
            ->$methodName('RESULT', self::once())
            ->new();
        $table = $this->mock(self::CLS)
            ->getHeader($header)
            ->new();
        self::assertSame('RESULT', $table->$methodName());
    }

    /**
     * @covers ::__construct
     * @covers ::getFormat
     */
    public function testConstruct() {
        $format = new stdClass;

        $formatFactory = $this->mock(self::CLS_FORMAT_FACTORY)
            ->getFormat(['FORMAT', 'FILE', 'MODE'], $format, self::once())
            ->new();

        $table = $this->mock(self::CLS)
            ->getFormatFactory($formatFactory)
            ->new();
        /* @var $table \org\majkel\dbase\Table */

        $table->__construct('FILE', 'MODE', 'FORMAT');

        self::assertNull($table->getColumns());
        self::assertSame(512, $table->getBufferSize());
        self::assertSame($format, $this->reflect($table)->getFormat());
        self::assertSame(0, $table->key());
    }

    /**
     * @return \org\majkel\dbase\Table
     */
    protected function getTableWithColumns($F1, $F2, $F3, $times) {
        $f1 = $this->getFieldMock()
            ->setLoad($F1, self::exactly($times))
            ->getName('F1')
            ->new();
        $f2 = $this->getFieldMock()
            ->setLoad($F2, self::exactly($times))
            ->getName('F2')
            ->new();
        $f3 = $this->getFieldMock()
            ->setLoad($F3, self::exactly($times))
            ->getName('F3')
            ->new();
        return $this->mock(self::CLS)
            ->getFields([$f1, $f2, $f3])
            ->new();
    }

    /**
     * @covers ::setColumns
     * @covers ::getColumns
     */
    public function testSetColumns() {
        $table = $this->getTableWithColumns(true, false, true, 1);
        $this->reflect($table)->buffer = [1, 2, 3];
        self::assertSame($table, $table->setColumns(['F1', 'F2', 'F3', 'F?']));
        self::assertEmpty($this->reflect($table)->buffer);
        self::assertSame(['F1', 'F2', 'F3', 'F?'], $table->getColumns());
    }

    /**
     * @return array
     */
    public function dataSetColumnsEmptyArguments() {
        return [
            [null], [false], ['INVALID'], [[]], [new \stdClass()]
        ];
    }

    /**
     * @covers ::setColumns
     * @covers ::getColumns
     * @dataProvider dataSetColumnsEmptyArguments
     */
    public function testSetColumnsEmptyArguments($columns) {
        $table = $this->getTableWithColumns(true, true, true, 2);
        $table->setColumns(['F1', 'F2', 'F3']);
        $this->reflect($table)->buffer = [1, 2];
        /* @var $table \org\majkel\dbase\Table */
        self::assertSame($table, $table->setColumns($columns));
        self::assertNull($table->getColumns());
        self::assertEmpty($this->reflect($table)->buffer);
    }

    /**
     * @covers ::isValid
     */
    public function testIsValid() {
        $this->headerProxyTest('isValid');
    }

    /**
     * @covers ::getHeader
     */
    public function testGetHeader() {
        $format = $this->getHeaderMock()
            ->getHeader('HEADER', self::once())
            ->new();
        $table = $this->mock(self::CLS)
            ->getFormat($format)
            ->new();
        $table->getHeader();
    }

    /**
     * @covers ::getVersion
     */
    public function testGetVersion() {
        $this->headerProxyTest('getVersion');
    }

    /**
     * @covers ::isPendingTransaction
     */
    public function testIsPendingTransaction() {
        $this->headerProxyTest('isPendingTransaction');
    }

    /**
     * @covers ::getLastUpdate
     */
    public function testGetLastUpdate() {
        $this->headerProxyTest('getLastUpdate');
    }

    /**
     * @covers ::getFields
     */
    public function testGetFields() {
        $this->headerProxyTest('getFields');
    }

    /**
     * @covers ::getFieldsNames
     */
    public function testGetFieldsNames() {
        $this->headerProxyTest('getFieldsNames');
    }

    /**
     * @covers ::getFieldsCount
     */
    public function testGetFieldsCount() {
        $this->headerProxyTest('getFieldsCount');
    }

    /**
     * @covers ::getRecordsCount
     */
    public function testGetRecordsCount() {
        $this->headerProxyTest('getRecordsCount');
    }

    /**
     * @covers ::getRecordSize
     */
    public function testGetRecordSize() {
        $this->headerProxyTest('getRecordSize');
    }

    /**
     * @covers ::getHeaderSize
     */
    public function testGetHeaderSize() {
        $this->headerProxyTest('getHeaderSize');
    }

    /**
     * @covers ::getField
     */
    public function testGetField() {
        $header = $this->getHeaderMock()
            ->getField(['COLUMN'], 'NAME', self::once())
            ->new();
        $table = $this->mock(self::CLS)
            ->getHeader($header)
            ->new();
        self::assertSame('NAME', $table->getField('COLUMN'));
    }

    /**
     * @covers ::setBufferSize
     * @covers ::getBufferSize
     */
    public function testSetBufferSizeRecords() {
        $table = $this->mock(self::CLS)->new();
        self::assertSame($table, $table->setBufferSize(123, Table::BUFFER_RECORDS));
        self::assertSame(123, $table->getBufferSize());
    }

    /**
     * @covers ::setBufferSize
     * @covers ::getBufferSize
     */
    public function testSetBufferSizeBytes() {
        $table = $this->mock(self::CLS)
            ->getRecordSize(10, self::once())
            ->new();
        self::assertSame($table, $table->setBufferSize(123, Table::BUFFER_BYTES));
        self::assertSame(13, $table->getBufferSize());
    }

    /**
     * @covers ::setBufferSize
     * @covers ::getBufferSize
     */
    public function testSetBufferSizeInvalid() {
        $table = $this->mock(self::CLS)->new();
        self::assertSame($table, $table->setBufferSize(-123, Table::BUFFER_RECORDS));
        self::assertSame(1, $table->getBufferSize());
    }

    /**
     * @covers ::getRecord
     * @expectedException \org\majkel\dbase\Exception
     * @expectedExceptionCode 2
     * @expectedExceptionMessage File is not opened
     */
    public function testGetRecordInvalidFile() {
        $table = $this->mock(self::CLS)
            ->isValid(false, self::once())
            ->new();
        $table->getRecord(123);
    }

    /**
     * @covers ::getRecord
     * @expectedException \org\majkel\dbase\Exception
     * @expectedExceptionCode 1
     * @expectedExceptionMessage Offset 123 does not exists
     */
    public function testGetRecordInvalidOffset() {
        $table = $this->mock(self::CLS)
            ->isValid(true, self::once())
            ->offsetExists([123], false, self::once())
            ->new();
        $table->getRecord(123);
    }

    /**
     * @covers ::getRecord
     */
    public function testGetRecord() {
        $format = $this->getFormatMock()
            ->getRecords([123, 321], [123 => 'VALUE'], self::once())
            ->new();
        $table = $this->mock(self::CLS)
            ->isValid(true, self::exactly(2))
            ->offsetExists(true, self::exactly(2))
            ->getBufferSize(321, self::once())
            ->getFormat($format, self::once())
            ->new();
        self::assertSame('VALUE', $table->getRecord(123));
        self::assertSame('VALUE', $table->getRecord(123));
    }

    /**
     * @covers ::current
     * @covers ::key
     * @covers ::next
     * @covers ::rewind
     * @covers ::valid
     */
    public function testTraversable() {
        $table = $this->mock(self::CLS)
            ->offsetExists([0], true, self::at(0))
            ->getRecord([0], 'R1', self::at(1))
            ->offsetExists([1], true, self::at(2))
            ->getRecord([1], 'R2', self::at(3))
            ->offsetExists([2], false, self::at(4))
            ->new();
        foreach ($table as $index => $value) {
            $results[$index] = $value;
        }
        foreach ($table as $index => $value) {
            $results[$index] = $value;
        }
        self::assertSame(['R1', 'R2'], $results);
    }

    /**
     * @covers ::count
     */
    public function testCount() {
        $table = $this->mock(self::CLS)
            ->getRecordsCount(333, self::once())
            ->new();
        self::assertSame(333, $table->count());
    }

    /**
     * @return array
     */
    public function dataOffsetExists() {
        return [
            [-1, false],
            [ 5, true],
            [15, false]
        ];
    }

    /**
     * @covers ::offsetExists
     * @dataProvider dataOffsetExists
     */
    public function testOffsetExists($index, $excepted) {
        $table = $this->mock(self::CLS)
            ->getRecordsCount(10)
            ->new();
        self::assertSame($excepted, $table->offsetExists($index));
    }

    /**
     * @covers ::offsetGet
     */
    public function testOffsetGet() {
        $table = $this->mock(self::CLS)
            ->getRecord([333], 'VALUE', self::exactly(2))
            ->new();
        self::assertSame('VALUE', $table->offsetGet(333));
        self::assertSame('VALUE', $table[333]);
    }

    /**
     * @covers ::offsetSet
     * @expectedException \org\majkel\dbase\Exception
     */
    public function testOffsetSet() {
        $table = $this->mock(self::CLS)->new();
        self::assertSame('VALUE', $table->offsetSet(333, 'VALUE'));
    }

    /**
     * @covers ::offsetUnset
     * @expectedException \org\majkel\dbase\Exception
     */
    public function testOffsetUnset() {
        $table = $this->mock(self::CLS)->new();
        self::assertSame('VALUE', $table->offsetUnset(333));
    }

}
