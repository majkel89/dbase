<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace org\majkel\dbase\tests\utils;

use PHPUnit_Framework_TestCase;
use Xpmock\TestCaseTrait;
use org\majkel\dbase\Field;

/**
 * Description of TestBase
 *
 * @author majkel
 */
class TestBase extends PHPUnit_Framework_TestCase {

    use TestCaseTrait;

    const CLS_FILTER = '\org\majkel\dbase\Filter';
    const CLS_FIELD = '\org\majkel\dbase\Field';
    const CLS_HEADER = '\org\majkel\dbase\Header';

    /**
     * @param boolean $supports
     * @return \org\majkel\dbase\Filter
     */
    protected function getFilterMock($supports = true) {
        return $this->mock(self::CLS_FILTER)
            ->supportsType($supports)
            ->new();
    }

    /**
     * @param string $type
     * @return \org\majkel\dbase\Field
     */
    protected function getFieldMock($type = Field::TYPE_CHARACTER) {
        return $this->mock(self::CLS_FIELD)
            ->fromData()
            ->toData()
            ->getType($type)
            ->new();
    }

    /**
     * @return \org\majkel\dbase\Header
     */
    protected function getHeaderMock() {
        return $this->mock(self::CLS_HEADER)
            ->new();
    }

    /**
     * @param \stdClass $obj
     * @param string $getter
     * @param string $setter
     * @param boolean $default
     */
    protected function boolGetterSetterTest($obj, $getter, $setter, $default = false) {
        self::assertSame($default, $obj->$getter());
        self::assertSame($obj, $obj->$setter(!$default));
        self::assertSame(!$default, $obj->$getter());
        self::assertSame($obj, $obj->$setter($default));
        self::assertSame($default, $obj->$getter());
    }

}
