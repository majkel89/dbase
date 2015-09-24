<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace org\majkel\dbase\memo;

/**
 * Description of newPHPClass
 *
 * @author majkel
 */
interface IMemo {

    /**
     * @return \SplFileInfo
     */
    public function getFileInfo();

    /**
     * Reads entry from memo file
     * @param string $entryId
     * @return nixed
     */
    public function getEntry($entryId);

}
