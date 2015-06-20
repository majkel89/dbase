<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace org\majkel\dbase;

use Exception as StdException;

/**
 * Description of Exception
 *
 * @author majkel
 */
class Exception extends StdException {

    const INVALID_OFFSET = 1;
    const FILE_NOT_OPENED = 2;
    const READ_ONLY = 3;
    const UNSUPPORTED_VERSION = 4;

}
