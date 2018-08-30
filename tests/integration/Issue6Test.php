<?php
/**
 * Created by PhpStorm.
 * User: Michał Kowalik <maf.michal@gmail.com>
 * Date: 22.01.17 22:10
 */

namespace org\majkel\dbase;

use org\majkel\dbase\tests\utils\TestBase;

/**
 * Class Issue6Test
 *
 * @author Michał Kowalik <maf.michal@gmail.com>
 *
 * @coversDefaultClass \org\majkel\dbase\format\FoxPro
 */
class Issue6Test extends TestBase
{
    /**
     * @test
     * @throws Exception
     * @throws \PHPUnit_Framework_AssertionFailedError
     */
    public function testLoadFoxProFile()
    {
        $table = Table::fromFile("tests/fixtures/issue-6-example-file.dbf");
        self::assertSame(3, $table->getRecordsCount());
        self::assertSame(8, $table->getFieldsCount());
        self::assertSame($table->getField('DFD')->getType(), Field::TYPE_CHARACTER);
        self::assertSame($table->getField('FDA')->getType(), Field::TYPE_CHARACTER);
        self::assertSame($table->getField('FDAS')->getType(), Field::TYPE_CHARACTER);
        self::assertSame($table->getField('HG')->getType(), Field::TYPE_CHARACTER);
        self::assertSame($table->getField('T43')->getType(), Field::TYPE_CHARACTER);
        self::assertSame($table->getField('LK')->getType(), Field::TYPE_CHARACTER);
        self::assertSame($table->getField('POI')->getType(), Field::TYPE_CHARACTER);
        self::assertSame($table->getField('OIU')->getType(), Field::TYPE_CHARACTER);
        foreach ($table as $row) {
            /** @var Record $row */
            self::assertNotEmpty($row->toArray());
        }
    }
}
