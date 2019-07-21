<?php
/**
 * Created by PhpStorm.
 * User: Michał Kowalik <maf.michal@gmail.com>
 * Date: 21.08.16 12:12
 */

namespace org\majkel\dbase;

use org\majkel\dbase\tests\utils\TestBase;

/**
 * Class ExtCaseInsensitiveGeneratorTest
 *
 * @package org\majkel\dbase
 *
 * @author  Michał Kowalik <maf.michal@gmail.com>
 *
 * @coversDefaultClass \org\majkel\dbase\ExtCaseInsensitiveGenerator
 */
class ExtCaseInsensitiveGeneratorTest extends TestBase
{
    /**
     * @test
     */
    public function testGenerator()
    {
        $extensions = array();
        $generator = new ExtCaseInsensitiveGenerator('some/FILE.php');
        foreach ($generator as $ext => $filePath) {
            if (!isset($extensions[$ext])) {
                $extensions[$ext] = 0;
            }
            $extensions[$ext]++;
            self::assertSame("some".DIRECTORY_SEPARATOR."FILE.$ext", $filePath);
        }
        self::assertSame(array(
            'php' => 1, 'PHP' => 1, 'pHP' => 1, 'PhP' => 1, 'phP' => 1, 'PHp' => 1, 'pHp' => 1, 'Php' => 1,
        ), $extensions);
    }

    /**
     * @test
     */
    public function testGeneratorNoExtension()
    {
        $generator = new ExtCaseInsensitiveGenerator('some/file/no-ext');
        $generator->rewind();
        self::assertTrue($generator->valid());
        self::assertSame('', $generator->key());
        self::assertSame('some/file/no-ext', $generator->current());
        $generator->next();
        self::assertFalse($generator->valid());
    }

    /**
     * @test
     */
    public function testGeneratorExtension4()
    {
        $extensions = array();
        foreach (new ExtCaseInsensitiveGenerator('some/file.abcd') as $ext => $filePath) {
            $extensions[$ext] = true;
        }
        self::assertSame(16, count($extensions));
    }

    /**
     * @test
     */
    public function testGeneratorExtension2()
    {
        $extensions = array();
        foreach (new ExtCaseInsensitiveGenerator('some/file.A') as $ext => $filePath) {
            $extensions[$ext] = true;
        }
        self::assertSame(array('a' => true, 'A' => true), $extensions);
    }

    /**
     * @test
     */
    public function testGeneratorLimit3()
    {
        $extensions = array();
        foreach (new ExtCaseInsensitiveGenerator('some/file.abcd', 3) as $ext => $filePath) {
            $extensions[$ext] = true;
        }
        self::assertSame(array(
            'abcd' => true,
            'ABCD' => true,
            'aBCD' => true,
        ), $extensions);
    }
}
