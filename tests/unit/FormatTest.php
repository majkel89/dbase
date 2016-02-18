<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace org\majkel\dbase;

use org\majkel\dbase\memo\MemoInterface;
use org\majkel\dbase\tests\utils\TestBase;

use stdClass;
use DateTime;
use ReflectionClass;

/**
 * Record class tests
 *
 * @author majkel
 *
 * @coversDefaultClass \org\majkel\dbase\Format
 */
class FormatTest extends TestBase {

    /**
     * @param string $methodName
     * @param string $throughClass
     * @param string $throughMethod
     * @param string $finalMethod
     */
    protected function getterProxyTest(
            $methodName,
            $throughClass,
            $throughMethod,
            $finalMethod = null
    ) {
        if (empty($finalMethod)) {
            $finalMethod = $methodName;
        }
        $throughObj = $this->mock($throughClass)
            ->$finalMethod('RESULT', self::once())
            ->new();
        $format = $this->getFormatMock()
            ->$throughMethod($throughObj)
            ->new();
        self::assertSame('RESULT', $format->$methodName());
    }

    /**
     * @covers ::__construct
     * @covers ::getFile
     * @covers ::getMode
     */
    public function testConstruct() {
        $format = $this->getFormatMock()
            ->new(__FILE__, 'r');
        $formatReflect = $this->reflect($format);
        self::assertSame('r', $formatReflect->getMode());
        $file = $formatReflect->getFile();
        self::assertTrue($file instanceof \SplFileObject);
        self::assertSame(__FILE__, $file->getPathname());
    }

    /**
     * @covers ::getHeader
     */
    public function testGetHeader() {
        $header = new stdClass();
        $format = $this->getFormatMock()
            ->readHeader($header, self::once())
            ->new();
        self::assertSame($header, $format->getHeader());
    }

    /**
     * @covers ::isValid
     */
    public function testIsValid() {
        $this->getterProxyTest('isValid', self::CLS_HEADER, 'getHeader');
    }

    /**
     * @covers ::getFileInfo
     */
    public function testGetFileInfo() {
        $this->getterProxyTest('getFileInfo', self::CLS_SPLFILEOBJECT, 'getFile');
    }

    /**
     * @covers ::getMemoFileInfo
     */
    public function testGetMemoFileInfo() {
        $this->getterProxyTest('getMemoFileInfo', self::CLS_SPLFILEOBJECT,
                'getMemo', 'getFileInfo');
    }

    /**
     * @covers ::getRecord
     */
    public function testGetRecord() {
        $format = $this->getFormatMock()
            ->getRecords(array('INDEX', 1), array(2 => 'RECORD'), self::once())
            ->new();
        self::assertSame('RECORD', $format->getRecord('INDEX'));
    }

    /**
     * @return array
     */
    public function dataGetReadBoundaries() {
        return array(
            array( 0,  1, 10, 0, 1),
            array(-1,  1, 10, 0, 1),
            array( 5, 20, 10, 5, 10),
        );
    }

    /**
     * @covers ::getReadBoundaries
     * @dataProvider dataGetReadBoundaries
     *
     * @param $index
     * @param $length
     * @param $records
     * @param $expectedStart
     * @param $expectedStop
     */
    public function testGetReadBoundaries($index, $length, $records, $expectedStart, $expectedStop) {
        $header = $this->getHeaderMock()
            ->getRecordsCount($records, self::once())
            ->new();
        $format = $this->getFormatMock()
            ->getHeader($header)
            ->new();
        list($start, $stop) = $this->reflect($format)->getReadBoundaries($index, $length);
        self::assertSame($expectedStart, $start);
        self::assertSame($expectedStop, $stop);
    }

    /**
     * @return array
     */
    public function dataGetReadBoundariesException() {
        return array(
            array(0, 1, 0),
            array(1, 1, 1),
        );
    }

