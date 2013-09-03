<?php
if (!defined('BYTE_SIZE')) {
    define('BYTE_SIZE',8);
}

/**
 * Conjunto de bits
 *
 * @author Luiz Felipe Cunha <felipe.silvacunha@gmail.com>
 */
class Bitset {

    /**
     * Valor
     *
     * @var float
     * @access private
     */
    private $_value = 0;

    /**
     * Tamanho
     *
     * @var float
     * @access private
     */
    private $_size = 0;

    /**
     * Construtor do conjunto
     *
     * @author Luiz Felipe Cunha <felipe.silvacunha@gmail.com>
     *
     * @param int $value  Valor
     * @param mixed $size Tamanho
     * @access public
     */
    public function __construct($value,$size) {
        $this->_value = (int)$value;
        $this->_size = $size;
    }

    /**
     * Adiciona bits ao conjunto
     *
     * @author Luiz Felipe Cunha <felipe.silvacunha@gmail.com>
     *
     * @param int $value  Bits
     * @param mixed $size Quantidade de bits
     * @access public
     * @return null
     */
    public function addBits($value,$size) {
        $this->_value = ($value << $size) | $value;
        $this->_size += $size;
    }

    /**
     * Obtenção do tamanho do conjunto
     *
     * @author Luiz Felipe Cunha <felipe.silvacunha@gmail.com>
     *
     * @access public
     * @return int
     */
    public function getSize() {
        return $this->_size;
    }

    /**
     * Obtenção de um byte (8 bits)
     *
     * @author Luiz Felipe Cunha <felipe.silvacunha@gmail.com>
     *
     * @access public
     * @return int
     */
    public function getByte() {
        // menor ou igual a um byte?
        if ($this->_size <= BYTE_SIZE) {
            // resultado é todo o valor
            $value = $this->_value;
            // reseta o conjunto
            $this->_value = 0;
            $this->_size = 0;
        // maior que um byte
        } else {
            // pega o byte mais a direita
            $value = $this->_value >> ($this->_size - BYTE_SIZE);
            // atualiza os dados do conjunto
            $total = str_repeat(1,$this->_size - 8);
            $this->_value &= bindec($total);
            $this->_size -= BYTE_SIZE;
        }

        return $value;
    }

    /**
     * Convert o binário para uma string binária
     *
     * @author Luiz Felipe Cunha <felipe.silvacunha@gmail.com>
     *
     * @access public
     * @return string
     */
    public function __toString() {
        $value = decbin($this->_value);
        return str_pad($value,$this->_size,'0',STR_PAD_LEFT);
    }
}
