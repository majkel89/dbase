<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace org\majkel\dbase\filter;

use org\majkel\dbase\Filter;
use org\majkel\dbase\Field;
use org\majkel\dbase\Flags;

/**
 * Description of Trim
 *
 * @author majkel
 */
class TrimFilter extends Filter {

    use Flags;

    const FILTER_INPUT = 1;
    const FILTER_OUTPUT = 2;

    public function __construct() {
        $this->flags = self::FILTER_INPUT | self::FILTER_OUTPUT;
    }

    /**
     * @return boolean
     */
    public function isFilterInput() {
        return $this->flagEnabled(self::FILTER_INPUT);
    }

    /**
     * @param boolean $filter
     * @return \org\majkel\dbase\filter\TrimFilter
     */
    public function setFilterInput($filter) {
        return $this->enableFlag(self::FILTER_INPUT, $filter);
    }

    /**
     * @return boolean
     */
    public function isFilterOutput() {
        return $this->flagEnabled(self::FILTER_OUTPUT);
    }

    /**
     * @param boolean $filter
     * @return \org\majkel\dbase\filter\TrimFilter
     */
    public function setFilterOutput($filter) {
        return $this->enableFlag(self::FILTER_OUTPUT, $filter);
    }

    /**
     * {@inheritdoc}
     */
    public function toValue($value) {
        return $this->isFilterInput() ? trim($value) : $value;
    }

    /**
     * {@inheritdoc}
     */
    public function fromValue($value) {
        return $this->isFilterOutput() ? trim($value) : $value;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsType($type) {
        return Field::TYPE_CHARACTER === $type || Field::TYPE_MEMO === $type;
    }

}