    /**
     * @covers ::getReadBoundaries
     * @expectedException \org\majkel\dbase\Exception
     * @dataProvider dataGetReadBoundariesException
     *
     * @param $index
     * @param $length
     * @param $records
     */
    public function testGetReadBoundariesException($index, $length, $records) {
        $header = $this->getHeaderMock()
            ->getRecordsCount($records, self::once())
            ->new();
        $format = $this->getFormatMock()
            ->getHeader($header)
            ->new();
        $this->reflect($format)->getReadBoundaries($index, $length);
    }

    /**
     * @covers ::getRecords
     */
    public function testGetRecords() {
        $file = $this->getFileMock()
            ->fseek(array(12 + 7 * 2), null, self::once())
            ->fread(array(2 * 7), "DATA1\0\0DATA2\0\0", self::once())
            ->new();
        $header = $this->getHeaderMock()
            ->getRecordSize(7, self::once())
            ->getHeaderSize(12, self::once())
            ->new();
        $format = $this->getFormatMock()
            ->getReadBoundaries(array(2, 2), array(2, 4), self::once())
            ->getFile($file, self::once())
            ->getRecordFormat('a7x', self::once())
            ->getHeader($header)
            ->createRecord(array(self::anything()), 'R')
            ->new();
        self::assertSame(array(2 => 'R', 3 => 'R'), $format->getRecords(2, 2));
    }

    /**
     * @covers ::getMemo
     * @covers ::setMemo
     */
    public function testGetMemo() {
        $format = $this->getFormatMock()->new();
        $memo = $this->getMemoObject();
        $memoFactory = $this->mock(self::CLS_MEMO_FACTORY)
            ->getMemoForDbf(array($format), $memo, self::once())
            ->new();
        MemoFactory::setInstance($memoFactory);
        self::assertSame($memo, $format->getMemo());
    }

    /**
     * @covers ::getLastDate
     */
    public function testGetLastDate() {
        $format = $this->getFormatMock()->new();
        $date = $this->reflect($format)->getLastDate(115, 07, 06);
        self::assertTrue($date instanceof DateTime);
        self::assertSame('2015-07-06 00:00:00', $date->format('Y-m-d H:i:s'));
    }

    /**
     * @covers ::createField
     * @expectedException \org\majkel\dbase\Exception
     * @expectedExceptionMessage Format `FoRmAt` does not support field `C`
     */
    public function testCreateFieldUnsupported() {
        $format = $this->mock(self::CLS_FORMAT)
            ->supportsType(array(Field::TYPE_CHARACTER), false, self::once())
            ->getName('FoRmAt', self::once())
            ->getType()
            ->new();
        $this->reflect($format)->createField(array(
            't' => Field::TYPE_CHARACTER,
        ));
    }

    /**
     * @covers ::getRecordFormat
     */
    public function testGetRecordFormat() {
        $f1 = $this->getFieldMock()
            ->getLength(3, self::once())
            ->new();
        $f2 = $this->getFieldMock()
            ->getLength(5)
            ->new();
        $header = $this->getHeaderMock()
            ->getFields(array(), array($f1, $f2), self::once())
            ->new();
        $format = $this->getFormatMock()
            ->getHeader($header)
            ->new();
        self::assertSame("a1d/a3f0/a5f1", $this->reflect($format)
             ->getRecordFormat());
    }

    /**
     * @covers ::readMemoEntry
     */
    public function testReadMemoEntry() {
        $memo = $this->getHeaderMock()
            ->getEntry(array(2), 'DAT', self::once())
            ->new();
        $format = $this->getFormatMock()
            ->getMemo($memo)
            ->new();
        self::assertSame('DAT', $this->reflect($format)
            ->readMemoEntry(2));
    }

    /**
     * @covers ::getSupportedFormats
     */
    public function testGetSupportedFormats() {
        $reflection = new ReflectionClass(self::CLS_FORMAT);
        $types = array();
        $excludes = array('AUTO', 'FIELD_', 'HEADER_', 'NAME', 'RECORD_');
        foreach ($reflection->getConstants() as $typeName => $typeValue) {
            $found = false;
            foreach ($excludes as $exclude) {
                if (strpos($typeName, $exclude) === 0) {
                    $found = true;
                }
            }
            if (!$found) {
                $types[] = $typeValue;
            }
        }
        self::assertSame($types, Format::getSupportedFormats());
    }

