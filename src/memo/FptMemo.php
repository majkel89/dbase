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
class FptMemo extends AbstractMemo {

    const TYPE_PICTURE = 0;
    const TYPE_TEXT = 1;

    const BH_SZ = 8;

    /** @var integer */
    private $blockSize;

    /**
     * @return integer;
     */
    protected function getBlockSize() {
        if (is_null($this->blockSize)) {
            $file = $this->getFile();
            $file->fseek(6);
            $bSz = $file->fread(2);
            $this->blockSize = (ord($bSz[0]) << 8) + ord($bSz[1]);
        }
        return $this->blockSize;
    }

    /**
     * {@inheritdoc}
     */
    public function getEntry($entryId) {
        $filteredEntryId = $this->getFilteredEntryId($entryId);
        $file = $this->getFile();
        $entryOffset = $filteredEntryId * $this->getBlockSize();
        if ($filteredEntryId < 0 || $entryOffset + self::BH_SZ > $file->getSize()) {
            throw new Exception("Unable to read block `$entryId`");
        } else if ($filteredEntryId === 0) {
            return '';
        }

        $file->fseek($entryOffset);

        $bh = $file->fread(self::BH_SZ);
        $len = (ord($bh[4]) << 24) | (ord($bh[5]) << 16)
             | (ord($bh[6]) <<  8) |  ord($bh[7]);
        if ($len < 0 || (0xFFFFFFFF !== -1 && $len >= 0xFFFFFFFF)) {
            throw new Exception("Invalid block length (negative size)");
        } else if ($len === 0) {
            return '';
        }

        return $file->fread($len);
    }

}
