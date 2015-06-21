<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$stdin = fopen('php://stdin', 'r');
$data = fread($stdin, 10 * 32);
fclose($stdin);

$len = strlen($data);

for ($i = 0; $i < $len; ++$i) {
    if (($i % 32) === 0) {
        if ($i > 0) {
            echo "\"\n";
        }
        echo '"';
    }
    $char = $data[$i];
    $ord = ord($char);
    if ($ord >= 32 && $ord < 127) {
        if ($char === '\\') {
            echo '\\';
        }
        echo $char;
    } else {
        echo '\x' . str_pad(strtoupper(dechex($ord)), 2, '0', STR_PAD_LEFT);
    }
}
echo '"';