    /**
     * @covers ::createRecord
     */
    public function testCreateRecord() {
        $f1 = $this->getFieldMock(Field::TYPE_MEMO)
            ->isMemoEntry(true)
            ->isLoad(true)
            ->getName('FF1')
            ->unserialize('field1')
            ->new();
        $f2 = $this->getFieldMock(Field::TYPE_CHARACTER)
            ->isMemoEntry(false)
            ->isLoad(false)
            ->getName('FF2')
            ->unserialize('field2')
            ->new();
        $header = $this->getHeaderMock()
            ->getFields(array(), array('FF1' => $f1, 'FF2' => $f2), self::once())
            ->new();
        $format = $this->getFormatMock()
            ->getHeader($header)
            ->readMemoEntry(array('123'), 'field1')
            ->new();
        $record = $this->reflect($format)->createRecord(array(
            'd' => 1,
            'fFF1' => '123',
            'fFF2' => 'field2',
        ));
        self::assertTrue($record instanceof Record);
        self::assertTrue($record->isDeleted());
        self::assertSame(array('FF1' => 'field1'), $record->toArray());
        self::assertSame(123, $record->getMemoEntryId('FF1'));
    }

    /**
     * @covers ::writeHeader
     * @covers ::getWriteHeaderFormat
     */
    public function testWriteHeader() {
        $header = new Header();
        $header->setVersion(0x03);
        $header->setRecordsCount(0x1020304);
        $header->setPendingTransaction(true);
        $header->setHeaderSize(0x20);
        $header->setRecordSize(0x61);
        $date = new \DateTime();

        $headerData =  "\x03"                                        // version
            . chr($date->format('Y') - 1900)
            . chr($date->format('m'))
            . chr($date->format('d'))                                // last update date
            . "\x04\x03\x02\x01"                                     // numer of records in the table
            . "\x20\x00"                                             // bytes in header
            . "\x61\x00"                                             // bytes in record
            . "\x01\x00\x00"                                         // reserved
            . "\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00" // reserved
            . "\x00\x00\x00\x00";                                    // reserved

        $file = $this->getMockBuilder(self::CLS_SPLFILEOBJECT)
            ->setMethods(array('fseek', 'fwrite'))
            ->disableOriginalConstructor()
            ->getMock();
        $file->expects(self::once())->method('fseek')->with(0);
        $file->expects(self::once())->method('fwrite')->with($headerData);

        $format = $this->getFormatMock()
            ->getFile($file)
            ->getHeader($header)
            ->new();

        $this->reflect($format)->writeHeader();

        self::assertSame(
            $date->format('Y-m-d'),
            $header->getLastUpdate()->format('Y-m-d')
        );
    }

    /**
     * @return string
     */
    protected function getValidHeader() {
        return "\x03"                                                // version
            . "\xFF\x01\x03"                                         // last update date
            . "\x04\x03\x02\x01"                                     // numer of records in the table
            . "\x61\x00"                                             // bytes in header
            . "\x20\x00"                                             // bytes in record
            . "\x00\x00\x00"                                         // reserved
            . "\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00" // reserved
            . "\x00\x00\x00\x00";                                    // reserved
    }

    /**
     * @covers ::readHeader
     */
    public function testReadHeaderInvalid() {
        $file = $this->getFileMock()
            ->fseek(array(0), self::once())
            ->fread(array(32), $this->getValidHeader(), self::once()) // header
            ->getSize(50)
            ->new();
        $format = $this->getFormatMock()
            ->getFile($file)
            ->new();
        $header = $this->reflect($format)->readHeader();
        self::assertTrue($header instanceof HeaderInterface);
        self::assertFalse($header->isValid());
        self::assertEmpty($header->getFields());
    }

