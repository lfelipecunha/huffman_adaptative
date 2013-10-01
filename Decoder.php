<?php

error_reporting(E_ALL | E_STRICT);
ini_set('display_errors',true);

require_once "Node.php";

Node::startup();

$input = fopen('files/output.bin','rb');
$out = fopen('php://stdout','w');

$resto = '';
$verificarExiste = true;
$i=0;
$j=0;
$texto = '';
do {
    if (!empty($resto)) {
        $data = $resto;
        $result = Node::getNodeFromPath($data);
    } else {
        $result = true;
    }

    while($result && !$result['node']) {
        fseek($input,$i++);
        $data = fread($input,1);
        $data = unpack('C*',$data);
        $data = str_pad(decbin(reset($data)) ,8,0,STR_PAD_LEFT);
        $data = $resto . $data;
        $result = Node::getNodeFromPath($data);
        if ($result && !$result['node']) {
            $resto = $data;
        }
    }

    if (!$result) {
        break;
    }

    $resto = $result['path'];
    $node = $result['node'];
    if ($node->isEqual(Node::$NYT)) {
        if (strlen($resto) < 8) {
            fseek($input,$i++);
            $test = fread($input,1);
            $test = unpack('C*',$test);
            $test = str_pad(decbin(reset($test)),8,0,STR_PAD_LEFT);
            $resto .= $test;
        }
        $element = substr($resto,0,8);
        $element = chr(bindec($element));
        $texto .= $element;
        Node::addElement($element);
        $resto = substr($resto,8);
    } else {
        $element = $node->getValue();
        Node::addElement($element);
        $texto .= $element;
    }

    fwrite($out,$element);

} while($i <= filesize('files/output.bin'));

