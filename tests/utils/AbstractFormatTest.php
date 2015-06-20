<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace org\majkel\dbase\tests\utils;

/**
 * Record class tests
 *
 * @author majkel
 */
abstract class AbstractFormatTest extends TestBase {

    /**
     * @return \org\majkel\dbase\Format
     */
    abstract protected function getFormatObject();

    /**
     * @return integer[]
     */
    abstract protected function getSupportedTypes();

    /**
     * @return array
     */
    public function dataSupportsType() {
        return $this->genSupportsTypeDataSet($this->getSupportedTypes());
    }

    /**
     * @param string $type
     * @param boolean $supports
     * @dataProvider dataSupportsType
     * @covers ::supportsType
     */
    public function testSupportsType($type, $supports) {
        $result = $this->getFormatObject()->supportsType($type);
        self::assertSame($supports, $result, "Invalid result for `$type`");
    }

    /**
     * @param array $data
     * @return array
     */
    protected function getHeaderData(array $data = []) {
        return array_merge([
            'v' => 0,
            'd1' => 0,
            'd2' => 0,
            'd3' => 0,
            'n' => 0,
            'rs' => 0,
            'hs' => 0,
            't' => 0,
        ], $data);
    }

}