    /**
     * @return string
     */
    protected function getValidFields() {
        return ''
            //field 0
            . "F1\x00\x00\x00\x00\x00\x00\x00\x00\x00" // field name
            . "C" // field type
            . "\x00\x00\x00\x00" // Field data address
            . "\x0A" // Field length in binary.
            . "\x00" // Field decimal count in binary.
            . "\x00\x00" // Reserved for dBASE III PLUS on a LAN.
            . "\x00" // Work area ID.
            . "\x00\x00" // Reserved for dBASE III PLUS on a LAN.
            . "\x00" // SET FIELDS flag.
            . "\x00\x00\x00\x00\x00\x00\x00\x00" // Reserved bytes.
            //field 1
            . "F2\x00xyz\x00\x00\x00\x00\x00" // field name
            . "C" // field type
            . "\x00\x00\x00\x00" // Field data address
            . "\x0A" // Field length in binary.
            . "\x00" // Field decimal count in binary.
            . "\x00\x00"  // Reserved for dBASE III PLUS on a LAN.
            . "\x00" // Work area ID.c
            . "\x00\x00" // Reserved for dBASE III PLUS on a LAN.
            . "\x00" // SET FIELDS flag.
            . "\x00\x00\x00\x00\x00\x00\x00\x00"; // Reserved bytes.
    }

    /**
     * @covers ::readHeader
     * @covers ::createHeader
     * @covers ::createField
     */
    public function testReadHeader() {
        $file = $this->getFileMock()
            ->fseek(array(0), self::once())
            ->fread(array(32), $this->getValidHeader(), self::at(1)) // header
            ->getSize(32 * 3 + 1 + 0x1020304 * 32)
            ->fread(array(64), $this->getValidFields(), self::at(3)) // fields
            ->new();
        $format = $this->getFormatMock()
            ->getFile($file)
            ->new();
        $header = $this->reflect($format)->readHeader();
        /* @var $header \org\majkel\dbase\HeaderInterface */
        self::assertTrue($header instanceof HeaderInterface);
        self::assertTrue($header->isValid());
        self::assertSame(2, $header->getFieldsCount());
        self::assertSame(0x1020304, $header->getRecordsCount());
        self::assertSame(32, $header->getRecordSize());
        self::assertSame(0x0A, $header->getField('F1')->getLength());
        self::assertSame(0x0A, $header->getField('F2')->getLength());
        self::assertSame('2155-01-03', $header->getLastUpdate()->format('Y-m-d'));
        self::assertTrue($header->isFieldsLocked());
    }

    /**
     * @covers ::isTransaction
     */
    public function testsTransaction() {
        $file = $this->getFileMock()
            ->flock(array(LOCK_SH), true, self::at(0))
            ->flock(array(LOCK_UN), true, self::at(1))
            ->new();

        $format = $this->getFormatMock()
            ->getFile($file)
            ->checkIfTransaction(array(), true, self::once())
            ->new();

        self::assertTrue($format->isTransaction());
    }

    /**
     * @covers ::isTransaction
     * @expectedException \Exception
     * @expectedExceptionMessage FAILED
     */
    public function testsTransactionException() {
        $file = $this->getFileMock()
            ->flock(array(LOCK_SH), true, self::at(0))
            ->flock(array(LOCK_UN), true, self::at(1))
            ->new();

        $format = $this->getFormatMock()
            ->getFile($file)
            ->checkIfTransaction(self::throwException(new \Exception('FAILED')))
            ->new();

        $format->isTransaction();
    }

    /**
     * @covers ::isTransaction
     */
    public function testIsTransactionLockFail() {
        $file = $this->getFileMock()
            ->flock(array(LOCK_SH), false, self::once())
            ->new();

        $format = $this->getFormatMock()
            ->getFile($file)
            ->new();

        self::assertTrue($format->isTransaction());
    }

