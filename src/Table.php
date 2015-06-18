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

/**
 * Provides read/write access to table
 *
 * @author majkel
 */
class Table implements Iterator, Countable, ArrayAccess, IHeader {

    const MODE_READ = 1;
    const MODE_WRITE = 2;
    const MODE_READWRITE = 3;

    const BUFFER_BYTES = 0;
    const BUFFER_RECORDS = 1;

    const DEFAULT_BUFFER_SIZE = 512;

    /** @var \org\majkel\dbase\Format */
    protected $format;
    /** @var integer Current record index */
    protected $index = 0;
    /** @var \org\majkel\dbase\Record[] Record buffer */
    protected $buffer;
    /** @var integer */
    protected $bufferSize = self::DEFAULT_BUFFER_SIZE;
    /** @var string[] Columns to read */
    protected $columns;

    /**
     * @param string $filePath
     * @param integer $mode
     * @param string $format
     */
    public function __construct($filePath, $mode = self::MODE_READ, $format = Format::AUTO) {
        $this->format = $this->getFormatFactory()->getFormat($format, $filePath, $mode);
    }

    /**
     * @param string[] $columns
     * @return \org\majkel\dbase\Table
     */
    public function setColumns($columns) {
        if (empty($columns) || !is_array($columns)) {
            $columns = null;
        }
        $this->columns = $columns;
        foreach ($this->getFields() as $field) {
            $field->setLoad(is_null($columns)
                || in_array($field->getName(), $columns));
        }
        $this->buffer = [];
        return $this;
    }

    /**
     * @return string[]|null
     */
    public function getColumns() {
        return $this->columns;
    }

    /**
     * @return boolean
     */
    public function isValid() {
        return $this->getHeader()->isValid();
    }

    /**
     * @return \org\majkel\dbase\Header
     */
    public function getHeader() {
        return $this->getFormat()->getHeader();
    }

    /**
     * Reads record from table
     * @param integer $index
     * @return \org\majkel\dbase\Record
     */
    public function getRecord($index) {
        if (!$this->isValid()) {
            Exception::raise(Exception::FILE_NOT_OPENED);
        }
        if (!$this->offsetExists($index)) {
            Exception::raise(Exception::INVALID_OFFSET);
        }
        if (isset($this->buffer[$index])) {
            return $this->buffer[$index];
        }
        $this->buffer = $this->getFormat()->getRecords($index, $this->getBufferSize());
        return $this->buffer[$index];
    }

    // <editor-fold defaultstate="collapsed" desc="IHeader implementation">

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

    /**
     * {@inheritdoc}
     */
    public function getField($indexOrName) {
        return $this->getHeader()->getField($indexOrName);
    }

    // </editor-fold>

    // <editor-fold defaultstate="collapsed" desc="Iterator implementation">

    /**
     * @return \org\majkel\dbase\Record
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

    // </editor-fold>

    // <editor-fold defaultstate="collapsed" desc="Countable implementation">

    /**
     * @return integer
     */
    public function count() {
        return $this->getRecordsCount();
    }

    // </editor-fold>

    // <editor-fold defaultstate="collapsed" desc="ArrayAccess implementation">

    /**
     * @param integer $offset
     * @return boolean
     */
    public function offsetExists($offset) {
        return $offset >= 0 && $offset < $this->getRecordsCount();
    }

    /**
     * @param integer $offset
     * @return \org\majkel\dbase\Record
     */
    public function offsetGet($offset) {
        return $this->getRecord($offset);
    }

    /**
     * @param integer $offset
     * @param \org\majkel\dbase\Record $value
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

    // </editor-fold>

    /**
     * @return integer
     */
    public function getBufferSize() {
        return $this->bufferSize;
    }

    /**
     * @param integer $size
     * @param integer $type
     * @return \org\majkel\dbase\Table
     */
    public function setBufferSize($size, $type = self::BUFFER_RECORDS) {
        if ($type === self::BUFFER_RECORDS) {
            $this->bufferSize = (integer)$size;
        } else {
            $recordSize = $this->getRecordSize();
            $this->bufferSize = ceil($size / $recordSize);
        }
        if ($this->bufferSize < 1) {
            $this->bufferSize = 1;
        }
        return $this;
    }

    /**
     * @return \org\majkel\dbase\FormatFactory
     */
    protected function getFormatFactory() {
        static $formatFactory = null;
        if (is_null($formatFactory)) {
            $formatFactory = new FormatFactory;
        }
        return $formatFactory;
    }

    /**
     * @return \org\majkel\dbase\Format
     */
    protected function getFormat() {
        return $this->format;
    }

}
