<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace org\majkel\dbase\field;

use org\majkel\dbase\tests\utils\AbstractFieldTest;
use org\majkel\dbase\Field;

/**
 * Record class tests
 *
 * @author majkel
 *
 * @coversDefaultClass \org\majkel\dbase\field\CharacterField
 */
class CharacterFieldTest extends AbstractFieldTest {

    const CLS = '\org\majkel\dbase\field\CharacterField';
    const TYPE = Field::TYPE_CHARACTER;

    /**
     * @return \org\majkel\dbase\Field
     */
    protected function getFieldObject() {
        return parent::getFieldObject()->setLength(4);
    }

    /**
     * {@inheritdoc}
     */
    public function dataFromData() {
        return array(
            array(" data \0\0\0\0\0", ' data'),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function dataToData() {
        return array(
            array('dataSome', 'data'),
        );
    }

    /**
     * @return integer
     */
    public function getDefaultLength() {
        return 1;
    }
}
