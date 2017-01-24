<?php
/**
 * Created by PhpStorm.
 * User: Michał Kowalik <maf.michal@gmail.com>
 * Date: 22.01.17 22:59
 */

namespace org\majkel\dbase\format;

use org\majkel\dbase\Field;
use org\majkel\dbase\Format;

/**
 * Class FoxPro
 *
 * @package org\majkel\dbase\format
 *
 * @author  Michał Kowalik <maf.michal@gmail.com>
 */
class FoxPro extends Format {

    const NAME = 'FoxPro';

    /**
     * {@inheritdoc}
     */
    protected function createHeader($data) {
        $header = parent::createHeader($data);
        return $header->setValid($header->getVersion() & $this->getVersion());
    }

    /**
     * {@inheritdoc}
     */
    public function supportsType($type) {
        return in_array($type, array(Field::TYPE_CHARACTER, Field::TYPE_DATE,
                                     Field::TYPE_LOGICAL, Field::TYPE_MEMO, Field::TYPE_NUMERIC));
    }

    /**
     * @return string
     */
    public function getType() {
        return Format::FOXPRO;
    }

    /**
     * @return integer
     */
    protected function getVersion() {
        return 0x30;
    }
}
