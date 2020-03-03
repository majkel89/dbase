<?php
/**
 * Created by PhpStorm.
 * User: Michał Kowalik <maf.michal@gmail.com>
 * Date: 22.01.17 22:59
 */

namespace org\majkel\dbase\format;

use org\majkel\dbase\Field;
use org\majkel\dbase\field\MemoField;
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
            Field::TYPE_LOGICAL, Field::TYPE_MEMO, Field::TYPE_NUMERIC,Field::TYPE_TIME,Field::TYPE_INTEGER));
    }

    /**
     * @return string
     * @throws \org\majkel\dbase\Exception
     */
    protected function getRecordFormat() {
        if (is_null($this->recordFormat)) {
            $format = array('a1d');
            foreach ($this->getHeader()->getFields() as $i => $field) {
                if ($field instanceof MemoField) {
                    $format[] = 'V'  . 'f' . $i;
                } else {
                    $format[] = 'a' . $field->getLength() . 'f' . $i;
                }
            }
            $this->recordFormat = implode('/', $format);
        }
        return $this->recordFormat;
    }

    /**
     * @return string
     * @throws \org\majkel\dbase\Exception
     */
    protected function getWriteRecordFormat() {
        if (is_null($this->writeRecordFormat)) {
            $this->writeRecordFormat = 'a';
            foreach ($this->getHeader()->getFields() as $i => $field) {
                if ($field instanceof MemoField) {
                    $this->writeRecordFormat .= 'V';
                } else {
                    $this->writeRecordFormat .= 'A' . $field->getLength();
                }
            }
        }
        return $this->writeRecordFormat;
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
