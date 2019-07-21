<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace org\majkel\dbase\format;

use org\majkel\dbase\Format;
use org\majkel\dbase\tests\utils\AbstractFormatTest;
use org\majkel\dbase\Field;

/**
 * Record class tests
 *
 * @author majkel
 *
 * @coversDefaultClass \org\majkel\dbase\format\DBase3
 */
class DBase4Test extends AbstractFormatTest {

    const CLS = '\org\majkel\dbase\format\DBase4';

    /**
     * {@inheritdoc}
     */
    protected function getFormatObject() {
        return $this->mock(self::CLS)->new();
    }

    /**
     * {@inheritdoc}
     */
    protected function getSupportedTypes() {
        return array(
            Field::TYPE_CHARACTER, Field::TYPE_DATE, Field::TYPE_LOGICAL,
            Field::TYPE_MEMO, Field::TYPE_NUMERIC, Field::TYPE_FLOAT
        );
    }

    /**
     * @covers ::createHeader
     */
    public function testCreateHeader() {
        $format = $this->getFormatObject();
        $header = $this->reflect($format)->createHeader($this->getHeaderData(array(
            'v' => 4,
        )));
        self::assertTrue($header->isValid());
    }

    /**
     * @covers ::createHeader
     */
    public function testCreateHeaderUnknownFormat() {
        $format = $this->getFormatObject();
        $header = $this->reflect($format)->createHeader($this->getHeaderData(array(
            'v' => 666,
        )));
        self::assertTrue($header->isValid());
    }

    /**
     * @covers ::getName
     */
    public function testGetName() {
        self::assertSame('dBASE IV', $this->getFormatObject()->getName());
    }

    /**
     * @covers ::getType
     */
    public function testGetType() {
        self::assertSame(Format::DBASE4, $this->getFormatObject()->getType());
    }

}
