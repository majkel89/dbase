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
 * @coversDefaultClass \org\majkel\dbase\FormatFactory
 */
class FormatFactoryTest extends TestBase {

    /**
     * @covers ::getFormat
     * @expectedException \org\majkel\dbase\Exception
     */
    public function testGetFormatUnknown() {
        $formatFactory = $this->getFormatFactoryMock()
            ->initializeFormats([], null, self::once())
            ->getFormats([], [], self::once())
            ->new();
        $formatFactory->getFormat('UNKNOWN', 'FILE', 'MODE');
    }

    /**
     * @covers ::getFormat
     * @expectedException \org\majkel\dbase\Exception
     */
    public function testGetFormatInvalidGenerator() {
        $formatFactory = $this->getFormatFactoryMock()
            ->initializeFormats([], null, self::once())
            ->getFormats([], ['FORMAT' => 'IMPL'], self::once())
            ->new();
        self::assertSame('IMPL', $formatFactory
            ->getFormat('FORMAT', 'FILE', 'MODE'));
    }

    /**
     * @covers ::getFormat
     * @expectedException \org\majkel\dbase\Exception
     */
    public function testGetFormatInvalidClass() {
        $formatFactory = $this->getFormatFactoryMock()
            ->initializeFormats([], null, self::once())
            ->getFormats([], ['FORMAT' => function () {
                return 'IMPL';
            }], self::once())
            ->new();
        self::assertSame('IMPL', $formatFactory
            ->getFormat('FORMAT', 'FILE', 'MODE'));
    }

    /**
     * @covers ::getFormat
     */
    public function testGetFormat() {
        $format = $this->getFormatStub();
        $formatFactory = $this->getFormatFactoryMock()
            ->initializeFormats([], null, self::once())
            ->getFormats([], ['FORMAT' => function ($filePath, $mode) use ($format) {
                if ($filePath != 'FILE' || $mode != 'rb') {
                    throw new \Exception("Generator invalid call $filePath, $mode");
                }
                return $format;
            }], self::once())
            ->new();
        self::assertSame($format, $formatFactory
            ->getFormat('FORMAT', 'FILE', 'MODE'));
    }

    /**
     * @covers ::getFormats
     */
    public function testGetFormats() {
        $formatFactory = $this->getFormatFactoryMock()
            ->initializeFormats([], null, self::once())
            ->new();
        $this->reflect($formatFactory)->formats = 'FORMATS';
        self::assertSame('FORMATS', $formatFactory->getFormats());
    }

    /**
     * @covers ::registerFormat
     * @covers ::unregisterFormat
     */
    public function testFormatRegisteration() {
        $formatFactory = $this->getFormatFactoryMock()
            ->initializeFormats([], null, self::once())
            ->new();
        $impl = function () {};
        self::assertSame($formatFactory, $formatFactory
            ->registerFormat('FORMAT', $impl));
        self::assertSame(['FORMAT' => $impl], $formatFactory->getFormats());
        self::assertSame($formatFactory, $formatFactory->unregisterFormat('FORMAT'));
        self::assertSame([], $formatFactory->getFormats());
    }

    public function dataGetMode() {
        return [
            [Table::MODE_READ, 'rb'],
            [Table::MODE_WRITE, 'rb+'],
            [Table::MODE_READWRITE, 'rb+'],
            ['UNKNOWN', 'rb'],
        ];
    }

    /**
     * @param string $mode
     * @param string $expected
     * @dataProvider dataGetMode
     */
    public function testGetMode($mode, $expected) {
        $formatFactory = $this->getFormatFactoryMock()->new();
        self::assertSame($expected, $this->reflect($formatFactory)
            ->getMode($mode));
    }

    /**
     * @covers ::initializeFormats
     */
    public function testInitializeFormats() {
        $formatFactory = $this->getFormatFactoryMock()->new();
        self::assertSame($formatFactory, $this->reflect($formatFactory)
            ->initializeFormats());
        self::assertSame([Format::DBASE3, Format::AUTO],
            array_keys($formatFactory->getFormats()));
    }

    /**
     * @covers ::initializeFormats
     */
    public function testGetFormatAuto() {
        $format = $this->getFormatMock()
            ->isValid(true)
            ->new();
        $formatFactory = $this->getFormatFactoryMock()->new();
        $formatFactory->registerFormat('VALID', function () use ($format) {
            return $format;
        });
        self::assertSame($format, $formatFactory
            ->getFormat(Format::AUTO, 'FILE', Table::MODE_READ));
    }

    /**
     * @covers ::initializeFormats
     * @expectedException \org\majkel\dbase\Exception
     * @expectedExceptionMessage Unable detect format for file `unknown_file`
     */
    public function testGetFormatAutoUnknown() {
        $format = $this->getFormatMock()
            ->isValid(false)
            ->new();
        $formatFactory = $this->getFormatFactoryMock()->new();
        $formatFactory->registerFormat('INVALID', function () use ($format) {
            return $format;
        });
        $formatFactory->registerFormat('UNKNOWN', function () {
                throw new \Exception;
            });
        $formatFactory->getFormat(Format::AUTO, 'unknown_file', Table::MODE_READ);
    }

}
