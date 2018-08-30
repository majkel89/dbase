<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace org\majkel\dbase\memo;

use org\majkel\dbase\Exception;
use org\majkel\dbase\MemoFactory;

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
     * @param mixed $entryId
     * @return array
     * @throws \org\majkel\dbase\Exception
     */
    private function gotoEntry($entryId) {
        $filteredEntryId = $this->getFilteredEntryId($entryId);
        $file = $this->getFile();
        $entryOffset = $filteredEntryId * $this->getBlockSize();
        if ($filteredEntryId < 0 || $entryOffset + self::BH_SZ > $file->getSize()) {
            throw new Exception("Unable to move to block `$entryId`");
        } else if ($filteredEntryId === 0) {
            return array(false, 0);
        }
        $file->fseek($entryOffset);

        $bh = $file->fread(self::BH_SZ);
        $len = (ord($bh[4]) << 24) | (ord($bh[5]) << 16)
             | (ord($bh[6]) <<  8) |  ord($bh[7]);
        if ($len < 0 || (0xFFFFFFFF !== -1 && $len >= 0xFFFFFFFF)) {
            throw new Exception("Invalid block length (negative size)");
        } else if ($len === 0) {
            return array(false, 0);
        }

        return array($filteredEntryId, $len);
    }

    /**
     * {@inheritdoc}
     * @throws Exception
     */
    public function getEntry($entryId) {
        list($entryId, $len) = $this->gotoEntry($entryId);
        if ($entryId === false) {
            return '';
        }
        return $this->getFile()->fread($len);
    }

    /**
     * @param integer $dataLen
     * @return integer
     */
    private function lenPaddedBlockSize($dataLen) {
        $blockSize = $this->getBlockSize();
        return ceil(max($dataLen + self::BH_SZ, 1) / $blockSize) * $blockSize;
    }

    /**
     * @param integer|null $entryId
     * @param string       $data
     * @return integer
     * @throws \org\majkel\dbase\Exception
     */
    public function setEntry($entryId, $data) {
        $file = $this->getFile();
        $dataLen = strlen($data);
        if (is_null($entryId)) {
            $file->fseek(0, SEEK_END);
            $entryId = $this->getEntriesCount();
        } else {
            list($entryId, $len) = $this->gotoEntry($entryId);
            $total = $this->getEntriesCount();
            if ($this->lenPaddedBlockSize($len) < $dataLen + self::BH_SZ && $entryId < $total - 1) {
                $file->fseek(0, SEEK_END);
                $entryId = $total;
            } else {
                $file->fseek(-self::BH_SZ, SEEK_CUR);
            }
        }
        $file->fwrite(pack('NNa' . $dataLen . '@' . $this->lenPaddedBlockSize($dataLen), 1, $dataLen, $data));
        return $entryId;
    }

    /**
     * @return integer
     */
    public function getEntriesCount() {
        $dataSize = max(0, $this->getFile()->getSize());
        return (integer) floor($dataSize / $this->getBlockSize());
    }

    /**
     * @return string
     */
    public function getType() {
        return MemoFactory::TYPE_FPT;
    }

    /**
     * @return \org\majkel\dbase\memo\MemoInterface
     * @throws Exception
     */
    public function create() {
        parent::create();
        $this->blockSize = 512;
        $this->getFile()->fseek(0);
        $data = pack('Vvn@' . $this->getBlockSize(), 0, 0, $this->getBlockSize());
        $this->getFile()->fwrite($data);
        $this->setEntry(null, '');
        return $this;
    }
}
