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
 * Record class tests
 *
 * @author majkel
 *
 * @coversDefaultClass \org\majkel\dbase\Field
 */
class FieldTest extends TestBase {

    /**
     * @covers ::addFilter
     * @covers ::getFilters
     */
    public function testAddFilter() {
        $field = $this->getFieldMock();
        $filter = $this->mock(self::CLS_FILTER)
            ->supportsType([$field->getType()], true, self::once())
            ->new();
        self::assertSame([], $field->getFilters());
        self::assertSame($field, $field->addFilter($filter));
        self::assertSame([$filter], $field->getFilters());
    }

    /**
     * @covers ::addFilter
     * @covers ::getType
     * @covers ::getFilters
     */
    public function testAddFilterDoesNotSupport() {
        $field = $this->getFieldMock();
        $filter = $this->mock(self::CLS_FILTER)
            ->supportsType([$field->getType()], false, self::once())
            ->new();
        $field->addFilter($filter);
        self::assertSame([], $field->getFilters());
    }

    /**
     * @return array
     */
    public function dataAddFilters() {
        $fA = $this->getFilterMock();
        $fB = $this->getFilterMock();
        return [
            [[$fA, $fB, $fA], [$fA, $fB, $fA]],
            [new \ArrayIterator([$fA, $fB, $fA]), [$fA, $fB, $fA]],
            [false, []],
            [null, []],
            [123, []],
            [new \stdClass(), []],
        ];
    }

    /**
     * @covers ::addFilters
     * @covers ::addFilter
     * @covers ::getFilters
     * @dataProvider dataAddFilters
     */
    public function testAddFilters($filters, $excepted) {
        $field = $this->getFieldMock();
        self::assertSame($field, $field->addFilters($filters));
        self::assertSame($excepted, $field->getFilters());
    }

    /**
     * @covers ::addFilter
     * @covers ::removeFilter
     * @covers ::getFilters
     */
    public function testRemoveFilterByIndex() {
        $field = $this->getFieldMock();
        $filter = $this->getFilterMock();
        $field->addFilter($filter);
        self::assertSame($field, $field->removeFilter(0));
        self::assertSame([], $field->getFilters());
    }

    /**
     * @covers ::addFilter
     * @covers ::removeFilter
     * @covers ::getFilters
     */
    public function testRemoveFilterByIndexDoesNotExists() {
        $field = $this->getFieldMock();
        $filter = $this->getFilterMock();
        $field->addFilter($filter);
        self::assertSame($field, $field->removeFilter(66));
        self::assertSame([$filter], $field->getFilters());
    }

    /**
     * @covers ::addFilter
     * @covers ::removeFilter
     * @covers ::getFilters
     */
    public function testRemoveFilterByObject() {
        $field = $this->getFieldMock();
        $filter = $this->getFilterMock();
        $field->addFilter($filter);
        self::assertSame($field, $field->removeFilter($filter));
        self::assertSame([], $field->getFilters());
    }

    /**
     * @covers ::addFilter
     * @covers ::removeFilter
     * @covers ::getFilters
     */
    public function testRemoveFilterByObjectDoestNotExists() {
        $field = $this->getFieldMock();
        $fA = $this->getFilterMock();
        $fB = $this->getFilterMock();
        $field->addFilter($fA);
        self::assertSame($field, $field->removeFilter($fB));
        self::assertSame([$fA], $field->getFilters());
    }

    /**
     * @covers ::setName
     * @covers ::getName
     */
    public function testSetName() {
        $field = $this->getFieldMock();
        self::assertSame($field, $field->setName('NAME'));
        self::assertSame('NAME', $field->getName());
    }

    /**
     * @covers ::setName
     * @expectedException \org\majkel\dbase\Exception
     * @expectedExceptionMessage Field name cannot be longer than 10 characters
     */
    public function testSetNameTooLarge() {
        $this->getFieldMock()->setName('VERY_LARGE_NAME');
    }

    /**
     * @covers ::setLength
     * @covers ::getLength
     */
    public function testSetLength() {
        $field = $this->getFieldMock();
        self::assertSame($field, $field->setLength('123'));
        self::assertSame(123, $field->getLength());
    }

    /**
     * @covers ::setLoad
     * @covers ::isLoad
     */
    public function testSetLoad() {
        $field = $this->getFieldMock();
        self::assertTrue($field->isLoad());
        self::assertSame($field, $field->setLoad(''));
        self::assertFalse($field->isLoad());
    }

    /**
     * @covers ::unserialize
     */
    public function testUnserialize() {
        $fA = $this->mock(self::CLS_FILTER)
            ->toValue(['FROM_DATA'], 'FROM_FA', self::once())
            ->supportsType(true)
            ->new();
        $fB = $this->mock(self::CLS_FILTER)
            ->toValue(['FROM_FA'], 'FROM_FB', self::once())
            ->supportsType(true)
            ->new();
        $field = $this->mock(self::CLS_FIELD)
            ->fromData(['IN_DATA'], 'FROM_DATA', self::once())
            ->toData()
            ->getType()
            ->getFilters([], [$fA, $fB], self::once())
            ->new();
        self::assertSame('FROM_FB', $field->unserialize('IN_DATA'));
    }

    /**
     * @covers ::serialize
     */
    public function testSerialize() {
        $fB = $this->mock(self::CLS_FILTER)
            ->fromValue(['FROM_FB'], 'FROM_FA', self::once())
            ->supportsType(true)
            ->new();
        $fA = $this->mock(self::CLS_FILTER)
            ->fromValue(['FROM_FA'], 'FROM_DATA', self::once())
            ->supportsType(true)
            ->new();
        $field = $this->mock(self::CLS_FIELD)
            ->fromData()
            ->toData(['FROM_DATA'], 'IN_DATA', self::once())
            ->getType()
            ->getFilters([], [$fA, $fB], self::once())
            ->new();
        self::assertSame('IN_DATA', $field->serialize('FROM_FB'));
    }

    /**
     * @return array
     */
    public function dataCreate() {
        return [
            [Field::TYPE_CHARACTER, '\org\majkel\dbase\Field\CharacterField', false],
            [Field::TYPE_DATE, '\org\majkel\dbase\Field\DateField', false],
            [Field::TYPE_LOGICAL, '\org\majkel\dbase\Field\LogicalField', false],
            [Field::TYPE_MEMO, '\org\majkel\dbase\Field\MemoField', true],
            [Field::TYPE_NUMERIC, '\org\majkel\dbase\Field\NumericField', false],
        ];
    }

    /**
     * @dataProvider dataCreate
     * @covers ::create
     * @covers ::isMemoEntry
     * @covers \org\majkel\dbase\field\MemoField::isMemoEntry
     */
    public function testCreate($type, $class, $isMemoEntry) {
        $field = Field::create($type);
        self::assertInstanceOf($class, $field);
        self::assertSame($isMemoEntry, $field->isMemoEntry());
    }

    /**
     * @covers ::create
     * @expectedException \org\majkel\dbase\Exception
     * @expectedExceptionMessage Unsupported field `UNKNOWN`
     */
    public function testCreateUnknown() {
        Field::create('UNKNOWN');
    }

    /**
     * @covers ::getTypes
     */
    public function testGetTypes() {
        $reflection = new ReflectionClass(self::CLS_FIELD);
        $types = Field::getTypes();
        $constants = $reflection->getConstants();
        foreach ($constants as $name => $value) {
            if (strpos($name, 'TYPE_') === 0) {
                if (!in_array($value, $types)) {
                    self::fail("Does not return `$name` => `$value`");
                }
            }
        }
        self::assertTrue(true);
    }
}
