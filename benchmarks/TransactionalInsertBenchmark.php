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

class TransactionalInsertBenchmark extends AthleticEvent {

    /** @var \org\majkel\dbase\Table */
    private $sourceTable;
    /** @var \org\majkel\dbase\Table */
    private $transactionalTable;
    /** @var \org\majkel\dbase\Table */
    private $nonTransactionalTable;
    /** @var \org\majkel\dbase\Record */
    private $currentField;

    /**
     * Initialize test tables
     */
    public function classSetUp() {
        $sourceTableFilePath       = 'tests/fixtures/producents.dbf';
        $transactionalTablePath    = 'benchmarks/producents.dbf.transactional.copy';
        $nonTransactionalTablePath = 'benchmarks/producents.dbf.non-transactional.copy';

        copy($sourceTableFilePath, $transactionalTablePath);
        copy($sourceTableFilePath, $nonTransactionalTablePath);

        $this->sourceTable = new Table($sourceTableFilePath, Table::MODE_READ);

        $this->transactionalTable = new Table($transactionalTablePath, Table::MODE_READWRITE);
        $this->transactionalTable->beginTransaction();

        $this->nonTransactionalTable = new Table($nonTransactionalTablePath, Table::MODE_READWRITE);
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
        $this->currentField = $this->sourceTable->current();
    }

    /**
     * @iterations 1000
     */
    public function transactional() {
        $this->transactionalTable->insert($this->currentField);
    }

    /**
     * @iterations 1000
     */
    public function nonTransactional() {
        $this->nonTransactionalTable->insert($this->currentField);
    }

}