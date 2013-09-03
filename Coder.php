<?php

define('BYTE_SIZE',8);

require_once "Tree.php";
require_once "Bitset.php";

class Coder {

    protected $_tree = null;

    protected $_inputFile = null;
    protected $_outputFile = null;



    public function __construct($inputFile,$outputFile) {
        $this
            ->setInputFile($inputFile)
            ->setOutputFile($outputFile);
        $this->_tree = new Tree();
    }

    protected function _validateFile($file) {
        if (!file_exists($file)) {
            throw new Exception(sprintf('"%s" not exists!',$file));
        }

        if (is_dir($file)) {
            throw new Exception(sprintf('"%s" is not a valid file!',$file));
        }

        if (!is_readable($file)) {
            throw new Exception(sprintf('Cannot read file at "%s"! Please give permission!',$file));
        }

    }

    public function setOutputFile($file) {
        $file = (string)$file;
        $this->_validateFile($file);
        if (!is_writable($file)) {
            throw new Exception(sprintf('Cannot write file at "%s"! Please give permission!',$file));
        }
        $this->_outputFile = array(
            'file' => fopen($file,'wb'),
            'size' => filesize($file)
        );
        return $this;
    }

    public function setInputFile($file) {
        $file = (string)$file;
        $this->_validateFile($file);
        $this->_inputFile = array(
            'file' => fopen($file,'rb'),
            'size' => filesize($file)
        );
        return $this;
    }

    public function run() {
        $input = $this->_inputFile;
        $output = $this->_outputFile;
        $i=0;
        $tree = $this->_tree;
        $buffer = new Bitset(0,0);
        do {
            fseek($input['file'],$i++);
            $char = fread($input['file'],1);
            echo $char;
            $node = $tree->getNode($char);
            if (!$node) {
                $path = $tree->getNYT()->getPath();
                $level = $tree->getNYT()->getLevel();
                $addChar = true;
            } else {
                $path = $node->getPath();
                $level = $node->getLevel();
                $addChar = false;
            }
            $tree->addElement($char);
            $buffer->addBits($path,$level+1);
            if ($addChar) {
                $buffer->addBits(ord($char),8);
            }

            while ($buffer->getSize() >= BYTE_SIZE) {
                $this->_writeByte($buffer->getByte());
            }
        } while($i < $input['size']);

        while ($buffer->getSize() > 0) {
            $this->_writeByte($buffer->getByte());
        }
    }

    protected function _writeByte($byte) {
        fwrite($this->_outputFile['file'],pack('C*',(int)$byte),1);
        return $this;
    }
}

$coder = new Coder('files/input.txt','files/output.bin');
$coder->run();
