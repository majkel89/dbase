<?php
/**
 * User: Michał (majkel) Kowalik <maf.michal@gmail.com>
 * Date: 14-Feb-16
 * Time: 15:15
 */

namespace org\majkel\dbase;

/**
 * Class Builder
 *
 * @package org\majkel\dbase
 * @author  Michał (majkel) Kowalik <maf.michal@gmail.com>
 */
class Builder {

    /** @var string */
    private $memoType;
    /** @var string */
    private $formatType = Format::DBASE3;
    /** @var Header */
    private $header;

    /**
     * @return \org\majkel\dbase\Header
     */
    protected function getHeader() {
        if (is_null($this->header)) {
            $this->header = new Header();
        }
        return $this->header;
    }

    /**
     * @return Field[]
     */
    public function getFields() {
        return $this->getHeader()->getFields();
    }

    /**
     * @param string $fieldName
     * @return \org\majkel\dbase\Field
     * @throws \org\majkel\dbase\Exception
     */
    public function getField($fieldName) {
        return $this->getHeader()->getField($fieldName);
    }

    /**
     * @param \org\majkel\dbase\Field $field
     * @return $this
     * @throws \org\majkel\dbase\Exception
     */
    public function addField(Field $field) {
        $this->getHeader()->addField($field);
        return $this;
    }

    /**
     * @param $fieldName
     * @return $this
     * @throws \org\majkel\dbase\Exception
     */
    public function removeField($fieldName) {
        $this->getHeader()->removeField($fieldName);
        return $this;
    }

    /**
     * @param \org\majkel\dbase\Table $table
     * @return \org\majkel\dbase\Builder
     */
    public static function fromTable(Table $table) {
        $builder = new static();
        $builder->header = clone $table->getHeader();
        $builder->setFormatType($table->getFormatType());
        $builder->setMemoType($table->getMemoType());
        return $builder;
    }

    /**
     * @param string $filePath
     * @return \org\majkel\dbase\Builder
     */
    public static function fromFile($filePath) {
        return self::fromTable(new Table($filePath, Table::MODE_READ));
    }

    /**
     * @return static
     */
    public static function create() {
        return new static();
    }

    /**
     * @param string $formatType
     * @return \org\majkel\dbase\Builder
     * @throws \org\majkel\dbase\Exception
     */
    public function setFormatType($formatType) {
        if ($formatType === Format::AUTO) {
            throw new Exception("Format Format::AUTO is prohibited");
        }
        $this->formatType = $formatType;
        return $this;
    }

    /**
     * @param string $memoType
     * @return Builder
     */
    public function setMemoType($memoType) {
        $this->memoType = $memoType;
        return $this;
    }

    /**
     * @param string $filePath
     * @return Table
     */
    public function build($filePath) {
        $header = $this->getHeader();

        $format = FormatFactory::getInstance()
            ->getFormat($this->getFormatType(), $filePath, Table::MODE_CREATE)
            ->create($header);

        $memoType = $this->getMemoType();
        if ($memoType) {
            $memoFactory = MemoFactory::getInstance();
            $memoPath = $memoFactory->getMemoPathForDbf($format, $memoType);
            $memo = $memoFactory->getMemo($memoPath, Table::MODE_CREATE, $memoType)->create();
            $format->setMemo($memo);
        }
        $table = Table::fromFormat($format);
        return $table;
    }

    /**
     * @return string
     */
    public function getFormatType() {
        return $this->formatType;
    }

    /**
     * @return string
     */
    public function getMemoType() {
        return $this->memoType;
    }
}
