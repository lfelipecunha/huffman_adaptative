<?php

/**
 * Obtjeto de nodo
 *
 * @author Luiz Felipe Cunha <felipe.silvacunha@gmail.com>
 */
class Node {

    /**
     * Constante para identificação de posicionamento à esquerda
     */
    const LEFT = 'left';

    /**
     * Constante para identificação de posicionamento à direita
     */
    const RIGHT = 'right';

    /**
     * Nodo filho à esquerda
     *
     * @var Node
     * @access protected
     */
    protected $_leftNode = null;

    /**
     * Nodo filho à direita
     *
     * @var Node
     * @access protected
     */
    protected $_rightNode = null;

    /**
     * Nodo pai
     *
     * @var Node
     * @access protected
     */
    protected $_parent = null;

    /**
     * Peso
     *
     * @var int
     * @access protected
     */
    protected $_weight = 0;

    /**
     * Nível
     *
     * @var int
     * @access protected
     */
    protected $_level = 0;

    /**
     * Valor do nodo
     *
     * @var mixed
     * @access protected
     */
    protected $_value = null;

    /**
     * Caminho para o nodo
     *
     * @var float
     * @access protected
     */
    protected $_path = 0;

    /**
     * Identificador do nodo
     *
     * @var int
     * @access protected
     */
    protected $_ID;

    /**
     * esquerda
     *
     * @var int
     * @access protected
     */
    protected $left = 0;

    /**
     * direita
     *
     * @var int
     * @access protected
     */
    protected $_right = 1;

    /**
     * Posição do nodo
     *
     * @var string
     * @access protected
     */
    protected $_position;



    /**
     * Controle de identificador de nodo
     *
     * @var int
     * @access private
     */
    private static $_geralID = 0;

    /**
     * Construtor da classe
     *
     * @author Luiz Felipe Cunha <felipe.silvacunha@gmail.com>
     *
     * @param string $value
     * @access public
     * @return null
     */
    public function __construct($value = '') {
        $this->_value = $value;
        $this->_ID = self::$_geralID;
        self::$_geralID++;
    }

    /**
     * Obtenção do caminho para o nodo
     *
     * @author Luiz Felipe Cunha <felipe.silvacunha@gmail.com>
     *
     * @access public
     * @return null
     */
    public function getPath() {
        return $this->_path;
    }

    /**
     * Define o caminho para o nodo
     *
     * @author Luiz Felipe Cunha <felipe.silvacunha@gmail.com>
     *
     * @param string $path
     * @access public
     * @return Node Próprio Objeto para encadeamento
     */
    public function setPath($path) {
        $this->_path = $path;
        return $this;
    }

    /**
     * isEqual
     *
     * @author Luiz Felipe Cunha <felipe.silvacunha@gmail.com>
     *
     * @param mixed $node
     * @access public
     * @return null
     */
    public function isEqual($node) {
        return $this->_ID == $node->getID();
    }

    /**
     * Obtenção do valor do nodo
     *
     * @author Luiz Felipe Cunha <felipe.silvacunha@gmail.com>
     *
     * @access public
     * @return mixed
     */
    public function getValue() {
        return $this->_value;
    }

    /**
     * Define o peso da ocorrência do nodo
     *
     * @author Luiz Felipe Cunha <felipe.silvacunha@gmail.com>
     *
     * @param int $weight
     * @access public
     * @return Node Próprio Objeto para encadeamento
     */
    public function setWeight($weight) {
        $this->_weight = (int)$weight;
        return $this;
    }

    /**
     * Obtenção do peso da ocorrência do nodo
     *
     * @author Luiz Felipe Cunha <felipe.silvacunha@gmail.com>
     *
     * @access public
     * @return int
     */
    public function getWeight() {
        return $this->_weight;
    }

    /**
     * Define o nodo pai
     *
     * @author Luiz Felipe Cunha <felipe.silvacunha@gmail.com>
     *
     * @param Node $parent     Novo Pai
     * @param string $position Posição no pai
     * @param bool $upParent   O pai deve ser atualizado?
     * @access public
     * @return Node Próprio Objeto para encadeamento
     */
    public function setParent(Node $parent,$position,$upParent = true) {
        $this->_parent = $parent;
        $this->setPosition($position);
        if ($upParent) {
            if ($position == self::LEFT) {
                $parent->setLeftNode($this);
            } else {
                $parent->setRightNode($this);
            }
        }
        return $this;
    }