    /**
     * @covers ::checkIfTransaction
     */
    public function testCheckIfTransaction() {

        $newHeader = new Header();
        $newHeader->setPendingTransaction(true);
        $newHeader->setLastUpdate(new \DateTime());
        $newHeader->setRecordsCount(12);

        $oldHeader = new Header();
        $oldHeader->setPendingTransaction(false);
        $oldHeader->setLastUpdate(new DateTime('2016-01-01'));
        $oldHeader->setRecordsCount(10);

        $format = $this->getFormatMock()
            ->readHeader($newHeader)
            ->getHeader($oldHeader)
            ->new();

        self::assertTrue($this->reflect($format)->checkIfTransaction());
        self::assertSame($newHeader->isPendingTransaction(), $oldHeader->isPendingTransaction());
        self::assertSame($newHeader->getLastUpdate(), $oldHeader->getLastUpdate());
        self::assertSame($newHeader->getRecordsCount(), $oldHeader->getRecordsCount());
    }

    /**
     * @covers ::setTransactionStatus
     * @covers ::isTransaction
     */
    public function testSetTransactionStatus() {
        $header = new Header();
        $header->setPendingTransaction(false);

        $format = $this->getFormatMock()
            ->getHeader($header)
            ->writeHeader(array(), null, self::once())
            ->new();

        $this->reflect($format)->setTransactionStatus(true);

        self::assertTrue($format->isTransaction());
        self::assertTrue($header->isPendingTransaction());
    }

    /**
     * @covers ::beginTransaction
     */
    public function testBeginTransaction() {
        $file = $this->getFileMock()
            ->flock(array(LOCK_EX), true, self::at(0))
            ->flock(array(LOCK_UN), true, self::at(1))
            ->new();

        $format = $this->getFormatMock()
            ->getFile($file)
            ->checkIfTransaction(false)
            ->setTransactionStatus(array(true), null, self::once())
            ->new();

        $format->beginTransaction();
        self::assertTrue($format->isTransaction());
    }

    /**
     * @covers ::beginTransaction
     * @expectedException \org\majkel\dbase\Exception
     * @expectedExceptionMessage Transaction already started by somebody else
     */
    public function testBeginTransactionAlreadyInTransaction() {
        $file = $this->getFileMock()
            ->flock(array(LOCK_EX), true, self::at(0))
            ->flock(array(LOCK_UN), true, self::at(1))
            ->new();

        $format = $this->getFormatMock()
            ->getFile($file)
            ->checkIfTransaction(true)
            ->new();

        $format->beginTransaction();
    }

    /**
     * @covers ::beginTransaction
     * @expectedException \org\majkel\dbase\Exception
     * @expectedExceptionMessage Failed to acquire exclusive lock
     */
    public function testBeginTransactionLockFail() {
        $file = $this->getFileMock()
            ->flock(array(LOCK_EX), false, self::once())
            ->new();

        $format = $this->getFormatMock()
            ->getFile($file)
            ->new();

        $format->beginTransaction();
    }

    /**
     * @covers ::beginTransaction
     * @expectedException \org\majkel\dbase\Exception
     * @expectedExceptionMessage Transaction already started
     */
    public function testBeginTransactionCalledTwice() {
        $file = $this->getFileMock()
            ->flock(array(LOCK_EX), true, self::at(0))
            ->flock(array(LOCK_UN), true, self::at(1))
            ->new();

        $format = $this->getFormatMock()
            ->getHeader(new Header())
            ->getFile($file)
            ->checkIfTransaction(false)
            ->writeHeader(array(), null, self::once())
            ->new();

        $format->beginTransaction();
        $format->beginTransaction();
    }

    /**
     * @covers ::endTransaction
     */
    public function testEndTransaction() {
        $file = $this->getFileMock()
            ->flock(array(LOCK_EX), true, self::at(0))
            ->flock(array(LOCK_UN), true, self::at(1))
            ->new();

        $format = $this->getFormatMock()
            ->getFile($file)
            ->checkIfTransaction(false)
            ->setTransactionStatus(array(false), null, self::once())
            ->new();
        $this->reflect($format)->transaction = true;

        $format->endTransaction();
    }

