<?php
/**
 * Created by PhpStorm.
 * User: majkel
 * Date: 30.08.18
 * Time: 21:02
 */

namespace org\majkel\dbase\tests\integration;

use org\majkel\dbase\Builder;
use org\majkel\dbase\Record;
use org\majkel\dbase\Table;
use org\majkel\dbase\tests\utils\TestBase;

/**
 * @coversDefaultClass \org\majkel\dbase\format\FoxPro
 */
final class Issue13Test extends TestBase
{
    const ORIGINAL_TABLE = 'tests/fixtures/materie.dbf';
    const COPIED_TABLE = 'tests/fixtures/materie-2.dbf';

    /**
     * @medium
     * @throws \org\majkel\dbase\Exception
     */
    public function testWriteFoxProFile()
    {
        $originalTable = Table::fromFile(self::ORIGINAL_TABLE);
        self::assertSame(2707, $originalTable->getRecordsCount());
        self::assertSame(1102, $originalTable->getRecordSize());
        self::assertSame(2792, $originalTable->getHeaderSize());
        self::assertSame(78, $originalTable->getFieldsCount());

        if (file_exists(self::COPIED_TABLE)) {
            unlink(self::COPIED_TABLE);
        }

        $copiedTable = Builder::fromTable($originalTable)->build(self::COPIED_TABLE);

        foreach ($originalTable as $index => $row) {
            /** @var Record $row */
            self::assertNotEmpty($row->toArray());
            self::assertNotContains("\0", $row->DESCRIERE);
            $copiedTable->insert($row->toArray());
            if ($index > 50) {
                break;
            }
        }
    }

    /**
     * @before testWriteFoxProFile
     * @medium
     * @throws \org\majkel\dbase\Exception
     */
    public function testVerifyFoxPro()
    {
        $originalTable = Table::fromFile(self::ORIGINAL_TABLE);
        $copiedTable = Table::fromFile(self::COPIED_TABLE);

        foreach ($copiedTable as $index => $row) {
            /** @var Record $row */
            /** @var Record $originalRow */
            $originalRow = $originalTable[$index];

            self::assertSame($originalRow->toArray(), $row->toArray());
        }
    }
}
