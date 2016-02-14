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

    /** @var Flags */
    private $flags;

    const FILTER_INPUT = 1;
    const FILTER_OUTPUT = 2;

    /**
     * @return \org\majkel\dbase\Flags
     */
    protected function getFlags() {
        if (is_null($this->flags)) {
            $this->flags = new Flags(self::FILTER_INPUT | self::FILTER_OUTPUT);
        }
        return $this->flags;
    }

    /**
     * @return boolean
     */
    public function isFilterInput() {
        return $this->getFlags()->flagEnabled(self::FILTER_INPUT);
    }

    /**
     * @param boolean $filter
     * @return \org\majkel\dbase\filter\TrimFilter
     */
    public function setFilterInput($filter) {
        $this->getFlags()->enableFlag(self::FILTER_INPUT, $filter);
        return $this;
    }

    /**
     * @return boolean
     */
    public function isFilterOutput() {
        return $this->getFlags()->flagEnabled(self::FILTER_OUTPUT);
    }

    /**
     * @param boolean $filter
     * @return \org\majkel\dbase\filter\TrimFilter
     */
    public function setFilterOutput($filter) {
        $this->getFlags()->enableFlag(self::FILTER_OUTPUT, $filter);
        return $this;
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
