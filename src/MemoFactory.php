<?php
/**
 * User: Michał (majkel) Kowalik <maf.michal@gmail.com>
 * Date: 13-Feb-16
 * Time: 16:53
 */

namespace org\majkel\dbase;

/**
 * Class MemoFactory
 *
 * @package org\majkel\dbase
 * @author  Michał (majkel) Kowalik <maf.michal@gmail.com>
 */
class MemoFactory {

    /** @var callable[] [ext => generator, ...] */
    private $formats = [];

    /** @var \org\majkel\dbase\MemoFactory */
    private static $instance;

    /**
     * @param string    $ext
     * @param callable $generator
     * @return void
     */
    public function registerFormat($ext, callable $generator) {
        $this->formats[$ext] = $generator;
    }

    /**
     * @param string $ext
     * @return void
     */
    public function unregisterFormat($ext) {
        unset($this->formats[$ext]);
    }

    /**
     * @return \callable[]
     */
    public function getFormats() {
        return $this->formats;
    }

    /**
     * @param \org\majkel\dbase\Format $format
     * @param string                   $ext
     * @return string
     */
    public function getMemoPathForDbf(Format $format, $ext) {
        $fileInfo =  $format->getFileInfo();
        $path = $fileInfo->getPath() . '/';
        $basename = $fileInfo->getBasename();
        $index = stripos($basename, '.dbf');
        if (strlen($basename) - 4 === $index) {
            $path .= substr($basename, 0, $index);
        } else {
            $path .= $basename;
        }
        return $path . '.' . $ext;
    }

    /**
     * @param \org\majkel\dbase\Format $format
     * @return \org\majkel\dbase\memo\MemoInterface
     * @throws \org\majkel\dbase\Exception
     */
    public function getMemoForDbf(Format $format) {
        foreach ($this->getFormats() as $ext => $generator) {
            $filePath = $this->getMemoPathForDbf($format, $ext);
            if (is_readable($filePath)) {
                return $this->getMemo($filePath, $format->getMode(), $ext);
            }
        }
        throw new Exception("Unable to open memo file");
    }

    /**
     * @param string      $path
     * @param integer     $mode
     * @param string|null $ext
     * @return \org\majkel\dbase\memo\MemoInterface
     * @throws \org\majkel\dbase\Exception
     */
    public function getMemo($path, $mode, $ext = null) {
        if (is_null($ext)) {
            $ext = strtolower((new \SplFileInfo($path))->getExtension());
        }
        $formats = $this->getFormats();
        if (isset($formats[$ext])) {
            return $formats[$ext]($path, $mode);
        }
        throw new Exception("Unable to determine memo format");
    }

    /**
     * @return void
     */
    public function initializeFormats() {
        $this->formats = [];
        $this->registerFormat('dbt', function($path, $mode){
            return new memo\DbtMemo($path, $mode);
        });
        $this->registerFormat('fpt', function ($path, $mode) {
            return new memo\FptMemo($path, $mode);
        });
    }

    /**
     * @return \org\majkel\dbase\MemoFactory
     */
    public static function getInstance() {
        if (is_null(self::$instance)) {
            self::$instance = new self();
            self::$instance->initializeFormats();
        }
        return self::$instance;
    }

    /**
     * @param \org\majkel\dbase\MemoFactory $instance
     * @return void
     */
    public static function setInstance(MemoFactory $instance = null) {
        self::$instance = $instance;
    }
}