    /**
     * @covers ::endTransaction
     * @expectedException \org\majkel\dbase\Exception
     * @expectedExceptionMessage Transaction haven't been started yet
     */
    public function testEndTransactionNotStarted() {
        $format = $this->getFormatMock()
            ->new();
        $format->endTransaction();
    }

    /**
     * @covers ::endTransaction
     * @expectedException \org\majkel\dbase\Exception
     * @expectedExceptionMessage Failed to acquire exclusive lock
     */
    public function testEndTransactionLockFailed() {
        $file = $this->getFileMock()
            ->flock(array(LOCK_EX), false, self::once())
            ->new();

        $format = $this->getFormatMock()
            ->getFile($file)
            ->new();
        $this->reflect($format)->transaction = true;

        $format->endTransaction();
    }


    /**
     * @covers ::endTransaction
     * @expectedException \org\majkel\dbase\Exception
     * @expectedExceptionMessage Transaction haven't been started yet
     */
    public function testEndTransactionNotStartedFile() {
        $file = $this->getFileMock()
            ->flock(array(LOCK_EX), true, self::at(0))
            ->flock(array(LOCK_UN), true, self::at(1))
            ->new();

        $format = $this->getFormatMock()
            ->getFile($file)
            ->checkIfTransaction(true)
            ->new();
        $this->reflect($format)->transaction = true;

        $format->endTransaction();
    }

    /**
     * @return array
     */
    public function dataMarkDeleted() {
        return array(
            array(true, "\x2A"),
            array(false, "\x20"),
        );
    }

    /**
     * @dataProvider dataMarkDeleted
     * @covers ::markDeleted
     *
     * @param $deleted
     * @param $char
     */
    public function testMarkDeleted($deleted, $char) {
        $header = new Header();
        $header->setRecordSize(10);
        $header->setHeaderSize(32);
        $header->setRecordsCount(3);

        $file = $this->getMockBuilder(self::CLS_SPLFILEOBJECT)
            ->setMethods(array('fseek', 'fwrite'))
            ->disableOriginalConstructor()
            ->getMock();
        $file->expects(self::once())->method('fseek')->with(32 + 2 * 10);
        $file->expects(self::once())->method('fwrite')->with($char);

        $format = $this->getFormatMock()
            ->getHeader($header)
            ->writeHeader(array(), self::once())
            ->getFile($file)
            ->new();

        $format->markDeleted(2, $deleted);
    }

    /**
     * @covers ::markDeleted
     */
    public function testMarkDeletedTransaction() {
        $format = $this->getFormatMock()
            ->getHeader(new Header())
            ->writeHeader(array(), self::never())
            ->getFile($this->getFileMock())
            ->getReadBoundaries(0)
            ->new();
        $this->reflect($format)->transaction = true;
        $format->markDeleted(2, true);
    }

    /**
     * @covers ::getWriteRecordFormat
     */
    public function testGetWriteRecordFormat() {
        $header = new Header();
        $header->addField(Field::create(Field::TYPE_LOGICAL)->setName('f1')->setLength(1));
        $header->addField(Field::create(Field::TYPE_CHARACTER)->setName('f2')->setLength(23));
        $header->addField(Field::create(Field::TYPE_MEMO)->setName('f3')->setLength(9));

        $format = $this->getFormatMock()
            ->getHeader($header)
            ->new();

        self::assertSame('aA1A23A9', $this->reflect($format)->getWriteRecordFormat());
    }

    /**
     * @covers ::serializeRecord
     */
    public function testSerializeRecord() {
        $header = new Header();
        $header->addField(Field::create(Field::TYPE_LOGICAL)
            ->setName('f1')->setLength(1));
        $header->addField(Field::create(Field::TYPE_CHARACTER)
            ->setName('f2')->setLength(3));
        $header->addField(Field::create(Field::TYPE_CHARACTER)
            ->setName('f3')->setLength(3));

        $format = $this->getFormatMock()
            ->getHeader($header)
            ->new();

        $record = new Record(array(
            'f1' => true,
            'f2' => 'ab',
            'f3' => 'x234',
        ));
        $record->setDeleted(true);

        $actual = $this->reflect($format)->serializeRecord($record);

        self::assertSame("*Tab x23", $actual);
    }

