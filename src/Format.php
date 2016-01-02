<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace org\majkel\dbase;

use DateTime;
use Exception as StdException;

/**
 * Implements how to read / write header / record(s)
 *
 * @author majkel
 */
abstract class Format {

    const AUTO = 'auto';
    const DBASE3 = 'dbase3';

    const NAME = 'Abstract DBase Format';

    const FIELD_SIZE = 32;
    const HEADER_SIZE = 32;

    const HEADER_FORMAT = 'Cv/C3d/Vn/vhs/vrs/vr1/Ct';
    const FIELD_FORMAT = 'A11n/a1t/Vrr1/Cll/Cdd/vrr2/Cwa/vrr3/Csff/Crr4';

    /** @var \SplFileObject File handle */
    protected $file;
    /** @var \SplFileObject File handle */
    protected $memoFile;
    /** @var \org\majkel\dbase\Header */
    protected $header;
    /** @var string record unpack format string */
    protected $recordFormat;
    /** @var string */
    protected $mode;
    /** @var boolean */
    protected $transaction = false;
    /** @var string */
    protected $writeRecordFormat;

    /**
     * @param string $filePath
     * @param string $mode
     */
    public function __construct($filePath, $mode) {
        $this->mode = $mode;
        $this->file = File::getObject($filePath, $mode);
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
        reset($records);
        return current($records);
    }

    /**
     * @param integer $index
     * @param integer $length
     * @return \org\majkel\dbase\Record[]
     * @throws \org\majkel\dbase\Exception
     */
    public function getRecords($index, $length) {
        list($start, $stop) = $this->getReadBoundaries($index, $length);
        $file = $this->getFile();
        $rSz = $this->getHeader()->getRecordSize();
        $file->fseek($this->getHeader()->getHeaderSize() + $start * $rSz);
        $format = $this->getRecordFormat();
        $allData = $file->fread($rSz * $length);
        $records = [];
        for ($i = 0; $start < $stop; ++$start, ++$i) {
            $data = unpack($format, strlen($allData) === $rSz
                ? $allData : substr($allData, $i * $rSz, $rSz));
            $records[$start] = $this->createRecord($data);
        }
        return $records;
    }

    /**
     * @param string $type
     * @return boolean
     */
    abstract public function supportsType($type);

    /**
     * @return \SplFileObject
     */
    protected function getFile() {
        return $this->file;
    }

    /**
     * @param integer $index
     * @param integer $length
     * @return array [$index, $stop]
     * @throws \org\majkel\dbase\Exception
     */
    protected function getReadBoundaries($index, $length) {
        $totalRecords = $this->getHeader()->getRecordsCount();
        if ($index < 0) {
            $index = 0;
        }
        if ($index >= $totalRecords) {
            throw new Exception("Trying to read outside of file");
        }
        $stop = $index + $length;
        if ($stop > $totalRecords) {
            $stop -= $stop - $totalRecords;
        }
        return [$index, $stop];
    }

    /**
     * @param string $ext
     * @return string
     */
    protected function getMemoFilePath($ext) {
        $fileInfo = $this->getFileInfo();
        $path = $fileInfo->getPath() . '/';
        $basename = $fileInfo->getBasename();
        $index = stripos($basename, '.dbf');
        if ($index !== false) {
            $path .= substr($basename, 0, $index);
        } else {
            $path .= $basename;
        }
        return $path . '.' . $ext;
    }

    /**
     * @return string
     */
    protected function getMode() {
        return $this->mode;
    }

