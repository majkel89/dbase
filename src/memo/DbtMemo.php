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
class DbtMemo extends AbstractMemo {

    const B_SZ = 512;

    /**
     * @param integer $entryId
     * @return int
     * @throws \org\majkel\dbase\Exception
     */
    private function gotoEntry($entryId) {
        $file = $this->getFile();
        $filteredEntryId = $this->getFilteredEntryId($entryId);
        if ($filteredEntryId < 0 || $filteredEntryId * self::B_SZ + self::B_SZ > $file->getSize()) {
            throw new Exception("Unable to move to block `$entryId`");
        }
        $file->fseek($filteredEntryId * self::B_SZ);
        return $filteredEntryId;
    }

    /**
     * {@inheritdoc}
     */
    public function getEntry($entryId) {
        $this->gotoEntry($entryId);
        return $this->getFile()->fread(self::B_SZ);
    }

    /**
     * @param integer|null $entryId
     * @param string       $data
     * @return integer
     * @throws \org\majkel\dbase\Exception
     */
    public function setEntry($entryId, $data) {
        $file = $this->getFile();
        if (is_null($entryId)) {
            $file->fseek(0, SEEK_END);
            $entryId = $this->getEntriesCount();
        } else {
            $entryId = $this->gotoEntry($entryId);
        }
        $file->fwrite(pack('a' . self::B_SZ, $data));
        return $entryId;
    }

    /**
     * @return integer
     */
    public function getEntriesCount() {
        $dataSize = max(0, $this->getFile()->getSize());
        return (integer) floor($dataSize / self::B_SZ);
    }

    /**
     * @return string
     */
    public function getType() {
        return MemoFactory::TYPE_DBT;
    }

    /**
     * @return $this
     */
    public function create() {
        parent::create();
        $this->setEntry(null, '');
        return $this;
    }
}
