<?php
/**
 * Created by PhpStorm.
 * User: majkel
 * Date: 03-Jan-16
 * Time: 18:54
 */

namespace org\majkel\dbase\benchmarks;

use Athletic\AthleticEvent;
use org\majkel\dbase\Table;

class SequentialReadBenchmark extends AthleticEvent {

    /** @var \org\majkel\dbase\Table */
    private $notBufferedTable;
    /** @var \org\majkel\dbase\Table */
    private $buffered200Table;
    /** @var \org\majkel\dbase\Table */
    private $buffered100Table;
    /** @var \org\majkel\dbase\Table */
    private $buffered500Table;

    /**
     * Initialize test tables
     */
    public function classSetUp() {
        $sourceTableFilePath    = 'tests/fixtures/producents.dbf';
        $this->notBufferedTable = Table::fromFile($sourceTableFilePath, Table::MODE_READ);
        $this->buffered200Table = Table::fromFile($sourceTableFilePath, Table::MODE_READ);
        $this->buffered100Table = Table::fromFile($sourceTableFilePath, Table::MODE_READ);
        $this->buffered500Table = Table::fromFile($sourceTableFilePath, Table::MODE_READ);
    }

    /**
     * @iterations 1000
     */
    public function notBuffered() {
        $this->notBufferedTable->current();
        $this->notBufferedTable->next();
    }

    /**
     * @iterations 1000
     */
    public function buffered100() {
        $this->buffered200Table->current();
        $this->buffered200Table->next();
    }

    /**
     * @iterations 1000
     */
    public function buffered200() {
        $this->buffered100Table->current();
        $this->buffered100Table->next();
    }

    /**
     * @iterations 1000
     */
    public function buffered500() {
        $this->buffered500Table->current();
        $this->buffered500Table->next();
    }

}