    /**
     * @return \org\majkel\dbase\memo\MemoInterface
     * @throws Exception
     */
    protected function getMemoFile() {
        if (is_null($this->memoFile)) {
            $supportedMemoFiles = array(
                'dbt' => 'org\majkel\dbase\memo\DbtMemo',
                'fpt' => 'org\majkel\dbase\memo\FptMemo',
            );
            foreach ($supportedMemoFiles as $ext => $class) {
                $filePath = $this->getMemoFilePath($ext);
                if (is_readable($filePath)) {
                    $this->memoFile = new $class($filePath, $this->getMode());
                    return $this->memoFile;
                }
            }
            throw new Exception("Unable to open memo file");
        }
        return $this->memoFile;
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
     * @return bool
     * @throws \Exception
     */
    public function isTransaction() {
        if ($this->transaction) {
            return true;
        }
        if (!$this->getFile()->flock(LOCK_SH)) {
            return true;
        }
        try {
            $isTransaction = $this->checkIfTransaction();
        } catch (StdException $e) {
            $this->getFile()->flock(LOCK_UN);
            throw $e;
        }
        $this->getFile()->flock(LOCK_UN);
        return $isTransaction;
    }

    /**
     * @return HeaderInterface
     * @return boolean
     */
    protected function checkIfTransaction() {
        $currentHeader = $this->readHeader();
        $header = $this->getHeader();
        $header->setPendingTransaction($currentHeader->isPendingTransaction());
        $header->setLastUpdate($currentHeader->getLastUpdate());
        $header->setRecordsCount($currentHeader->getRecordsCount());
        return $header->isPendingTransaction();
    }

    /**
     * @param boolean $enabled
     */
    protected function setTransactionStatus($enabled) {
        $enabled = (boolean) $enabled;
        $this->getHeader()->setPendingTransaction($enabled);
        $this->transaction = $enabled;
        $this->writeHeader();
    }

    /**
     * @throws \Exception
     * @throws \org\majkel\dbase\Exception
     */
    public function beginTransaction() {
        if ($this->transaction) {
            throw new Exception("Transaction already started");
        }
        if (!$this->getFile()->flock(LOCK_EX)) {
            throw new Exception("Failed to acquire exclusive lock");
        }
        try {
            if ($this->checkIfTransaction()) {
                throw new Exception("Transaction already started by somebody else");
            }
            $this->setTransactionStatus(true);
        } catch (StdException $e) {
            $this->getFile()->flock(LOCK_UN);
            throw $e;
        }
        $this->getFile()->flock(LOCK_UN);
    }

    /**
     * @throws \Exception
     * @throws \org\majkel\dbase\Exception
     */
    public function endTransaction() {
        if (!$this->transaction) {
            throw new Exception("Transaction haven't been started yet");
        }
        if (!$this->getFile()->flock(LOCK_EX)) {
            throw new Exception("Failed to acquire exclusive lock");
        }
        try {
            if ($this->checkIfTransaction()) {
                throw new Exception("Transaction haven't been started yet");
            }
            $this->setTransactionStatus(false);
        } catch (StdException $e) {
            $this->getFile()->flock(LOCK_UN);
            throw $e;
        }
        $this->getFile()->flock(LOCK_UN);
    }

    /**
     * @param integer $index
     * @param boolean $deleted
     * @throws \org\majkel\dbase\Exception
     */
    public function markDeleted($index, $deleted) {
        if (!$this->transaction) {
            $this->writeHeader();
        }
        list($offset) = $this->getReadBoundaries($index, 0);
        $file = $this->getFile();
        $file->fseek($offset * $this->getHeader()->getRecordSize() + $this->getHeader()->getHeaderSize());
        $file->fwrite($deleted ? "\x2A" : "\x20");
    }

    /**
     * @return string
     */
    protected function getWriteRecordFormat() {
        if (is_null($this->writeRecordFormat)) {
            $this->writeRecordFormat = 'C';
            foreach ($this->getHeader()->getFields() as $i => $field) {
                $this->writeRecordFormat .= 'a' . $field->getLength();
            }
        }
        return $this->writeRecordFormat;
    }

    /**
     * @param \org\majkel\dbase\Record|\ArrayAccess|array $data
     * @return integer
     */
    public function insert($data) {
        $header = $this->getHeader();
        $newIndex = $header->getRecordsCount();
        $header->setRecordsCount($newIndex + 1);
        $this->writeHeader();
        $format = $this->getRecordFormat();
        $file = $this->getFile();
        $file->fseek(0, SEEK_END);
        $DATA = call_user_func_array('pack', array_merge([$format], $data));
        $file->fwrite($DATA);
        return $newIndex;
    }

    /**
     * @param integer $index
     * @param \org\majkel\dbase|\ArrayAccess|array $data
     * @return void
     */
    public function update($index, $data) {
        $this->writeHeader();
        list($offset) = $this->getReadBoudries($index, 0);
        $file = $this->getFile();
        $file->fseek($offset * $this->getHeader()->getRecordSize());
        // .. store data .. //
    }

    /**
     * @return string
     */
    protected function getWriteHeaderFormat() {
        return 'C4Vvvv@32';
    }

    /**
     * @return void
     */
    protected function writeHeader() {
        $file = $this->getFile();
        $file->fseek(0);
        $header = $this->getHeader();
        $header->setLastUpdate(new \DateTime());
        $date = $header->getLastUpdate();
        $data = pack($this->getWriteHeaderFormat(),
            $header->getVersion(),
            $date->format('Y') - 1900,
            (integer) $date->format('m'),
            (integer) $date->format('d'),
            $header->getRecordsCount(),
            $header->getRecordSize(),
            $header->getHeaderSize(),
            $header->isPendingTransaction()
        );
        $file->fwrite($data);
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
        $recordsCount = $header->getRecordsCount();
        if ($recordsCount < 0 || $headerSize + $recordsCount * $header->getRecordSize() > $fileSize) {
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

        $header->lockFields();
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
     * @return \org\majkel\dbase\Field
     * @throws \org\majkel\dbase\Exception
     */
    protected function createField($data) {
        if (!$this->supportsType($data['t'])) {
            throw new Exception("Format `{$this->getName()}` does not support field `{$data['t']}`");
        }
        $field = Field::create($data['t']);
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
        return $this->getMemoFile()->getEntry($index);
    }

    /**
     * @param array $data
     * @return \org\majkel\dbase\Record
     */
    protected function createRecord($data) {
        $record = new Record;
        $record->setDeleted($data['d'] !== ' ');
        $fields = $this->getHeader()->getFields();
        foreach ($fields as $name => $field) {
            if ($field->isLoad()) {
                $value = $data['f'.$name];
                if ($field->isMemoEntry()) {
                    $value = $this->readMemoEntry($value);
                }
                $record[$name] = $field->unserialize($value);
            }
        }
        return $record;
    }

    /**
     * @return string[]
     */
    public static function getSupportedFormats() {
        return [
            Format::DBASE3,
        ];
    }
}
