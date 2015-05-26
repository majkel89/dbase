<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace org\majkel\dbase;

use Iterator;
use Countable;
use ArrayAccess;
use SplFileObject;

/**
 * Description of Test
 *
 * - Traversable
 * - buffered (do not store all records)
 * - specify columns to load
 * - load header (access basic informations)
 * - list of columns
 * - can list column keys (easy to insert to csv as header)
 * - prefer exceptions over function results
 * - fluent syntax
 * - testable
 * - records as objects
 * - this class only reads
 *
 * @author majkel
 */
class DbfReader implements Iterator, Countable, ArrayAccess, IHeader {

    /** @var SplFileObject File handle */
    protected $file;
    /** @var Header Table heaader information */
    protected $header;
    /** @var integer Current record index */
    protected $index;

    /**
     * Opens table to read
     * @param string $filePath
     * @return DbfReader
     */
    public function open($filePath) {
        // opens file
        $file = new SplFileObject($filePath);
        $file->openFile('r');
        // load header
        $header = new Header();
        $header->loadFromFile($file);
        // assigns properties
        $this->file = $file;
        $this->header = $header;
        return $this;
    }

    /**
     * Check if table is open
     * @return boolean
     */
    public function isOpen() {
        return $this->file instanceof SplFileObject;
    }

    /**
     * Closes table
     * @return DbfReader
     */
    public function close() {
        if ($this->isOpen()) {
            $this->file = null;
        }
        return $this;
    }

    /**
     * @return boolean
     */
    public function isValid() {
        return $this->isOpen() && $this->getHeader()->isValid();
    }

    /**
     * @return Header
     */
    public function getHeader() {
        return $this->header;
    }

    /**
     * Reads record from table
     * @param integer $index
     * @return Record
     */
    public function getRecord($index) {
        if (!$this->offsetExists($index)) {
            Exception::raise(Exception::INVALID_OFFSET);
        }
        if (!$this->isOpen()) {
            Exception::raise(Exception::FILE_NOT_OPENED);
        }
        $this->file->fseek($this->getHeaderSize() + $this->getRecordSize() * $index);
        $record = new Record;

        $fields = $this->getFields();
        $format = ['a1d'];
        foreach ($fields as $i => $field) {
            $format[] = 'a' . $field->getLength() . 'ff' . $i;
        }
        $data = unpack(implode('/', $format), $this->file->fread($this->getRecordSize()));
        $record->setDeleted($data['d'] !== ' ');
        unset($data['d']);
        foreach ($data as $index => $value) {
            $idx = substr($index, 2);
            $record->{$fields[$idx]->getName()} = $value;
        }
        return $record;
    }

    /**
     * {@inheritdoc}
     */
    public function getVersion() {
        return $this->getHeader()->getVersion();
    }

    /**
     * {@inheritdoc}
     */
    public function isPendingTransaction() {
        return $this->getHeader()->isPendingTransaction();
    }

    /**
     * {@inheritdoc}
     */
    public function getLastUpdate() {
        return $this->getHeader()->getLastUpdate();
    }

    /**
     * {@inheritdoc}
     */
    public function getFields() {
        return $this->getHeader()->getFields();
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldsNames() {
        return $this->getHeader()->getFieldsNames();
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldsCount() {
        return $this->getHeader()->getFieldsCount();
    }

    /**
     * {@inheritdoc}
     */
    public function getRecordsCount() {
        return $this->getHeader()->getRecordsCount();
    }

    /**
     * {@inheritdoc}
     */
    public function getRecordSize() {
        return $this->getHeader()->getRecordSize();
    }

    /**
     * {@inheritdoc}
     */
    public function getHeaderSize() {
        return $this->getHeader()->getHeaderSize();
    }

    /// Iterator ///////////////////////////////////////////////////////////////

    /**
     * @return Record
     */
    public function current() {
        return $this->getRecord($this->index);
    }

    /**
     * @return integer
     */
    public function key() {
        return $this->index;
    }

    /**
     * @return void
     */
    public function next() {
        $this->index++;
    }

    /**
     * @return void
     */
    public function rewind() {
        $this->index = 0;
    }

    /**
     * @return boolean
     */
    public function valid() {
        return $this->offsetExists($this->index);
    }

    /// Countable //////////////////////////////////////////////////////////////

    /**
     * @return integer
     */
    public function count() {
        return $this->getRecordsCount();
    }

    /// ArrayAccess ////////////////////////////////////////////////////////////

    /**
     * @param integer $offset
     * @return boolean
     */
    public function offsetExists($offset) {
        return $offset < $this->getRecordsCount();
    }

    /**
     * @param integer $offset
     * @return Record
     */
    public function offsetGet($offset) {
        return $this->getRecord($offset);
    }

    /**
     * @param integer $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value) {
        Exception::raise(Exception::READ_ONLY, $offset, $value);
    }

    /**
     * @param integer $offset
     */
    public function offsetUnset($offset) {
        Exception::raise(Exception::READ_ONLY, $offset);
    }

}
