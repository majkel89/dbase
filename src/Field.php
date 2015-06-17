<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace org\majkel\dbase;

use Traversable;

/**
 * Stores field definition
 *
 * @author majkel
 */
abstract class Field {

    const TYPE_CHARACTER = 'C';
    const TYPE_LOGICAL = 'L';
    const TYPE_DATE = 'D';
    const TYPE_NUMERIC = 'N';
    const TYPE_MEMO = 'M';

    const MAX_NAME_LENGTH = 10;

    /** @var string */
    protected $name;
    /** @var integer */
    protected $length;
    /** @var boolean */
    protected $load = true;
    /** @var \org\majkel\dbase\IFilter[] */
    protected $filters = [];

    /**
     * @codeCoverageIgnore
     */
    protected function __construct() {
    }

    /**
     * Adds filter
     * @param \org\majkel\dbase\IFilter $filter
     * @return \org\majkel\dbase\Field
     */
    public function addFilter(IFilter $filter) {
        if ($filter->supportsType($this->getType())) {
            $this->filters[] = $filter;
        }
        return $this;
    }

    /**
     * Adds filters
     * @param \org\majkel\dbase\IFilter[] $filters
     * @return \org\majkel\dbase\Field
     */
    public function addFilters($filters) {
        if (!is_array($filters) && !$filters instanceof Traversable) {
            return $this;
        }
        foreach ($filters as $filter) {
            $this->addFilter($filter);
        }
        return $this;
    }

    /**
     * Returns all filters
     * @return \org\majkel\dbaseIFilter[]
     */
    public function getFilters() {
        return $this->filters;
    }

    /**
     * Removes filter at index or by object
     * @param integer $indexOrFilter
     * @return \org\majkel\dbase\Field
     */
    public function removeFilter($indexOrFilter) {
        if (is_scalar($indexOrFilter)) {
            unset($this->filters[$indexOrFilter]);
        } else if ($indexOrFilter instanceof IFilter) {
            foreach ($this->filters as $i => $filter) {
                if ($filter === $indexOrFilter) {
                    unset($this->filters[$i]);
                }
            }
        }
        return $this;
    }

    /**
     * Returns field name
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Sets filed name
     * @param string $name
     * @return \org\majkel\dbase\Field
     */
    public function setName($name) {
        if (strlen($name) > self::MAX_NAME_LENGTH) {
            throw new Exception("Field name cannot be longer than ".self::MAX_NAME_LENGTH." characters");
        }
        $this->name = $name;
        return $this;
    }

    /**
     * Returns filed length
     * @return integer
     */
    public function getLength() {
        return $this->length;
    }

    /**
     * Sets filed length
     * @param integer $length
     * @return \org\majkel\dbase\Field
     */
    public function setLength($length) {
        $this->length = (integer)$length;
        return $this;
    }

    /**
     * Allows to set wether to load value
     * @param boolean $load
     * @return \org\majkel\dbase\Field
     */
    public function setLoad($load) {
        $this->load = (boolean)$load;
        return $this;
    }

    /**
     * Determins wether to load filed value
     * @return boolean;
     */
    public function isLoad() {
        return $this->load;
    }

    /**
     * Constructs value from raw data and applyes filters
     * @param string $data
     * @return mixed
     */
    public function unserialize($data) {
        $value = $this->fromData($data);
        foreach ($this->getFilters() as $filter) {
            $value = $filter->toValue($value);
        }
        return $value;
    }

    /**
     * Applyes filters and converts value to raw data
     * @param mixed $value
     * @return string
     */
    public function serialize($value) {
        $filters = $this->getFilters();
        for ($i = count($filters) - 1; $i >= 0; --$i) {
            $value = $filters[$i]->fromValue($value);
        }
        return $this->toData($value);
    }

    /**
     * Creates value from raw data
     * @param string $data
     * @return mixed
     */
    abstract public function fromData($data);

    /**
     * Converts value to raw data
     * @param mixed $value
     * @return string
     */
    abstract public function toData($value);

    /**
     * Return field type
     * @return integer
     */
    abstract public function getType();

    /**
     * Determin wether to read data from memo file
     * @return boolean
     */
    public function isMemoEntry() {
        return false;
    }

    /**
     * Constructs Filed based on type
     * @param string $type
     * @return \org\majkel\dbase\Field
     * @throws Exception
     */
    public static function create($type) {
        switch ($type) {
            case Field::TYPE_CHARACTER:
                return new field\CharacterField;
            case Field::TYPE_DATE:
                return new field\DateField;
            case Field::TYPE_LOGICAL:
                return new field\LogicalField;
            case Field::TYPE_MEMO:
                return new field\MemoField;
            case Field::TYPE_NUMERIC:
                return new field\NumericField;
            default:
                throw new Exception("Unsupported field `$type`");
        }
    }

}
