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
 * @coversDefaultClass \org\majkel\dbase\Flags
 */
class FlagsTest extends TestBase {

    const CLS = '\org\majkel\dbase\Flags';

    /**
     * @covers ::flagEnabled
     * @covers ::enableFlag
     */
    public function testEnableDisableFalg() {
        $flags = $this->getObjectForTrait(self::CLS);
        $flagsReflect = $this->reflect($flags);
        /* @var $flags \org\majkel\dbase\Flags */
        self::assertFalse($flagsReflect->flagEnabled(1));
        self::assertSame($flags, $flagsReflect->enableFlag(1, true));
        self::assertTrue($flagsReflect->flagEnabled(1));
        self::assertSame($flags, $flagsReflect->enableFlag(1, false));
        self::assertFalse($flagsReflect->flagEnabled(1));
    }

}
