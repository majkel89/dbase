<?php
/**
 * User: Michał (majkel) Kowalik <maf.michal@gmail.com>
 * Date: 14-Feb-16
 * Time: 16:51
 */

namespace org\majkel\dbase;

use org\majkel\dbase\tests\utils\TestBase;

/**
 * Class BuilderTest
 *
 * @package org\majkel\dbase
 * @author  Michał (majkel) Kowalik <maf.michal@gmail.com>
 */
class BuilderIntegrationTest extends TestBase {

    /**
     * @return array
     */
    public function dataDuplicateProducentBuilder() {
        return array(
            array('tests/fixtures/dBase3.copy.dbt.dbf', MemoFactory::TYPE_DBT),
            array('tests/fixtures/dBase3.copy.fpt.dbf', MemoFactory::TYPE_FPT),
        );
    }

    /**
     * @test
     * @medium
     * @param string $destinationPath
     * @param string $memoType
     * @dataProvider dataDuplicateProducentBuilder
     */
    public function testDuplicateProducentBuilder($destinationPath, $memoType) {
        $sourcePath = 'tests/fixtures/dBase3.dbf';
        $recordData = array(
            'SL_CHPODPL' => 'a',
            'CHP_ODPLAT' => 'xyz',
            'NUM' => 123,
            'DAT' => '2015-01-01',
            'LOGIC' => true,
            'MEMO' => 'some description of it',
        );

        $builder = Builder::fromFile($sourcePath);

        $table = $builder->setMemoType($memoType)
            ->build($destinationPath);
        for ($i = 0; $i < 3; $i++) {
            $table->insert($recordData);
        }
        $table = null;

        $table = Table::fromFile($destinationPath);
        self::assertTrue($table->isValid());
        self::assertSame(3, $table->getRecordsCount());
        foreach ($table as $record) {
            $data = $record->toArray();
            $data['DAT'] = $record->DAT->format('Y-m-d');
            self::assertSame($recordData, $data);
        }
    }

    /**
     * @test
     * @medium
     * @coversNothing
     */
    public function testConstructNewFile() {
        $filePath = 'tests/fixtures/build.copy.dbf';
        $recordData = array(
            'str' => 'some text',
            'bool' => true,
            'num' => 123
        );

        $table = Builder::create()
            ->setFormatType(Format::DBASE3)
            ->addField(Field::create(Field::TYPE_CHARACTER)->setName('str')->setLength(15))
            ->addField(Field::create(Field::TYPE_LOGICAL)->setName('bool'))
            ->addField(Field::create(Field::TYPE_NUMERIC)->setName('num'))
            ->build($filePath);
        $table->insert($recordData);
        $table->insert($recordData);
        $table->insert($recordData);
        $table = null;

        $table = Table::fromFile($filePath);
        self::assertTrue($table->isValid());
        self::assertSame(3, $table->getRecordsCount());
        self::assertSame($recordData, $table->getRecord(0)->toArray());
        self::assertSame($recordData, $table->getRecord(1)->toArray());
        self::assertSame($recordData, $table->getRecord(2)->toArray());
    }

}