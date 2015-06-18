<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace org\majkel\dbase\filter;

use org\majkel\dbase\Filter;
use org\majkel\dbase\Field;

/**
 * Description of Trim
 *
 * @author majkel
 */
class CharsetFilter extends Filter {

    /** @var string */
    protected $input;
    /** @var string */
    protected $output;

    /**
     * @param string $input
     * @param string $output
     */
    public function __construct($input = null, $output = null) {
        $this->setInput($input)->setOutput($output);
    }

    /**
     * @return string
     */
    public function getInput() {
        return $this->input;
    }

    /**
     * @param string $input
     * @return \org\majkel\dbase\Trim\Charset
     */
    public function setInput($input) {
        if (empty($input)) {
            $input = iconv_get_encoding('input_encoding');
        }
        $this->input = $input;
        return $this;
    }

    /**
     * @return string
     */
    public function getOutput() {
        return $this->output;
    }

    /**
     * @param string $output
     * @return \org\majkel\dbase\Trim\Charset
     */
    public function setOutput($output) {
        if (empty($output)) {
            $output = iconv_get_encoding('output_encoding');
        }
        $this->output = $output;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function fromValue($value) {
        return iconv($this->getOutput(), $this->getInput(), $value);
    }

    /**
     * {@inheritdoc}
     */
    public function toValue($value) {
        return iconv($this->getInput(), $this->getOutput(), $value);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsType($type) {
        return Field::TYPE_CHARACTER === $type || Field::TYPE_MEMO === $type;
    }

}
