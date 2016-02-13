<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace org\majkel\dbase\memo;

use org\majkel\dbase\Exception;
use org\majkel\dbase\tests\utils\TestBase;

/**
 * Memo class tests
 *
 * @author majkel
 *
 * @coversDefaultClass \org\majkel\dbase\memo\FptMemo
 */
class FptMemoTest extends TestBase {

    const CLS = '\org\majkel\dbase\memo\FptMemo';

    /**
     * @return array
     */
    public function dataGetEntryInvalidEntryId() {
        return array(
            array(-2),        // negative index
            array('invalid'), // invalid index
            array(2),         // index too large
        );
    }

    /**
     * @test
     * @covers ::getEntry
     * @covers ::gotoEntry
     * @dataProvider dataGetEntryInvalidEntryId
     * @expectedException \org\majkel\dbase\Exception
     */
    public function testGetEntryInvalidEntryId($entryId) {
        $mockedFile = $this->getFileMock()
                ->getSize(16)
                ->new();
        $mock = $this->mock(self::CLS)
                ->getFile($mockedFile)
                ->getBlockSize(8)
                ->new();
        $mock->getEntry($entryId);
    }

    /**
     * @return array
     */
    public function dataGetEntry() {
        return array(
            array(1.2,    'DATA'),
            array(2,      'DATA'),
            array('   2', 'DATA'),
        );
    }

    /**
     * @test
     * @covers ::getEntry
     * @covers ::gotoEntry
     * @dataProvider dataGetEntry
     */
    public function testGetEntry($entryId, $expected) {
        $mockedFile = $this->getFileMock()
                ->getSize(2048)
                ->fread([8], "\x0\x0\x0\x0\x1\x2\x3\x4", self::at(1))
                ->fread([(1 << 24) + (2 << 16) + (3 << 8) + 4], "DATA", self::at(2))
                ->new();
        $mock = $this->mock(self::CLS)
                ->getFile($mockedFile)
                ->getBlockSize(4)
                ->new();
        self::assertSame($expected, $mock->getEntry($entryId));
    }

    /**
     * @return array
     */
    public function dataGetEntryZero() {
        return array(
            array(0),
            array('       '),
        );
    }

    /**
     * @test
     * @covers ::getEntry
     * @covers ::gotoEntry
     * @dataProvider dataGetEntryZero
     */
    public function testGetEntryZero($entryId) {
        $mockedFile = $this->getFileMock()
                ->getSize(2048)
                ->new();
        $mock = $this->mock(self::CLS)
                ->getFile($mockedFile)
                ->getBlockSize(4)
                ->new();
        self::assertSame('', $mock->getEntry($entryId));
    }

    /**
     * @test
     * @covers ::getEntry
     * @covers ::gotoEntry
     * @expectedException \org\majkel\dbase\Exception
     */
    public function testGetEntryInvalidRecordSize() {
        $mockedFile = $this->getFileMock()
                ->getSize(2048)
                ->fread("\x0\x0\x0\x0\xFF\xFF\xFF\xFF")
                ->new();
        $mock = $this->mock(self::CLS)
                ->getFile($mockedFile)
                ->getBlockSize(4)
                ->new();
        $mock->getEntry(77);
    }

    /**
     * @test
     * @covers ::getEntry
     * @covers ::gotoEntry
     */
    public function testGetEntryInvalidZeroSize() {
        $mockedFile = $this->getFileMock()
                ->getSize(2048)
                ->fread("\x0\x0\x0\x0\x0\x0\x0\x0")
                ->new();
        $mock = $this->mock(self::CLS)
                ->getFile($mockedFile)
                ->getBlockSize(4)
                ->new();
        self::assertSame('', $mock->getEntry(77));
    }

    /**
     * @test
     * @covers ::getBlockSize
     */
    public function testGetBlockSize() {
        $mockedFile = $this->getFileMock()
                ->fseek([6], null, self::once())
                ->fread([2], "\x2\x1", self::once())
                ->new();
        $mock = $this->mock(self::CLS)
                ->getFile($mockedFile)
                ->new();
        self::assertSame(513, $this->reflect($mock)->getBlockSize());
    }

    /**
     * Adds new entry
     * @covers ::setEntry
     * @covers ::getEntitiesCount
     * @covers ::lenPaddedBlockSize
     */
    public function testSetEntryNew() {
        $file = $this->getMockBuilder(self::CLS_SPLFILEOBJECT)
            ->setMethods(['fseek', 'getSize', 'fwrite'])
            ->disableOriginalConstructor()
            ->getMock();
        $file->expects(self::once())->method('fseek')->with(0, SEEK_END);
        $file->expects(self::any())->method('getSize')->willReturn(3 * 16);
        $file->expects(self::once())->method('fwrite')->with(
            "\x00\x00\x00\x01" . //type
            "\x00\x00\x00\x04" . //data length
            "data\x00\x00\x00\x00"
        );

        $memo = $this->mock(self::CLS)
            ->getFile($file)
            ->getBlockSize(16)
            ->new();

        self::assertSame(3, $memo->setEntry(null, 'data'));
    }

