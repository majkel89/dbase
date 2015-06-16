<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace org\majkel\dbase;

/**
 * @author majkel
 */
class FormatFactory {

    /**
     * Registered formats. CODE => generator.
     * @var callable[]
     */
    protected $formats;

    /**
     * Returns new format object
     * @param string $name
     * @param string $filePath
     * @param integer $mode
     * @return \org\majkel\dbase\Format
     * @throws Exception
     */
    public function getFormat($name, $filePath, $mode) {
        $this->initializeFormats();
        if (!isset($this->formats[$name])) {
            throw new Exception("Format `$name` is not registered");
        }
        $format = $this->formats[$name]($filePath, $this->getMode($mode));
        if (!$format instanceof Format) {
            throw new Exception("Cannot generate format `$name`");
        }
        return $format;
    }

    /**
     * Returns all avileable formats
     * @return string[]
     */
    public function getFormats() {
        $this->initializeFormats();
        return array_keys($this->formats);
    }

    /**
     * Registers new format
     * @param string $name
     * @param callable $generator
     * @return \org\majkel\dbase\FormatFactory
     */
    public function registerFormat($name, callable $generator) {
        $this->initializeFormats();
        $this->formats[$name] = $generator;
        return $this;
    }

    /**
     * Unregisteres format
     * @param string $name
     * @return \org\majkel\dbase\FormatFactory
     */
    public function unregisterFormat($name) {
        $this->initializeFormats();
        unset($this->formats[$name]);
        return $this;
    }

    /**
     * @param int $mode
     * @return string
     */
    protected function getMode($mode) {
        return $mode & Table::MODE_WRITE ? 'rb+' : 'rb';
    }

    /**
     * Initializes formats
     * @return \org\majkel\dbase\FormatFactory
     */
    protected function initializeFormats() {
        if (is_null($this->formats)) {
            $this->formats = [];
            $this->registerFormat(Format::DBASE3, function ($filePath, $mode) {
                return new format\DBase3($filePath, $mode);
            });
            $this->registerFormat(Format::AUTO, function ($filePath, $mode) {
                foreach ($this->formats as $generator) {
                    try {
                        $format = $generator($filePath, $this->getMode($mode));
                        if ($format->isValid()) {
                            return $format;
                        }
                    }
                    catch (\Exception $e) {

                    }
                }
                throw new Exception("Unable detect format for file `$filePath`");
            });
        }
        return $this;
    }
}
