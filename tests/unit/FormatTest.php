<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace org\majkel\dbase;

use org\majkel\dbase\tests\utils\TestBase;

use stdClass;
use SplFileInfo;
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
                'getMemoFile', 'getFileInfo');
    }

    /**
     * @covers ::getRecord
     */
    public function testGetRecord() {
        $format = $this->getFormatMock()
            ->getRecords(['INDEX', 1], [2 => 'RECORD'], self::once())
            ->new();
        self::assertSame('RECORD', $format->getRecord('INDEX'));
    }

    /**
     * @return array
     */
    public function dataGetReadBoudries() {
        return [
            [ 0,  1, 10, 0, 1],
            [-1,  1, 10, 0, 1],
            [ 5, 20, 10, 5, 10],
        ];
    }

    /**
     * @covers ::getReadBoudries
     * @dataProvider dataGetReadBoudries
     */
    public function testGetReadBoudries($index, $length, $records, $expectedStart, $expectedStop) {
        $header = $this->getHeaderMock()
            ->getRecordsCount([], $records, self::once())
            ->new();
        $format = $this->getFormatMock()
            ->getHeader($header)
            ->new();
        list($start, $stop) = $this->reflect($format)->getReadBoudries($index, $length);
        self::assertSame($expectedStart, $start);
        self::assertSame($expectedStop, $stop);
    }

    /**
     * @return array
     */
    public function dataGetReadBoudriesException() {
        return [
            [0, 1, 0],
            [1, 1, 1],
        ];
    }

    /**
     * @covers ::getReadBoudries
     * @expectedException \org\majkel\dbase\Exception
     * @dataProvider dataGetReadBoudriesException
     */
    public function testGetReadBoudriesException($index, $length, $records) {
        $header = $this->getHeaderMock()
            ->getRecordsCount([], $records, self::once())
            ->new();
        $format = $this->getFormatMock()
            ->getHeader($header)
            ->new();
        $this->reflect($format)->getReadBoudries($index, $length);
    }

    /**
     * @covers ::getRecords
     */
    public function testGetRecords() {
        $file = $this->getFileMock()
            ->fseek([12 + 7 * 2], null, self::once())
            ->fread([2 * 7], "DATA1\0\0DATA2\0\0", self::once())
            ->new();
        $header = $this->getHeaderMock()
            ->getRecordSize([], 7, self::once())
            ->getHeaderSize([], 12, self::once())
            ->new();
        $format = $this->getFormatMock()
            ->getReadBoudries([2, 2], [2, 4], self::once())
            ->getFile([], $file, self::once())
            ->getRecordFormat([], 'a7x', self::once())
            ->getHeader($header)
            ->createRecord([self::anything()], 'R')
            ->new();
        self::assertSame([2 => 'R', 3 => 'R'], $format->getRecords(2, 2));
    }

    /**
     * @return array
     */
    public function dataGetMemoFilePath() {
        return [
            ['some/file.txt', 'some/file.txt.dbt'],
            ['some/file', 'some/file.dbt'],
            ['some/file.dbf', 'some/file.dbt'],
            ['some/file.dBf', 'some/file.dbt'],
            ['some/file.DBF', 'some/file.dbt'],
        ];
    }

    /**
     * @covers ::getMemoFilePath
     * @dataProvider dataGetMemoFilePath
     */
    public function testGetMemoFilePath($dbfPath, $memoPath) {
        $fileInfo = new SplFileInfo($dbfPath);
        $format = $this->getFormatMock()
            ->getFileInfo([], $fileInfo, self::once())
            ->new();
        self::assertSame($memoPath, $this->reflect($format)->getMemoFilePath());
    }

    /**
     * @covers ::getMemoFile
     */
    public function testGetMemoFile() {
        $format = $this->getFormatMock()
            ->getMemoFilePath([], __FILE__, self::once())
            ->getMode([], 'rb', self::once())
            ->new();
        $file = $this->reflect($format)->getMemoFile();
        self::assertSame(__FILE__, $file->getPathname());
    }

    /**
     * @covers ::createHeader
     */
    public function testCreateHeader() {
        $date = new DateTime;
        $format = $this->getFormatMock()
            ->getLastDate([1, 2, 3], $date, self::once())
            ->new();
        $header = $this->reflect($format)->createHeader([
            'v' => 7, 'n' => 5, 'rs' => 123,
            'd1' => 1, 'd2' => 2, 'd3' => 3,
            'hs' => 321, 't' => true,
        ]);
        /* @var $header \org\majkel\dbase\Header */
        self::assertSame(7, $header->getVersion());
        self::assertSame($date, $header->getLastUpdate());
        self::assertSame(5, $header->getRecordsCount());
        self::assertSame(123, $header->getRecordSize());
        self::assertSame(321, $header->getHeaderSize());
        self::assertTrue($header->isPendingTransaction());
        self::assertTrue($header->isValid());
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
     */
    public function testCreateField() {
        $format = $this->mock(self::CLS_FORMAT)
            ->supportsType([Field::TYPE_CHARACTER], true, self::once())
            ->new();
        $field = $this->reflect($format)->createField([
            't' => Field::TYPE_CHARACTER,
            'n' => 'NaMe',
            'll' => 123,
        ]);
        self::assertTrue($field instanceof Field);
        self::assertSame('NaMe', $field->getName());
        self::assertSame(123, $field->getLength());
    }

    /**
     * @covers ::createField
     * @expectedException \org\majkel\dbase\Exception
     * @expectedExceptionMessage Format `FoRmAt` does not support field `C`
     */
    public function testCreateFieldUnsupported() {
        $format = $this->mock(self::CLS_FORMAT)
            ->supportsType([Field::TYPE_CHARACTER], false, self::once())
            ->getName('FoRmAt', self::once())
            ->new();
        $this->reflect($format)->createField([
            't' => Field::TYPE_CHARACTER,
        ]);
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
            ->getFields([], [$f1, $f2], self::once())
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
            ->fseek([1024], self::once())
            ->fread([512], 'DAT', self::once())
            ->new();
        $format = $this->getFormatMock()
            ->getMemoFile($memo)
            ->new();
        self::assertSame("DAT", $this->reflect($format)
            ->readMemoEntry(2));
    }

    /**
     * @covers ::getSupportedFormats
     */
    public function testGetSupportedFormats() {
        $reflection = new ReflectionClass(self::CLS_FORMAT);
        $types = [];
        $excludes = ['AUTO', 'FIELD', 'HEADER'];
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
            ->getFields([], [$f1, $f2], self::once())
            ->new();
        $format = $this->getFormatMock()
            ->getHeader($header)
            ->readMemoEntry(['123'], 'field1')
            ->new();
        $record = $this->reflect($format)->createRecord([
            'd' => 1,
            'f0' => '123',
            'f1' => 'field2',
        ]);
        self::assertTrue($record instanceof Record);
        self::assertTrue($record->isDeleted());
        self::assertSame(['FF1' => 'field1'], $record->toArray());
    }

    /**
     * @return string
     */
    protected function getValidHeader() {
        return "\x03s\x06\x15\x03\x00\x00\x00a\x00\x15\x00\x00\x00\x00\x00\x00"
             . "\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00";
    }

    /**
     * @covers ::readHeader
     */
    public function testReadHeaderInvalid() {
        $file = $this->getFileMock()
            ->fseek([0], self::once())
            ->fread([32], $this->getValidHeader(), self::once()) // header
            ->getSize(50)
            ->new();
        $header = $this->getHeaderMock()
            ->getHeaderSize(97)
            ->getRecordsCount(2)
            ->getRecordSize(10)
            ->new();
        $format = $this->getFormatMock()
            ->getFile($file)
            ->createHeader([], $header, self::once())
            ->new();
        $headerRet = $this->reflect($format)->readHeader();
        self::assertTrue($header instanceof Header);
        self::assertSame($header, $headerRet);
        self::assertFalse($header->isValid());
        self::assertEmpty($header->getFields());
    }

    /**
     * @return string
     */
    protected function getValidFields() {
        return "F1\x00\x00\x00\x00\x00\x00\x00\x00\x00C\x00\x00\x00\x00\x0A"
             . "\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00"
             . "F2\x00xyz\x00\x00\x00\x00\x00C\x00\x00\x00\x00\x0A\x00\x00"
             . "\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00";
    }

    /**
     * @covers ::readHeader
     */
    public function testReadHeader() {
        $file = $this->getFileMock()
            ->fseek([0], self::once())
            ->fread([32], $this->getValidHeader(), self::at(1)) // header
            ->getSize(161)
            ->fread([64], $this->getValidFields(), self::at(3)) // fields
            ->new();
        $header = $this->getHeaderMock()
            ->getHeaderSize(97)
            ->getRecordsCount(2)
            ->getRecordSize(32)
            ->new();
        $format = $this->getFormatMock()
            ->getFile($file)
            ->createHeader([], $header, self::once())
            ->new();
        $headerRet = $this->reflect($format)->readHeader();
        self::assertTrue($header instanceof Header);
        self::assertSame($header, $headerRet);
        self::assertCount(2, $header);
    }

}
