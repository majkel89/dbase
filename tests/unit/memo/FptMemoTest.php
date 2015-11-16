<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace org\majkel\dbase\memo;

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
     * @dataProvider dataGetEntryInvalidEntryId
     * @expectedException org\majkel\dbase\Exception
     */
    public function testGetEntryInvalidEntryId($entryId) {
        $mockedFile = $this->getFileMock()
                ->getSize(16)
                ->new();
        $mock = $this->mock(self::CLS)
                ->getFile([], $mockedFile)
                ->getBlockSize(8)
                ->new();
        $mock->getEntry($entryId);
    }

    /**
     * @return array
     */
    public function dataGetEntry() {
        return array(
            array(1.2, 'DATA'),
            array(2,   'DATA'),
        );
    }

    /**
     * @test
     * @covers ::getEntry
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
     * @test
     * @covers ::getEntry
     */
    public function testGetEntryZero() {
        $mockedFile = $this->getFileMock()
                ->getSize(2048)
                ->new();
        $mock = $this->mock(self::CLS)
                ->getFile($mockedFile)
                ->getBlockSize(4)
                ->new();
        self::assertSame('', $mock->getEntry(0));
    }

    /**
     * @test
     * @covers ::getEntry
     * @expectedException org\majkel\dbase\Exception
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
                ->getFile([], $mockedFile, self::once())
                ->new();
        self::assertSame(513, $this->reflect($mock)->getBlockSize());
    }
}
