<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace org\majkel\dbase\memo;

use org\majkel\dbase\Exception;

/**
 * Description of FptMemo
 *
 * @author majkel
 */
class DbtMemo extends AbstractMemo {

    const B_SZ = 512;

    /**
     * {@inheritdoc}
     */
    public function getEntry($entryId) {
        $filteredEntryId = $this->getFilteredEntryId($entryId);
        $file = $this->getFile();
        if ($filteredEntryId < 0 || $filteredEntryId * self::B_SZ + self::B_SZ > $file->getSize()) {
            throw new Exception("Unable to read block `$entryId`");
        }
        $file->fseek($filteredEntryId * self::B_SZ);
        return $file->fread(self::B_SZ);
    }

}
