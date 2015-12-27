<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace org\majkel\dbase\memo;

use org\majkel\dbase\File;

/**
 * Description of newPHPClass
 *
 * @author majkel
 */
abstract class AbstractMemo implements MemoInterface {

    /** @var \SplFileObject */
    private $file;

    /**
     * @return \SplFileObject
     */
    protected function getFile() {
        return $this->file;
    }

    /**
     *
     * @param string $path
     * @param string $mode
     */
    public function __construct($path, $mode) {
        $this->file = File::getObject($path, $mode);
    }

    /**
     * @return \SplFileInfo
     */
    public function getFileInfo() {
        return $this->getFile()->getFileInfo();
    }

    /**
     * @param string $entryId
     * @return integer
     */
    protected function getFilteredEntryId($entryId) {
        if (is_numeric($entryId)) {
            return (integer) $entryId;
        } else {
            $entryId = trim($entryId);
            return empty($entryId) ? 0 : -1;
        }
    }

}
