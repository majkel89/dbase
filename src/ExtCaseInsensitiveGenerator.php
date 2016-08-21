<?php
/**
 * Created by PhpStorm.
 * User: Michał Kowalik <maf.michal@gmail.com>
 * Date: 21.08.16 11:42
 */

namespace org\majkel\dbase;

/**
 * Class ExtCaseInsensitiveGenerator
 *
 * @package org\majkel\dbase
 *
 * @author  Michał Kowalik <maf.michal@gmail.com>
 */
class ExtCaseInsensitiveGenerator implements \Iterator
{
    /** @var \SplFileInfo */
    private $fileInfo;
    /** @var string */
    private $basePath;
    /** @var int */
    private $state;
    /** @var string */
    private $ext;
    /** @var int */
    private $modulo;
    /** @var int */
    private $limit;

    /**
     * ExtCaseInsensitiveGenerator constructor.
     *
     * @param string $filePath
     * @param int    $limit
     */
    public function __construct($filePath, $limit = 0)
    {
        $this->fileInfo = new \SplFileInfo($filePath);
        $this->limit = (int) $limit;
    }

    /**
     * @return string
     */
    private function generateExt()
    {
        $ext = strtolower($this->fileInfo->getExtension());
        $extLen = strlen($ext);
        for ($i = 0; $i < $extLen; $i++) {
            if ($this->state & (1 << $i)) {
                $ext[$i] = strtoupper($ext[$i]);
            }
        }
        return $ext;
    }

    /**
     * Return the current element
     *
     * @link  http://php.net/manual/en/iterator.current.php
     * @return string|null Can return any type.
     * @since 5.0.0
     */
    public function current()
    {
        return $this->valid() ? "{$this->basePath}{$this->key()}" : null;
    }

    /**
     * Move forward to next element
     *
     * @link  http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function next()
    {
        $this->state = ($this->state - 1) & $this->modulo;
        $this->limit--;
        if ($this->limit == 0) {
            $this->state = null;
        }
        $this->ext = null;
    }

    /**
     * Return the key of the current element
     *
     * @link  http://php.net/manual/en/iterator.key.php
     * @return string scalar on success, or null on failure.
     * @since 5.0.0
     */
    public function key()
    {
        if (is_null($this->ext)) {
            $this->ext = $this->generateExt();
        }
        return $this->ext;
    }

    /**
     * Checks if current position is valid
     *
     * @link  http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     *        Returns true on success or false on failure.
     * @since 5.0.0
     */
    public function valid()
    {
        return !is_null($this->state);
    }

    /**
     * Rewind the Iterator to the first element
     *
     * @link  http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function rewind()
    {
        $ext = $this->fileInfo->getExtension();
        $this->state = 0;
        $this->limit = $this->limit ? $this->limit : (1 << strlen($ext));
        $this->modulo = max((1 << strlen($ext)) - 1, 0);
        $this->ext = null;
        $this->basePath = $this->fileInfo->getPath() . DIRECTORY_SEPARATOR . $this->fileInfo->getBasename($ext);
    }
}