    /**
     * @covers ::serializeRecord
     */
    public function testSerializeRecordMemo() {
        $header = new Header();
        $header->addField(
            Field::create(Field::TYPE_MEMO)
                ->setName('f1')
                ->setLength(9)
        );

        $memoFile = $this->mock(self::CLS_MEMO)
            ->getFileInfo()
            ->getEntry()
            ->getType()
            ->getEntriesCount()
            ->setEntry(array(123, "Some text\x1A\x1A"), 124, self::once())
            ->new();

        $format = $this->getFormatMock()
            ->getMemo($memoFile)
            ->getHeader($header)
            ->new();

        $record = new Record();
        $record->f1 = 'Some text';
        $record->setMemoEntryId('f1', 123);

        $actual = $this->reflect($format)->serializeRecord($record);
        self::assertSame(' 124      ', $actual);
        self::assertSame(124, $record->getMemoEntryId('f1'));
    }

    /**
     * @covers ::insert
     * @covers ::getRecordOffset
     */
    public function testInsert() {
        $header = new Header();
        $header->setRecordsCount(3);
        $header->setHeaderSize(11);
        $header->setRecordSize(7);

        $record = new Record(array(
            'f1' => 'T',
            'f2' => '123',
            'f3' => 'ala ma kota'
        ));

        $file = $this->getMockBuilder(self::CLS_SPLFILEOBJECT)
            ->setMethods(array('fseek', 'fwrite'))
            ->disableOriginalConstructor()
            ->getMock();
        $file->expects(self::once())->method('fseek')->with(11 + 3 * 7);
        $file->expects(self::once())->method('fwrite')->with("<RECORD>\x1A");

        $format = $this->getFormatMock()
            ->getHeader($header)
            ->writeHeader(array(), self::once())
            ->serializeRecord(array($record), '<RECORD>', self::once())
            ->getFile($file)
            ->new();

        $newIndex = $format->insert($record);

        self::assertSame(3, $newIndex);
        self::assertSame(4, $header->getRecordsCount());
    }

    /**
     * @covers ::insert
     */
    public function testInsertTransaction() {
        $format = $this->getFormatMock()
            ->getHeader(new Header())
            ->writeHeader(array(), self::never())
            ->serializeRecord()
            ->getFile($this->getFileMock()->new())
            ->new();
        $this->reflect($format)->transaction = true;
        $format->insert(new Record());
    }

    /**
     * @covers ::update
     */
    public function testUpdate() {
        $header = new Header();
        $header->setRecordSize(12);
        $header->setHeaderSize(23);
        $header->setRecordsCount(3);

        $record = new Record(array(
            'f1' => 'T',
            'f2' => '123',
            'f3' => 'ala ma kota'
        ));

        $file = $this->getMockBuilder(self::CLS_SPLFILEOBJECT)
            ->setMethods(array('fseek', 'fwrite'))
            ->disableOriginalConstructor()
            ->getMock();
        $file->expects(self::once())->method('fseek')->with(23 + 2 * 12);
        $file->expects(self::once())->method('fwrite')->with('<RECORD>');

        $format = $this->getFormatMock()
            ->getHeader($header)
            ->writeHeader(array(), self::once())
            ->serializeRecord(array($record), '<RECORD>', self::once())
            ->getFile($file)
            ->new();

        $format->update(2, $record);
    }

    /**
     * @covers ::update
     */
    public function testUpdateTransaction() {
        $format = $this->getFormatMock()
            ->getHeader(new Header())
            ->writeHeader(array(), self::never())
            ->serializeRecord('<RECORD>')
            ->getFile($this->getFileMock())
            ->getReadBoundaries(0)
            ->new();
        $this->reflect($format)->transaction = true;
        $format->update(2, new Record(array(
            'f1' => 'T',
            'f2' => '123',
            'f3' => 'ala ma kota'
        )));
    }
}
