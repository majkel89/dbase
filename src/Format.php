<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace org\majkel\dbase;

use SplFileObject;
use DateTime;

/**
 * Implements how to read / write header / record(s)
 *
 * @author majkel
 */
abstract class Format {

    const AUTO = 'auto';
    const DBASE3 = 'dbase3';

    const FIELD_SIZE = 32;
    const HEADER_SIZE = 32;

    const HEADER_FORMAT = 'Cv/c3d/Vn/vhs/vrs/vr1/Ct';
    const FIELD_FORMAT = 'A11n/a1t/Vrr1/Cll/Cdd/vrr2/Cwa/vrr3/Csff/Crr4';

    /** @var \SplFileObject File handle */
    protected $file;
    /** @var \SplFileObject File handle */
    protected $dbtFile;
    /** @var org\majkel\dbase\Header */
    protected $header;
    /** @var string record unpack format string */
    protected $recordFormat;
    /** @var string */
    protected $mode;

    /**
     * @param string $filePath
     * @param string $mode
     */
    public function __construct($filePath, $mode) {
        $this->mode = $mode;
        $this->file = new SplFileObject($filePath, $mode);
    }

    /**
     * @return \org\majkel\dbase\Header
     */
    public function getHeader() {
        if (is_null($this->header)) {
            $this->header = $this->readHeader();
        }
        return $this->header;
    }

    /**
     * @return boolean
     */
    public function isValid() {
        return $this->getHeader()->isValid();
    }

    /**
     * @return \SplFileInfo
     */
    public function getFileInfo() {
        return $this->getFile()->getFileInfo();
    }

    /**
     * @return \SplFileInfo
     */
    public function getMemoFileInfo() {
        return $this->getMemoFile()->getFileInfo();
    }

    /**
     * @return string Format name
     */
    public function getName() {
        return static::NAME;
    }

    /**
     * @param integer $index
     * @return Record
     */
    public function getRecord($index) {
        $records = $this->getRecords($index, 1);
        return $records[0];
    }

    /**
     * @param integer $index
     * @param integer $length
     * @param string[] $columns
     * @return Record[]
     * @throws Exception
     */
    public function getRecords($index, $length) {
        $totalRecords = $this->getHeader()->getRecordsCount();
        if ($index < 0) {
            $index = 0;
        }
        if ($index >= $totalRecords) {
            $index = $totalRecords - 1;
        }
        $stop = $index + $length;
        if ($stop > $totalRecords) {
            $stop -= $stop - $totalRecords;
        }
        if ($index < 0 || $stop < 1) {
            throw new Exception("Cannot read any records becouse file is empty");
        }
        $file = $this->getFile();
        $rSz = $this->getHeader()->getRecordSize();
        $file->fseek($this->getHeader()->getHeaderSize() + $index * $rSz);
        $format = $this->getRecordFormat();
        $allData = $file->fread($rSz * $length);
        $records = [];
        for ($i = 0; $index < $stop; ++$index, ++$i) {
            $data = unpack($format, strlen($allData) === $rSz
                ? $allData : substr($allData, $i * $rSz, $rSz));
            $records[$index] = $this->createRecord($data);
        }
        return $records;
    }

    /**
     * @param integer $field
     * @return boolean
     */
    abstract protected function supportsField($field);

    /**
     * @return \SplFileObject
     */
    protected function getFile() {
        return $this->file;
    }

    /**
     * @return string
     */
    protected function getMemoFilePath() {
        $fileInfo = $this->getFileInfo();
        $path = $fileInfo->getPath() . '/';
        $basename = $fileInfo->getBasename();
        $index = strrpos($basename, '.'.$fileInfo->getExtension());
        if ($index !== false) {
            $path .= substr($basename, 0, $index);
        } else {
            $path .= $basename;
        }
        return $path . '.dbt';
    }

    /**
     * @return string
     */
    protected function getMode() {
        return $this->mode;
    }

