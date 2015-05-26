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
use DateTime;

/**
 * Description of Header
 *
 * @author majkel
 */
class Header implements IHeader, Iterator, Countable, ArrayAccess {

    const FLAG_VALID = 1;
    const FLAG_TRANSACTION = 2;

    const VERSION_5 = 3;
    const VERSION_7 = 4;

    /** @var integer */
    protected $flags = 0;
    /** @var Field[] */
    protected $fields;
    /** @var integer; */
    protected $version;
    /** @var DateTime; */
    protected $lastUpdate;
    /** @var integer */
    protected $recordsCount;
    /** @var integer */
    protected $recordSize;
    /** @var integer */
    protected $headerSize;

    /**
     * {@inheritdoc}
     */
    public function getFields() {
        return $this->fields;
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldsNames() {
        return array_keys($this->fields);
    }

    /**
     * {@inheritdoc}
     */
    public function getVersion() {
        return $this->version;
    }

    /**
     * {@inheritdoc}
     */
    public function getLastUpdate() {
        return $this->lastUpdate;
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldsCount() {
        return count($this->getFields());
    }

    /**
     * {@inheritdoc}
     */
    public function isPendingTransaction() {
        return $this->flags & self::FLAG_TRANSACTION;
    }

    /**
     * {@inheritdoc}
     */
    public function getRecordsCount() {
        return $this->recordsCount;
    }

    /**
     * {@inheritdoc}
     */
    public function getRecordSize() {
        return $this->recordSize;
    }

    /**
     * {@inheritdoc}
     */
    public function getHeaderSize() {
        return $this->headerSize;
    }

    /**
     * @return boolean
     */
    public function isValid() {
        return $this->flags & self::FLAG_VALID;
    }

    /**
     * @param \SplFileObject $file
     */
    public function loadFromFile($file) {
        $file->fseek(0);
        $this->flags = 0;

        $data = unpack('Cv/c3d/Vn/vhs/vrs/vr1/Ct', $file->fread(32));

        // load version
        $this->version = $data['v'] & 7;
        if ($this->version !== self::VERSION_5 && $this->version !== self::VERSION_7) {
            Exception::raise(Exception::UNSUPPORTED_VERSION);
        }

        // load last modfied date
        $date = new DateTime;
        $date->setDate(($data['d1']) + 1900, $data['d2'], $data['d3']);
        $this->lastUpdate = $date;

        // total number of recors
        $this->recordsCount = $data['n'];
        $this->recordSize = $data['rs'];
        $this->headerSize = $data['hs'];

        // transaction bit
        if ($data['t']) {
            $this->flags |= self::FLAG_TRANSACTION;
        }

        // load fields
        $this->fields = [];
        for ($fc = ($this->headerSize - 34) / 32; $fc > 0; --$fc) {
            $field = new Field();
            $field->loadFromFile($file);
            $this->fields[$field->getName()] = $field;
        }

        // done
        $this->flags |= self::FLAG_VALID;
    }

    /**
     * @return integer
     */
    public function count() {
        return $this->getFieldsCount();
    }

    /**
     * @return Field
     */
    public function current() {
        current($this->fields);
    }

    /**
     * @return string
     */
    public function key() {
        return key($this->fields);
    }

    /**
     * @return void
     */
    public function next() {
        next($this->fields);
    }

    /**
     * @return void
     */
    public function rewind() {
        reset($this->fields);
    }

    /**
     * @return boolean
     */
    public function valid() {
        return $this->key() !== null;
    }

    public function offsetExists($offset) {
        return isset($this->fields[$offset]);
    }

    public function offsetGet($offset) {
        return $this->fields[$offset];
    }

    public function offsetSet($offset, $value) {
        $this->fields[$offset] = $value;
    }

    public function offsetUnset($offset) {
        unset($this->fields[$offset]);
    }

}
