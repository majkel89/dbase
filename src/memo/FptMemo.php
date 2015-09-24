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
    private function getBlockSize() {
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
        $entryId = (integer) $entryId;
        if ($entryId === 0) {
            return '';
        }

        $file = $this->getFile();
        $file->fseek($entryId * $this->getBlockSize());

        $bh = $file->fread(self::BH_SZ);
        $len = (ord($bh[4]) << 24) + (ord($bh[5]) << 16)
             + (ord($bh[6]) <<  8) +  ord($bh[7]);
        if ($len < 0) {
            throw new Exception("Invalid block length (negative size)");
        }

        return $file->fread($len);
    }

}
