<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace org\majkel\dbase\field;

use org\majkel\dbase\tests\utils\AbstractFieldTest;
use org\majkel\dbase\Field;
use DateTime;

/**
 * Record class tests
 *
 * @author majkel
 *
 * @coversDefaultClass \org\majkel\dbase\field\DateField
 */
class DateFieldTest extends AbstractFieldTest {

    const CLS = '\org\majkel\dbase\field\DateField';
    const TYPE = Field::TYPE_DATE;

    /**
     * {@inheritdoc}
     */
    public function dataFromData() {
        $data = new DateTime();
        $data->setDate(2015, 06, 12);
        $data->setTime(0, 0, 0);
        return [
            ['20150612', $data],
            ['invalid', (new DateTime)->setTimestamp(0)],
        ];
    }

    /**
     * @param mixzed $data
     * @param DateTime $expected
     * @dataProvider dataFromData
     */
    public function testFromData($data, $expected) {
        $result = $this->getFieldObject()->fromData($data);
        self::assertSame($expected->getTimestamp(), $result->getTimestamp());
    }

    /**
     * {@inheritdoc}
     */
    public function dataToData() {
        $date = new DateTime();
        $date->setDate(2015, 06, 12);
        return [
            [$date, '20150612'],
            ['2015-06-12 12:11:13', '20150612'],
            [$date->getTimestamp(), '20150612'],
        ];
    }

}
