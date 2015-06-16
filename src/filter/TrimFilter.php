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
class TrimFilter extends Filter {

    /**
     * {@inheritdoc}
     */
    public function toValue($value) {
        return ltrim($value);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsType($type) {
        return Field::TYPE_CHARACTER === $type;
    }

}
