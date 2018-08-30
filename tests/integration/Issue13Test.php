<?php
/**
 * Created by PhpStorm.
 * User: majkel
 * Date: 30.08.18
 * Time: 21:02
 */

namespace org\majkel\dbase\tests\integration;

use org\majkel\dbase\Record;
use org\majkel\dbase\Table;
use org\majkel\dbase\tests\utils\TestBase;

/**
 * @xcoversDefaultClass \org\majkel\dbase\format\FoxPro
 * @coversNothing
 */
final class Issue13Test extends TestBase
{
    /**
     * @medium
     * @throws \org\majkel\dbase\Exception
     */
    public function testOutOfMemoryBugReadFoxProMemoFieldCorrectly()
    {
        $table = Table::fromFile("tests/fixtures/issue-13-example-file.dbf");
        self::assertSame(2707, $table->getRecordsCount());
        self::assertSame(1102, $table->getRecordSize());
        self::assertSame(2792, $table->getHeaderSize());
        self::assertSame(78, $table->getFieldsCount());
        foreach ($table as $index => $row) {
            /** @var Record $row */
            self::assertNotEmpty($row->toArray());
            self::assertNotContains("\0", $row->DESCRIERE);
            if ($index > 100) {
                break;
            }
        }
    }
}
