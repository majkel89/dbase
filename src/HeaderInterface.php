<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace org\majkel\dbase;

/**
 * Interface to access header information
 *
 * @author majkel
 */
interface HeaderInterface {

    /**
     * @return \org\majkel\dbase\Field[]
     */
    public function getFields();

    /**
     * @return string[]
     */
    public function getFieldsNames();

    /**
     * @return integer
     */
    public function getVersion();

    /**
     * @return \DateTime
     */
    public function getLastUpdate();

    /**
     * @return integer
     */
    public function getFieldsCount();

    /**
     * @return boolean
     */
    public function isPendingTransaction();

    /**
     * @return integer
     */
    public function getRecordsCount();

    /**
     * @return integer
     */
    public function getRecordSize();

    /**
     * @return integer
     */
    public function getHeaderSize();

    /**
     * @return \org\majkel\dbase\Field
     */
    public function getField($indexOrName);
}
