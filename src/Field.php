<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace org\majkel\dbase;

/**
 * Description of Field
 *
 * @author majkel
 */
class Field {

    const TYPE_BOOLEAN = 'B';
    // ...

    /** @var string */
    protected $name;
    /** @var integer */
    protected $type;
    /** @var integer */
    protected $length;

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @return integer
     */
    public function getType() {
        return $this->type;
    }

    /**
     * @return integer
     */
    public function getLength() {
        return $this->length;
    }

    /**
     * @param \SplFileObject $file
     * @return void
     */
    public function loadFromFile($file) {
        $data = unpack('a11n/Ct/Vrr1/Cll/Cdd/vrr2/Cwa/vrr3/Csff/Crr4', $file->fread(32));
        $this->name = rtrim($data['n']);
        $this->type = chr($data['t']);
        $this->length = $data['ll'];
    }
}
