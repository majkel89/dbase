<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace org\majkel\dbase;

use org\majkel\dbase\tests\utils\TestBase;

/**
 * Memo factory tests
 *
 * @author majkel
 *
 * @coversDefaultClass \org\majkel\dbase\MemoFactory
 */
class MemoFactoryTest extends TestBase {

    /**
     * @test
     * @covers ::getFormats
     * @covers ::registerFormat
     * @covers ::unregisterFormat
     */
    public function testRegister() {
        $memoFactory = new MemoFactory();
        self::assertCount(0, $memoFactory->getFormats());
        $memoFactory->registerFormat('txt', '\stdClass');
        self::assertSame(array('txt' => '\stdClass'), $memoFactory->getFormats());
        $memoFactory->registerFormat('www', '\stdClass2');
        self::assertSame(array('txt' => '\stdClass', 'www' => '\stdClass2'), $memoFactory->getFormats());
        $memoFactory->unregisterFormat('txt');
        self::assertSame(array('www' => '\stdClass2'), $memoFactory->getFormats());
    }

    /**
     * @test
     * @covers ::setInstance
     * @covers ::getInstance
     */
    public function testSetInstance() {
        $instance = new MemoFactory();
        MemoFactory::setInstance($instance);
        self::assertSame($instance, MemoFactory::getInstance());
    }

    /**
     * @return array
     */
    public function dataGetKnownFormats() {
        return array(
            array('dbt', '\org\majkel\dbase\memo\DbtMemo'),
            array('fpt', '\org\majkel\dbase\memo\FptMemo'),
        );
    }

    /**
     * @test
     * @dataProvider dataGetKnownFormats
     * @covers ::getInstance
     * @covers ::initializeFormats
     * @covers ::getMemo
     *
     * @param string $ext
     * @param string $exceptedClass
     *
     * @throws \org\majkel\dbase\Exception
     */
    public function testGetKnownFormats($ext, $exceptedClass) {
        $factory = MemoFactory::getInstance();
        $memo = $factory->getMemo(__FILE__, Table::MODE_READ, $ext);
        self::assertInstanceOf($exceptedClass, $memo);
    }

    /**
     * @expectedException \org\majkel\dbase\Exception
     * @expectedExceptionMessage Unable to determine memo format
     */
    public function testGetMemoUnsupportedAndAutoExt() {
        $factory = new MemoFactory;
        $factory->getMemo(__FILE__, Table::MODE_READ);
    }


    /**
     * @return array
     */
    public function dataGetMemoPathForDbf() {
        return array(
            array('some/file.txt', 'some/file.txt.dbt', 'dbt'),
            array('some/file', 'some/file.dbt', 'dbt'),
            array('some/file.dbf', 'some/file.dbt', 'dbt'),
            array('some/file.dBf', 'some/file.dbt', 'dbt'),
            array('some/file.DBF', 'some/file.xxx', 'xxx'),
        );
    }

    /**
     * @covers ::getMemoPathForDbf
     * @dataProvider dataGetMemoPathForDbf
     *
     * @param string $dbfPath
     * @param string $memoPath
     * @param string $ext
     */
    public function testGetMemoFilePath($dbfPath, $memoPath, $ext) {
        $format = $this->getFormatMock()
            ->getFileInfo(new \SplFileInfo($dbfPath))
            ->new();
        $factory = new MemoFactory;
        self::assertSame($memoPath, $factory->getMemoPathForDbf($format, $ext));
    }

    /**
     * @test
     * @covers ::getMemoForDbf
     * @expectedException \org\majkel\dbase\Exception
     * @expectedExceptionMessage Unable to open memo file
     */
    public function testGetMemoNoMemo() {
        $format = $this->getFormatMock()
            ->getFileInfo(new \SplFileInfo(__FILE__))
            ->new();
        $factory = new MemoFactory;
        $factory->getMemoForDbf($format);
    }

    /**
     * @test
     * @covers ::getMemoForDbf
     */
    public function testGetMemoFileExists() {
        $format = $this->getFormatMock()
            ->getFileInfo(new \SplFileInfo(__FILE__))
            ->getMode('rx')
            ->new();
        $memo = new \stdClass();
        $memoFactory = $this->mock(self::CLS_MEMO_FACTORY)
            ->getFormats(array(), array('XX' => '\stdClass'))
            ->getMemoPathForDbf(array($format, 'XX'), __FILE__, self::once())
            ->getMemo(array(__FILE__, 'rx', 'XX'), $memo, self::once())
            ->new();
        self::assertSame($memo, $memoFactory->getMemoForDbf($format));
    }
}
