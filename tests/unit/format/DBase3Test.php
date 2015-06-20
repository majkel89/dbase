<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace org\majkel\dbase\format;

use org\majkel\dbase\tests\utils\AbstractFormatTest;
use org\majkel\dbase\Field;

/**
 * Record class tests
 *
 * @author majkel
 *
 * @coversDefaultClass \org\majkel\dbase\format\DBase3
 */
class DBase3Test extends AbstractFormatTest {

    const CLS = '\org\majkel\dbase\format\DBase3';

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
        return [
            Field::TYPE_CHARACTER, Field::TYPE_DATE, Field::TYPE_LOGICAL,
            Field::TYPE_MEMO, Field::TYPE_NUMERIC
        ];
    }

    /**
     * @covers ::createHeader
     */
    public function testCreateHeader() {
        $format = $this->getFormatObject();
        $header = $this->reflect($format)->createHeader($this->getHeaderData([
            'v' => 3,
        ]));
        self::assertTrue($header->isValid());
    }

    /**
     * @covers ::createHeader
     */
    public function testCreateHeaderUnknownFormat() {
        $format = $this->getFormatObject();
        $header = $this->reflect($format)->createHeader($this->getHeaderData([
            'v' => 666,
        ]));
        self::assertTrue($header->isValid());
    }

    /**
     * @covers ::getName
     */
    public function testGetName() {
        self::assertSame('dBASE III PLUS', $this->getFormatObject()->getName());
    }

}
