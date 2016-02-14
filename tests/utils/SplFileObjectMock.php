<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace org\majkel\dbase\tests\utils;

use RecursiveIterator;
use SeekableIterator;
use resource;

/**
 * SplFileObject class sceleton used for mocking
 *
 * @author majkel
 */
class SplFileObjectMock implements RecursiveIterator, SeekableIterator {

    public $filename;
    public $open_mode;
    public $use_include_path;
    public $context;
    public $delimiter = ',';
    public $enclosure = '"';
    public $escape = '\\';
    public $flags = 0;

    /* Methods */
    public function __construct($filename, $open_mode = 'r', $use_include_path = false, $context = null) {
        $this->filename = $filename;
        $this->open_mode = $open_mode;
        $this->use_include_path = $use_include_path;
        $this->context = $context;
    }

    public function eof() {

    }

    public function fflush() {

    }

    public function fgetc() {

    }

    public function fgetcsv($delimiter = ',', $enclosure = '"', $escape = '\\') {

    }

    public function fgets() {

    }

    public function fgetss($allowable_tags) {

    }

    public function flock($operation, &$wouldblock = null) {

    }

    public function fpassthru() {

    }

    public function fputcsv($fields, $delimiter = ',', $enclosure = '"', $escape = '\\') {

    }

    public function fread($length) {

    }

    public function fscanf($format) {

    }

    public function fseek($offset, $whence = SEEK_SET) {

    }

    public function fstat() {

    }

    public function ftell() {

    }

    public function ftruncate($size) {

    }

    public function fwrite($str, $length = null) {

    }

    public function getCsvControl() {
        return array($this->delimiter, $this->enclosure, $this->escape);
    }

    public function getFlags() {
        return $this->flags;
    }

    public function getMaxLineLen() {

    }

    public function setCsvControl($delimiter = ',', $enclosure = '"', $escape = '\\') {
        $this->delimiter = $delimiter;
        $this->enclosure = $enclosure;
        $this->escape = $escape;
    }

    public function setFlags($flags) {
        $this->flags = $flags;
    }

    public function setMaxLineLen($max_len) {

    }

    /* Inherited methods */
    public function getATime() {

    }

    public function getBasename($suffix) {

    }

    public function getCTime() {

    }

    public function getExtension() {

    }

    public function getFileInfo($class_name = '\SplFileInfo') {

    }

    public function getFilename() {

    }

    public function getGroup() {

    }

    public function getInode() {

    }

    public function getLinkTarget() {

    }

    public function getMTime() {

    }

    public function getOwner() {

    }

    public function getPath() {

    }

    public function getPathInfo($class_name) {

    }

    public function getPathname() {

    }

    public function getPerms() {

    }

    public function getRealPath() {

    }

    public function getSize() {

    }

    public function getType() {

    }

    public function isDir() {

    }

    public function isExecutable() {

    }

    public function isFile() {

    }

    public function isLink() {

    }

    public function isReadable() {

    }

    public function isWritable() {

    }

    public function openFile($open_mode = 'r', $use_include_path = false, resource $context = null) {

    }

    public function setFileClass($class_name = '\SplFileObject') {

    }

    public function setInfoClass($class_name = '\SplFileInfo') {

    }

    public function __toString() {

    }

    public function current() {

    }

    public function getChildren() {

    }

    public function hasChildren() {

    }

    public function key() {

    }

    public function next() {

    }

    public function rewind() {

    }

    public function seek($position) {

    }

    public function valid() {

    }

}
