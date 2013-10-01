<?php

error_reporting(E_ALL | E_STRICT);
ini_set('display_errors',true);

require_once 'Node.php';

Node::startup();

$input = fopen('php://stdin', 'r');
$output = fopen('files/output.bin', 'w+');
$rest = 0;
$bits = 0;

while(($element = fread($input,1)) !== '') {
    $node = Node::getNode($element);

    if (!$node) {
        $path = Node::$NYT->getPath();
        $level = Node::$NYT->getLevel();
        $sendElement = true;
    } else {
        $path = $node->getPath();
        $level = $node->getLevel();
        $sendElement = false;
    }

    Node::addElement($element);

    $rest = ($rest << ($level +1)) | $path;
    $bits += $level+1;

    if ($sendElement) {
        $bits += 8;
        $rest = ($rest << 8) | ord($element);
    }

    while ($bits >= 8) {
        $aux = $rest >> ($bits - 8);
        $total = str_repeat(1, $bits - 8);
        $rest &= bindec($total);
        fwrite($output,pack('C*',$aux),1);
        $bits -= 8;
    }
}

if ($bits > 0) {
    $data = ($rest << (8 - $bits))  | bindec(str_repeat('1',8-$bits));
    fwrite($output,pack('c*',$data),1);
}
