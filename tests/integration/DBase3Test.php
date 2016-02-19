<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace org\majkel\dbase;

use org\majkel\dbase\tests\utils\TestBase;

/**
 * Integration tests of dBase III Format
 *
 * @author majkel
 */
class DBase3Test extends TestBase {

    /**
     * @medium
     * @coversNothing
     */
    public function testReadDbase3() {
        $dbf = Table::fromFile('tests/fixtures/dBase3.dbf');
        self::assertSame($dbf->getRecordsCount(), 6);
        $record = $dbf->getRecord(0);
        self::assertSame('4', $record->SL_CHPODPL);
        self::assertSame('Bezp', $record->CHP_ODPLAT);
        self::assertSame(22, $record->NUM);
        self::assertSame('2015-06-26', $record->DAT->format('Y-m-d'));
        self::assertSame(true, $record['LOGIC']);
        self::assertSame('memo1', $record->MEMO);
    }

    /**
     * @medium
     * @coversNothing
     */
    public function testCopyRecords() {
        $sourceFile = 'tests/fixtures/simple3.dbf';
        $destinationFile = 'tests/fixtures/simple3.dbf.copy';

        copy($sourceFile, $destinationFile);

        $source = Table::fromFile($sourceFile);
        $destination = Table::fromFile($destinationFile, Table::MODE_READWRITE);

        $destination->beginTransaction();

        foreach ($source as $sourceRecord) {
            $destination->insert($sourceRecord);
        }

        $destination->endTransaction();

        self::assertSame(2 * $source->getRecordsCount(), $destination->getRecordsCount());

        $destination = null;

        $destinationFile2 = 'tests/fixtures/simple3.dbf.2.copy';
        copy($destinationFile, $destinationFile2);
        $final = Table::fromFile($destinationFile2);
        $records = 0;
        foreach ($final as $record) {
            self::assertFalse(empty($record->F1));
            $records += 1;
        }
        self::assertSame(2 * $source->getRecordsCount(), $final->getRecordsCount());
        self::assertSame($final->getRecordsCount(), $records);
    }

    /**
     * @medium
     * @coversNothing
     */
    public function testReadLongFile() {
        $results = array();
        $dbf = Table::fromFile('tests/fixtures/producents.dbf');
        foreach ($dbf as $index => $record) {
            $results[$index] = $record->SL_PROD;
        }
        self::assertCount(7356, $results);
    }

}
