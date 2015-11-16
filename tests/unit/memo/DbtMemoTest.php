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
     * @dataProvider dataGetEntryInvalidEntryId
     * @expectedException org\majkel\dbase\Exception
     */
    public function testGetEntryInvalidEntryId($entryId) {
        $mockedFile = $this->getFileMock()
                ->getSize(1024)
                ->new();
        $mock = $this->mock(self::CLS)
                ->getFile([], $mockedFile, self::once())
                ->new();
        $mock->getEntry($entryId);
    }

    /**
     * @test
     * @covers ::getEntry
     */
    public function testGetEntry() {
        $mockedFile = $this->getFileMock()
                ->getSize(2048)
                ->fseek([1024], null, self::once())
                ->fread([512], 'DATA', self::once())
                ->new();
        $mock = $this->mock(self::CLS)
                ->getFile([], $mockedFile, self::once())
                ->new();
        self::assertSame('DATA', $mock->getEntry(0.5));
    }

}
