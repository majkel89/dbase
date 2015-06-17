<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace org\majkel\dbase\tests\integration;

use org\majkel\dbase\tests\utils\TestBase;

use org\majkel\dbase\Exception;

/**
 * Record class tests
 *
 * @author majkel
 *
 * @coversDefaultClass \org\majkel\dbase\Exception
 */
class ExceptionTest extends TestBase {

    /**
     * @covers ::raise
     * @expectedException org\majkel\dbase\Exception
     * @expectedExceptionCode 1
     * @expectedExceptionMessage Offset OFFSET does not exists
     */
    public function testRaise() {
        Exception::raise(Exception::INVALID_OFFSET, 'OFFSET');
    }

}