    /**
     * @return SplFileObject
     */
    protected function getMemoFile() {
        if (is_null($this->dbtFile)) {
            $this->dbtFile = new SplFileObject($this->getMemoFilePath(),
                    $this->getMode());
        }
        return $this->dbtFile;
    }

    /**
     * @param array $data
     * @return \org\majkel\dbase\Header
     */
    protected function createHeader($data) {
        $header = new Header();
        $header->setVersion($data['v']);
        $header->setLastUpdate($this->getLastDate($data['d1'], $data['d2'], $data['d3']));
        $header->setRecordsCount($data['n']);
        $header->setRecordSize($data['rs']);
        $header->setHeaderSize($data['hs']);
        $header->setPendingTransaction($data['t']);
        $header->setValid(true);
        return $header;
    }

    /**
     * @return Header
     */
    protected function readHeader() {
        $file = $this->getFile();
        $file->fseek(0);

        $hSz = static::HEADER_SIZE;
        $data = unpack(static::HEADER_FORMAT, $file->fread($hSz));

        $header = $this->createHeader($data);

        $fileSize = $file->getSize();
        $headerSize = $header->getHeaderSize();
        if ($headerSize + $header->getRecordsCount() * $header->getRecordSize() > $fileSize) {
            $header->setValid(false);
            return $header;
        }

        $fieldsSz = $headerSize - $hSz - 1;
        $allFields = $file->fread($fieldsSz);
        $fieldsCount = floor($fieldsSz / $hSz);

        $fSz = static::FIELD_SIZE;
        $format = static::FIELD_FORMAT;
        for ($index = 0; $index < $fieldsCount; ++$index) {
            $data = unpack($format, substr($allFields, $index * $fSz, $fSz));
            $col = strpos($data['n'], "\0");
            if ($col !== false) {
                $data['n'] = substr($data['n'], 0, $col);
            }
            $header->addField($this->createField($data));
        }

        return $header;
    }

    /**
     * @param integer $year
     * @param integer $moth
     * @param integer $day
     * @return DateTime
     */
    protected function getLastDate($year, $moth, $day) {
        $date = new DateTime;
        $date->setDate($year + 1900, $moth, $day);
        $date->setTime(0, 0, 0);
        return $date;
    }

    /**
     * @param array $data
     * @return Field
     */
    protected function createField($data) {
        $field = Field::create($data['t']);
        if (!$this->supportsField($data['t'])) {
            throw new Exception("Format `{$this->getName()}` does not support field `{$data['t']}`");
        }
        $field->setName(rtrim($data['n']));
        $field->setLength($data['ll']);
        return $field;
    }

    /**
     * @return string
     */
    protected function getRecordFormat() {
        if (is_null($this->recordFormat)) {
            $format = ['a1d'];
            foreach ($this->getHeader()->getFields() as $i => $field) {
                $format[] = 'a' . $field->getLength() . 'f' . $i;
            }
            $this->recordFormat = implode('/', $format);
        }
        return $this->recordFormat;
    }

    /**
     * @param integer $index
     * @return string
     */
    protected function readMemoEntry($index) {
        $memoFile = $this->getMemoFile();
        $memoFile->fseek($index * 512);
        return $memoFile->fread(512);
    }

    /**
     * @param array $data
     * @return \org\majkel\dbase\Record
     */
    protected function createRecord($data) {
        $record = new Record;
        $record->setDeleted($data['d'] !== ' ');
        $fields = $this->getHeader()->getFields();
        foreach ($data as $index => $value) {
            if ($index !== 'd') {
                $field = $fields[substr($index, 1)];
                /* @var $field \org\majkel\dbase\Field */
                if ($field->isLoad()) {
                    if ($field->isMemoEntry()) {
                        $value = $this->readMemoEntry($value);
                    }
                    $record[$field->getName()] = $field->unserialize($value);
                }
            }
        }
        return $record;
    }
}
