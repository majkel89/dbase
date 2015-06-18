<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace org\majkel\dbase\filter;

use org\majkel\dbase\tests\utils\AbstractFilterTest;
use org\majkel\dbase\Field;

/**
 * Record class tests
 *
 * @author majkel
 *
 * @coversDefaultClass \org\majkel\dbase\filter\CharsetFilter
 */
class CharsetFilterTest extends AbstractFilterTest {

    /**
     * {@inheritdoc}
     */
    public function getFilterObject() {
        return new CharsetFilter('UTF-8', 'ISO-8859-1//TRANSLIT');
    }

    /**
     * {@inheritdoc}
     */
    public function dataToValue() {
        return [
            [' € ', ' EUR '],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function dataFromValue() {
        return [
            [' EUR ', ' EUR '],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getSupportedTypes() {
        return [
            Field::TYPE_CHARACTER,
            Field::TYPE_MEMO,
        ];
    }

    /**
     * @covers ::setInput
     * @covers ::getInput
     */
    public function testSetInput() {
        $filter = $this->getFilterObject();
        self::assertSame($filter, $filter->setInput('INPUT'));
        self::assertSame('INPUT', $filter->getInput());
        self::assertSame($filter, $filter->setInput(null));
        self::assertSame(iconv_get_encoding('input_encoding'), $filter->getInput());
    }

    /**
     * @covers ::setOutput
     * @covers ::getOutput
     * @covers ::__construct
     */
    public function testSetOutput() {
        $filter = $this->getFilterObject();
        self::assertSame($filter, $filter->setOutput('INPUT'));
        self::assertSame('INPUT', $filter->getOutput());
        self::assertSame($filter, $filter->setOutput(null));
        self::assertSame(iconv_get_encoding('output_encoding'), $filter->getOutput());
    }

}