    /**
     * Obtenção do pai
     *
     * @author Luiz Felipe Cunha <felipe.silvacunha@gmail.com>
     *
     * @access public
     * @return Node
     */
    public function getParent() {
        return $this->_parent;
    }

    /**
     * Define o nodo filho da esquerda
     *
     * @author Luiz Felipe Cunha <felipe.silvacunha@gmail.com>
     *
     * @param Node $node
     * @access public
     * @return Node Próprio Objeto para encadeamento
     */
    public function setLeftNode(Node $node) {
        $node->setParent($this,self::LEFT,false);
        $this->_leftNode = $node;
        return $this;
    }

    /**
     * Obtenção do nodo filho da esquerda
     *
     * @author Luiz Felipe Cunha <felipe.silvacunha@gmail.com>
     *
     * @access public
     * @return Node
     */
    public function getLeftNode() {
        return $this->_leftNode;
    }

    /**
     * Define o nodo filho da direita
     *
     * @author Luiz Felipe Cunha <felipe.silvacunha@gmail.com>
     *
     * @param Node $node
     * @access public
     * @return Node
     */
    public function setRightNode(Node $node) {
        $node->setParent($this,self::RIGHT,false);
        $this->_rightNode = $node;
        return $this;
    }

    /**
     * Obtenção do nodo filho da direita
     *
     * @author Luiz Felipe Cunha <felipe.silvacunha@gmail.com>
     *
     * @access public
     * @return Node
     */
    public function getRightNode() {
        return $this->_rightNode;
    }

    /**
     * Define o nível do Nodo
     *
     * @author Luiz Felipe Cunha <felipe.silvacunha@gmail.com>
     *
     * @param int $level
     * @access public
     * @return Node Próprio Objeto para encadeamento
     */
    public function setLevel($level) {
        $this->_level = $level;

        // realiza varredura dos filhos para a realização da atualização
        $right = $this->getRightNode();
        if ($right) {
            $right->setLevel($level+1);
        }

        $left = $this->getLeftNode();
        if ($left) {
            $left->setLevel($level+1);
        }
        return $this;
    }

    /**
     * Obtenção do nível do Nodo
     *
     * @author Luiz Felipe Cunha <felipe.silvacunha@gmail.com>
     *
     * @access public
     * @return int
     */
    public function getLevel() {
        return $this->_level;
    }

    /**
     * Obtenção da posição do nodo
     *
     * @author Luiz Felipe Cunha <felipe.silvacunha@gmail.com>
     *
     * @access public
     * @return null
     */
    public function getPosition() {
        return $this->_position;
    }

    /**
     * Define a posição do nodo
     *
     * @author Luiz Felipe Cunha <felipe.silvacunha@gmail.com>
     *
     * @param string $position
     * @access public
     * @return null
     */
    public function setPosition($position) {
        $this->_position = $position;
        return $this;
    }

    /**
     * Define o valor da direita
     *
     * @author Luiz Felipe Cunha <felipe.silvacunha@gmail.com>
     *
     * @param int $right
     * @access public
     * @return Node
     */
    public function setRight($right) {
        $this->_right = $right;
        return $this;
    }

    /**
     * Obtenção do valor da direita
     *
     * @author Luiz Felipe Cunha <felipe.silvacunha@gmail.com>
     *
     * @access public
     * @return int
     */
    public function getRight() {
        return $this->_right;
    }


    /**
     * Define o valor da esquerda
     *
     * @author Luiz Felipe Cunha <felipe.silvacunha@gmail.com>
     *
     * @param int $left
     * @access public
     * @return Node Próprio Objeto para encadeamento
     */
    public function setLeft($left) {
        $this->_left = $left;
        return $this;
    }

    /**
     * Obtenção de valor do left
     *
     * @author Luiz Felipe Cunha <felipe.silvacunha@gmail.com>
     *
     * @access public
     * @return int
     */
    public function getLeft() {
        return $this->_left;
    }

    /**
     * Obtenção de identificador do nodo
     *
     * @author Luiz Felipe Cunha <felipe.silvacunha@gmail.com>
     *
     * @access public
     * @return int
     */
    public function getId() {
        return $this->_ID;
    }

    /**
     * Adiciona uma nova ocorrência
     *
     * @author Luiz Felipe Cunha <felipe.silvacunha@gmail.com>
     *
     * @access public
     * @return Node Próprio Objeto para encadeamento
     */
    public function addWeight() {
        $this->_weight++;
        return $this;
    }

}