    /**
     * Tries to modify non existing entry
     * @covers ::setEntry
     * @expectedException Exception
     * @expectedExceptionMessage Unable to move to block `333`
     */
    public function testSetEntryInvalid() {
        $file = $this->getFileMock()
            ->getSize(3 * 16)
            ->new();

        $memo = $this->mock(self::CLS)
            ->getFile($file)
            ->getBlockSize(16)
            ->new();

        self::assertSame(3, $memo->setEntry(333, 'data'));
    }

    /**
     * Modifies existing entry that does not fit into existing block
     * @covers ::setEntry
     */
    public function testSetEntryOverlapping() {
        $file = $this->getMockBuilder(self::CLS_SPLFILEOBJECT)
            ->setMethods(['fseek', 'getSize', 'fwrite', 'fread'])
            ->disableOriginalConstructor()
            ->getMock();
        $file->expects(self::at(0))->method('getSize')->willReturn(3 * 16);
        $file->expects(self::at(1))->method('fseek')->with(16);
        $file->expects(self::at(2))->method('fread')->willReturn("\x00\x00\x00\x01\x00\x00\x00\x04");
        $file->expects(self::at(3))->method('getSize')->willReturn(3 * 16);
        $file->expects(self::at(4))->method('fseek')->with(0, SEEK_END);
        $file->expects(self::at(5))->method('fwrite')->with(
            "\x00\x00\x00\x01" . //type
            "\x00\x00\x00\x10" . //data length
            str_repeat('a', 16) .
            str_repeat("\x00", 8)
        );

        $memo = $this->mock(self::CLS)
            ->getFile($file)
            ->getBlockSize(16)
            ->new();

        self::assertSame(3, $memo->setEntry(1, str_repeat('a', 16)));
    }

    /**
     * Modifies last entry that does not fit into block size
     * @covers ::setEntry
     */
    public function testSetEntryOverlappingLast() {
        $file = $this->getMockBuilder(self::CLS_SPLFILEOBJECT)
            ->setMethods(['fseek', 'getSize', 'fwrite', 'fread'])
            ->disableOriginalConstructor()
            ->getMock();
        $file->expects(self::at(0))->method('getSize')->willReturn(3 * 16);
        $file->expects(self::at(1))->method('fseek')->with(32);
        $file->expects(self::at(2))->method('fread')->willReturn("\x00\x00\x00\x01\x00\x00\x00\x04");
        $file->expects(self::at(3))->method('getSize')->willReturn(3 * 16);
        $file->expects(self::at(4))->method('fseek')->with(-8, SEEK_CUR);
        $file->expects(self::at(5))->method('fwrite')->with(
            "\x00\x00\x00\x01" . //type
            "\x00\x00\x00\x10" . //data length
            str_repeat('a', 16) .
            str_repeat("\x00", 8)
        );

        $memo = $this->mock(self::CLS)
            ->getFile($file)
            ->getBlockSize(16)
            ->new();

        self::assertSame(2, $memo->setEntry(2, str_repeat('a', 16)));
    }

    /**
     * Modifies existing entry that fits into block size
     * @covers ::setEntry
     */
    public function testSetEntry() {
        $file = $this->getMockBuilder(self::CLS_SPLFILEOBJECT)
            ->setMethods(['fseek', 'getSize', 'fwrite', 'fread'])
            ->disableOriginalConstructor()
            ->getMock();
        $file->expects(self::at(0))->method('getSize')->willReturn(2 * 16);
        $file->expects(self::at(1))->method('fseek')->with(16);
        $file->expects(self::at(2))->method('fread')->willReturn("\x00\x00\x00\x01\x00\x00\x00\x04");
        $file->expects(self::at(3))->method('getSize')->willReturn(2 * 16);
        $file->expects(self::at(4))->method('fseek')->with(-8, SEEK_CUR);
        $file->expects(self::at(5))->method('fwrite')->with(
            "\x00\x00\x00\x01" . //type
            "\x00\x00\x00\x08" . //data length
            str_repeat('a', 8)
        );

        $memo = $this->mock(self::CLS)
            ->getFile($file)
            ->getBlockSize(16)
            ->new();

        self::assertSame(1, $memo->setEntry(1, str_repeat('a', 8)));
    }
}
