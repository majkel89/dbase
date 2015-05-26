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

    /**
     * @var string[]
     */
    protected static $messages = [
        self::INVALID_OFFSET => "Offset %1 does not exists",
        self::FILE_NOT_OPENED => "File is not opened",
        self::READ_ONLY => "Table is opened in read only mode",
        self::UNSUPPORTED_VERSION => "Only version 5 and 7 are supported",
    ];

    /**
     * @param integer $code
     * @throws Exception
     */
    public static function raise($code) {
        $arguments = func_get_args();
        $arguments[0] = self::$messages[$code];
        $message = call_user_func_array('sprintf', $arguments);
        throw new Exception($message, $code);
    }

}
