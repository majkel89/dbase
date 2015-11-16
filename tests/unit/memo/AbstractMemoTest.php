<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace org\majkel\dbase\memo;

use org\majkel\dbase\tests\utils\TestBase;

/**
 * Memo class tests
 *
 * @author majkel
 *
 * @coversDefaultClass \org\majkel\dbase\memo\AbstractMemo
 */
class AbstractMemoTest extends TestBase {

    const CLS = '\org\majkel\dbase\memo\AbstractMemo';

    /**
     * @covers ::__construct
     * @covers ::getFileInfo
     * @covers ::getFile
     */
    public function testGetFileInfo() {
        $memoFile = $this->mock(self::CLS)
                ->getEntry()
                ->new(__FILE__, 'r');
        self::assertSame(__FILE__, $memoFile->getFileInfo()->getPathname());
    }

}
