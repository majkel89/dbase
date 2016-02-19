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
class Table implements Iterator, Countable, ArrayAccess, HeaderInterface {

    const MODE_READ = 'rb';
    const MODE_WRITE = self::MODE_READWRITE;
    const MODE_READWRITE = 'rb+';
    const MODE_CREATE = 'wb+';

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
    /** @var boolean */
    protected $transaction = false;

    /**
     * @param \org\majkel\dbase\Format $format
     */
    public function __construct(Format $format) {
        $this->format = $format;
    }

    /**
     * @param string $filePath
     * @param string $mode
     * @param string $format
     *
     * @return static
     * @throws \org\majkel\dbase\Exception
     */
    public static function fromFile($filePath,$mode = self::MODE_READ, $format = Format::AUTO) {
        $format = FormatFactory::getInstance()->getFormat($format, $filePath, $mode);
        return new static($format);
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
        $this->buffer = array();
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
     * Stores record in database
     * @param integer $index
     * @param \org\majkel\dbase\Record|\Traversable|array $data
     * @return void
     */
    public function update($index, $data) {
        if (!$data instanceof Record) {
            $data = new Record(Utils::toArray($data));
        }
        $this->getFormat()->update($index, $data);
        // update buffer to reflect current changes
        if (isset($this->buffer[$index])) {
            $this->buffer[$index] = $data;
        }
    }

    /**
     * Adds new record to database
     * @param \org\majkel\dbase\Record|\Traversable|array $data
     * @return integer index of new record
     */
    public function insert($data) {
        if ($data instanceof Record) {
            $data = clone $data;
        } else {
            $data = new Record(Utils::toArray($data));
        }
        return $this->getFormat()->insert($data);
    }

    /**
     * @param integer $index
     * @param boolean $deleted
     */
    public function markDeleted($index, $deleted) {
        $this->getFormat()->markDeleted($index, $deleted);
        if (isset($this->buffer[$index])) {
            $this->buffer[$index]->setDeleted($deleted);
        }
    }

    /**
     * Marks record as deleted
     * @param integer $index
     * @return void
     */
    public function delete($index) {
        $this->markDeleted($index, true);
    }

    /**
     * @return boolean
     */
    public function isTransaction() {
        return $this->getFormat()->isTransaction();
    }

    /**
     * Begins transaction
     * @throws Exception
     * @return void
     */
    public function beginTransaction() {
        $this->getFormat()->beginTransaction();
    }

    /**
     * Ends transaction
     * @throws Exception
     * @return void
     */
    public function endTransaction() {
        $this->getFormat()->endTransaction();
    }

    /**
     * Reads record from table
     * @param integer $index
     * @return \org\majkel\dbase\Record
     * @throws \org\majkel\dbase\Exception
     */
    public function getRecord($index) {
        if (!$this->isValid()) {
            throw new Exception('File is not opened', Exception::FILE_NOT_OPENED);
        }
        if (!$this->offsetExists($index)) {
            throw new Exception('Offset '.strval($index).' does not exists', Exception::INVALID_OFFSET);
        }
        if (isset($this->buffer[$index])) {
            return $this->buffer[$index];
        }
        $this->buffer = $this->getFormat()->getRecords($index, $this->getBufferSize());
        return $this->buffer[$index];
    }

    // <editor-fold defaultstate="collapsed" desc="HeaderInterface implementation">

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
     * @param integer                  $offset
     * @param \org\majkel\dbase\Record $value
     * @throws \org\majkel\dbase\Exception
     */
    public function offsetSet($offset, $value) {
        throw new Exception("Table is opened in read only mode", Exception::READ_ONLY);
    }

    /**
     * @param integer $offset
     * @throws \org\majkel\dbase\Exception
     */
    public function offsetUnset($offset) {
        throw new Exception("Table is opened in read only mode", Exception::READ_ONLY);
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
            $this->bufferSize = (integer)ceil($size / $recordSize);
        }
        if ($this->bufferSize < 1) {
            $this->bufferSize = 1;
        }
        return $this;
    }

    /**
     * @return \org\majkel\dbase\Format
     */
    protected function getFormat() {
        return $this->format;
    }

    /**
     * @return string
     */
    public function getFormatType() {
        return $this->getFormat()->getType();
    }

    /**
     * @return string|null
     */
    public function getMemoType() {
        try {
            return $this->getFormat()->getMemo()->getType();
        } catch (Exception $e) {
            return null;
        }
    }
}
