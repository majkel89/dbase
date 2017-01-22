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
 * FoxPro format tests
 *
 * @author majkel
 *
 * @coversDefaultClass \org\majkel\dbase\format\FoxPro
 */
class FoxProTest extends AbstractFormatTest {

    const CLS = '\org\majkel\dbase\format\FoxPro';

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
            Field::TYPE_MEMO, Field::TYPE_NUMERIC
        );
    }

    /**
     * @covers ::createHeader
     */
    public function testCreateHeader() {
        $format = $this->getFormatObject();
        $header = $this->reflect($format)->createHeader($this->getHeaderData(array(
            'v' => 30,
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
        self::assertSame('FoxPro', $this->getFormatObject()->getName());
    }

    /**
     * @covers ::getType
     */
    public function testGetType() {
        self::assertSame(Format::FOXPRO, $this->getFormatObject()->getType());
    }

}
