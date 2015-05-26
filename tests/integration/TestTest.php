<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace org\majkel\dbase\tests\integration;

use org\majkel\dbase\tests\utils\TestBase;
use org\majkel\dbase\DbfReader;

/**
 * Description of TestTest
 *
 * @author majkel
 */
class TestTest extends TestBase {

    public function testTest() {
        $dbf = new DbfReader();
        $dbf->open('tests/fixtures/TRANCHOR.DBF');
        var_export($dbf->getLastUpdate()->format('Y-m-d'));
        var_export($dbf->getFieldsNames());
        foreach ($dbf as $record) {
            /* @var $record org\majkel\dbase\Record */
            var_export($record->toArray());
        }

        //self::fail('Not yet implemented');
    }

}
