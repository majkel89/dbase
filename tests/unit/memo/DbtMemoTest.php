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
 * @coversDefaultClass \org\majkel\dbase\memo\DbtMemo
 */
class DbtMemoTest extends TestBase {

    const CLS = '\org\majkel\dbase\memo\DbtMemo';

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
                ->getSize(1024)
                ->new();
        $mock = $this->mock(self::CLS)
                ->getFile(array(), $mockedFile, self::once())
                ->new();
        $mock->getEntry($entryId);
    }

    /**
     * @return array
     */
    public function dataGetEntry() {
        return array(
            array(0.5),
            array(1),
            array('    2'),
            array('     '),
        );
    }

    /**
     * @test
     * @covers ::getEntry
     * @covers ::gotoEntry
     * @dataProvider dataGetEntry
     */
    public function testGetEntry($entryId) {
        $mockedFile = $this->getFileMock()
                ->getSize(2048)
                ->fseek(array(1024), null, self::once())
                ->fread(array(512), 'DATA', self::once())
                ->new();
        $mock = $this->mock(self::CLS)
                ->getFile($mockedFile)
                ->new();
        self::assertSame('DATA', $mock->getEntry($entryId));
    }

    /**
     * @covers ::setEntry
     * @covers ::gotoEntry
     */
    public function testSetEntry() {
        $file = $this->getMockBuilder(self::CLS_SPLFILEOBJECT)
            ->setMethods(array('fseek', 'fwrite', 'getSize'))
            ->disableOriginalConstructor()
            ->getMock();
        $file->expects(self::any())->method('getSize')->willReturn(2048);
        $file->expects(self::once())->method('fseek')->with(3 * 512);
        $file->expects(self::once())->method('fwrite')->with(pack('a3@512', '123'));

        $mock = $this->mock(self::CLS)
            ->getFile($file)
            ->new();

        self::assertSame(3, $mock->setEntry(3, '123'));
    }

    /**
     * @covers ::setEntry
     * @covers ::gotoEntry
     */
    public function testSetEntryNew() {
        $file = $this->getMockBuilder(self::CLS_SPLFILEOBJECT)
            ->setMethods(array('fseek', 'fwrite', 'getSize'))
            ->disableOriginalConstructor()
            ->getMock();
        $file->expects(self::any())->method('getSize')->willReturn(2048);
        $file->expects(self::once())->method('fseek')->with(0, SEEK_END);
        $file->expects(self::once())->method('fwrite')->with(pack('a3@512', '123'));

        $mock = $this->mock(self::CLS)
            ->getFile($file)
            ->new();

        self::assertSame(4, $mock->setEntry(null, '123'));
    }

    /**
     * @covers ::setEntry
     * @covers ::gotoEntry
     * @expectedException \org\majkel\dbase\Exception
     * @expectedExceptionMessage Unable to move to block `55`
     */
    public function testSetEntryInvalid() {
        $file = $this->getMockBuilder(self::CLS_SPLFILEOBJECT)
            ->setMethods(array('getSize'))
            ->disableOriginalConstructor()
            ->getMock();
        $file->expects(self::any())->method('getSize')->willReturn(2048);

        $mock = $this->mock(self::CLS)
            ->getFile($file)
            ->new();

        self::assertSame(3, $mock->setEntry(55, '123'));
    }
}
