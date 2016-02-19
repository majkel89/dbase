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

class TransactionalUpdateBenchmark extends AthleticEvent {

    /** @var \org\majkel\dbase\Table */
    private $sourceTable;
    /** @var \org\majkel\dbase\Table */
    private $transactionalTable;
    /** @var \org\majkel\dbase\Table */
    private $nonTransactionalTable;
    /** @var \org\majkel\dbase\Record */
    private $currentField;
    /** @var integer */
    private $currentIndex;

    /**
     * Initialize test tables
     */
    public function classSetUp() {
        $sourceTableFilePath       = 'tests/fixtures/producents.dbf';
        $transactionalTablePath    = 'benchmarks/producents.dbf.transactional.copy';
        $nonTransactionalTablePath = 'benchmarks/producents.dbf.non-transactional.copy';

        copy($sourceTableFilePath, $transactionalTablePath);
        copy($sourceTableFilePath, $nonTransactionalTablePath);

        $this->sourceTable = Table::fromFile($sourceTableFilePath, Table::MODE_READ);

        $this->transactionalTable = Table::fromFile($transactionalTablePath, Table::MODE_READWRITE);
        $this->transactionalTable->beginTransaction();

        $this->nonTransactionalTable = Table::fromFile($nonTransactionalTablePath, Table::MODE_READWRITE);
    }

    /**
     * Close transaction
     */
    public function classTearDown() {
        $this->transactionalTable->endTransaction();
    }

    /**
     * Fetch new record
     */
    public function setUp() {
        $this->sourceTable->next();
        $this->currentIndex = $this->sourceTable->key();
        $this->currentField = $this->sourceTable->current();
    }

    /**
     * @iterations 1000
     */
    public function transactional() {
        $this->transactionalTable->update($this->currentIndex, $this->currentField);
    }

    /**
     * @iterations 1000
     */
    public function nonTransactional() {
        $this->nonTransactionalTable->update($this->currentIndex, $this->currentField);
    }

}