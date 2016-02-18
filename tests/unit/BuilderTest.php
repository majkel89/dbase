<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace org\majkel\dbase;

use org\majkel\dbase\tests\utils\TestBase;

use ReflectionClass;

/**
 * Builder class tests
 *
 * @author majkel
 *
 * @coversDefaultClass \org\majkel\dbase\Builder
 */
class BuilderTest extends TestBase {

    /**
     * @covers ::setFormatType
     * @expectedException \org\majkel\dbase\Exception
     * @expectedExceptionMessage Format Format::AUTO is prohibited
     */
    public function testSetFormatTypeInvalidType() {
        $builder = new Builder();
        $builder->setFormatType(Format::AUTO);
    }

    /**
     * @covers ::getFields
     * @covers ::getField
     * @covers ::addField
     * @covers ::removeField
     */
    public function testManageFields() {
        $builder = new Builder();
        self::assertSame(0, count($builder->getFields()));
        $field = Field::create(Field::TYPE_CHARACTER)->setName('str');
        $builder->addField($field);
        self::assertSame($field, $builder->getField('str'));
        self::assertSame(1, count($builder->getFields()));
        $builder->removeField('str');
        self::assertSame(0, count($builder->getFields()));
    }

    /**
     * @covers ::create
     * @covers ::getHeader
     */
    public function testDefaultCreate() {
        $builder = Builder::create();
        self::assertSame(0, count($builder->getFields()));
    }

}
