<?php

/**
 * Obtjeto de nodo e estaticamente controle de arvoré binária
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
     * @access public
     */
    public $ID;

    /**
     * esquerda
     *
     * @var int
     * @access public
     */
    public $left = 0;

    /**
     * direita
     *
     * @var int
     * @access public
     */
    public $right = 1;


    /**
     * Nodo ainda não transmitido (Not Yet Transmited)
     *
     * @var Node
     * @access public
     */
    public static $NYT = null;

    /**
     * Nodo inicial da árvore
     *
     * @var Node
     * @access public
     */
    public static $ROOT = null;

    /**
     * Controle de identificador de nodo
     *
     * @var int
     * @access private
     */
    private static $_geralID = 0;


    /**
     * Inicialização da árvore
     *
     * @author Luiz Felipe Cunha <felipe.silvacunha@gmail.com>
     *
     * @static
     * @access public
     * @return null
     */
    public static function startup() {
        $NYT = new Node('NYT');
        $NYT->position = self::LEFT;
        $ROOT = $NYT;
        self::$NYT = $NYT;
        self::$ROOT = $ROOT;
    }

    /**
     * Adiciona um elemento
     *
     * @author Luiz Felipe Cunha <felipe.silvacunha@gmail.com>
     *
     * @param mixed $element
     * @static
     * @access public
     * @return null
     */
    public static function addElement($element) {
        $node = self::_getNode($element,self::$ROOT);
        if (is_null($node)) {
            $newNode = self::addNode(new Node($element));
        } else {
            $newNode = $node;
        }
        self::_translate($newNode);
        return $node;
    }

    /**
     * Obtenção de um nodo através de seu valor interno
     *
     * @author Luiz Felipe Cunha <felipe.silvacunha@gmail.com>
     *
     * @param string $element
     * @static
     * @access public
     * @return Node
     */
    public static function getNode($element) {
        return self::_getNode($element,self::$ROOT);
    }

    /**
     * Obtenção de um nodo em um determinado ponto da árvore
     *
     * @author Luiz Felipe Cunha <felipe.silvacunha@gmail.com>
     *
     * @param string $value
     * @param Node $node
     * @static
     * @access protected
     * @return Node
     */
    protected static function _getNode($value,Node $node) {
        $element = null;
        // nodo de mesmo valor
        if ($node->getValue() == $value) {
            $element = $node;
        } else {
            // varre prioritariamente os nodos da direita pois estes são os de maior ocorrência
            $right = $node->getRightNode();
            if (!is_null($right)) {
                $element = self::_getNode($value,$right);
            }
            if (is_null($element)) {
                $left = $node->getLeftNode();
                if (!is_null($left)) {
                    $element = self::_getNode($value,$left);
                }
            }
        }
        return $element;
    }

    protected static function _getNodesFromLevel($level,$node = null) {
        $nodes = array();
        if (empty($node)) {
            $node = self::$ROOT;
        }

        if ($node->getLevel() == $level) {
            $nodes[] = $node;
        } elseif ($node->getLevel() < $level) {
            if ($node->getRightNode()) {
                $nodes = array_merge($nodes,self::_getNodesFromLevel($level,$node->getRightNode()));
            }
            if ($node->getLeftNode()) {
                $nodes = array_merge($nodes,self::_getNodesFromLevel($level,$node->getLeftNode()));
            }
        }

        return $nodes;
    }

    /**
     * Obtenção de nodo melhor posicionado
     *
     * @author Luiz Felipe Cunha <felipe.silvacunha@gmail.com>
     *
     * @param Node $node     Nodo para comparar
     * @param Node $treeNode Nodo inicial para a pesquisa
     * @static
     * @access protected
     * @return Node
     */
    protected static function _betterPosition($node) {
        $result = null;
        // o nodo é o ROOT?
        if (!$node->getParent()) {
            return null;
        }
        $treeNode = self::$ROOT;
        // obtenção do nível para posicionar o nodo
        while (
            $treeNode &&
            $treeNode->getWeight() > $node->getWeight() &&
            $treeNode->getLevel() <= $node->getLevel()
        ) {
            $treeNode = $treeNode->getLeftNode();
        }
        if (!$treeNode || $treeNode->getLevel() == 0 || $treeNode->getWeight() == 0) {
            $result = null;
        } else {
            $level = $treeNode->getLevel();

            // variavel temporária para os nodos
            $nodes = self::_getNodesFromLevel($level);

            // varre os nodos
            foreach ($nodes as $n) {
                // é o próprio nodo?
                if ($n->isEqual($node)) {
                    continue;
                }

                // nodo para comparar está melhor posicionado?
                if (
                    $n->getWeight() == $node->getWeight() &&
                    !$n->isEqual($node->getParent()) &&
                    !$n->isEqual(self::$NYT) &&
                    ($n->getLevel() < $node->getLevel() || $node->right < $n->right)
                ) {
                    $result = $n;
                }
            }
        }
        return $result;
    }

    /**
     * Atualiza o nodo e verifica a necessidade de translação com outro nodo da árvore
     *
     * @author Luiz Felipe Cunha <felipe.silvacunha@gmail.com>
     *
     * @param Node $node
     * @param bool $better Verificar nodo melhor posicionado?
     * @static
     * @access protected
     * @return null
     */
    protected static function _translate($node,$better = true) {
        // atualiza a árvore
        self::update();
        while($node) {
            // deve procurar por melhor posicionamento?
            if ($better) {

                $betterNode = self::_betterPosition($node);
                // encontrada melhor posição
                if ($betterNode) {
                    // realiza transção entre os nodos
                    $level = $betterNode->getLevel();
                    $betterNode->setLevel($node->getLevel());
                    $node->setLevel($level);

                    $parent = $betterNode->getParent();
                    $position = $betterNode->position;
                    $betterNode->setParent($node->getParent(),$node->position);
                    $node->setParent($parent,$position);
                }
            }
            // nodo folha?
            if ($node->getValue() !== '') {
                // incrementa um no peso
                $node->setWeight($node->getWeight()+1);
            // nodo de ligação
            } else {
                // atualiza o peso com base em suas ligações
                $weight = 0;
                if ($node->getLeftNode()) {
                    $weight += $node->getLeftNode()->getWeight();
                }
                if ($node->getRightNode()) {
                    $weight += $node->getRightNode()->getWeight();
                }
                $node->setWeight($weight);
            }
            // caminha um nível acima
            $node = $node->getParent();
        }

        // atualiza os nodos
        self::update();
    }

    /**
     * Adiciona um novo nodo
     *
     * @author Luiz Felipe Cunha <felipe.silvacunha@gmail.com>
     *
     * @param Node $node
     * @static
     * @access public
     * @return Nodo adicionado
     */
    public static function addNode(Node $node) {
        $nyt = self::$NYT;
        $parent = $nyt->getParent();
        $newParent = new Node();
        if (is_null($parent)) {
            self::$ROOT = $newParent;
        } else {
            $parent->setLeftNode($newParent);
        }
        $newParent->setLeftNode($nyt);
        $newParent->setRightNode($node);

        $level = $nyt->getLevel();
        $newParent->setLevel($level);

        return $node;

    }

    /**
     * Atualiza a arvore apartir da raiz
     *
     * @author Luiz Felipe Cunha <felipe.silvacunha@gmail.com>
     *
     * @static
     * @access public
     * @return null
     */
    public static function update() {
        self::_update(self::$ROOT,0);
    }


    /**
     * Atualiza os valores de left e right e também os caminhos para cada nodo
     *
     * @author Luiz Felipe Cunha <felipe.silvacunha@gmail.com>
     *
     * @param Nodo $node   Nodo inicial
     * @param int $left    Valor de left inicial
     * @param string $path Caminho inicial
     * @static
     * @access private
     * @return int valor do maior número de direita e esquerda
     */
    private static function _update($node,$left,$path = 0) {
        $node->left = $left;
        $node->_setPath($path);
        $leftNode = $node->getLeftNode();
        $weight = 0;
        if ($leftNode) {
            $weight += $leftNode->getWeight();
            $aux = $path << 1;
            $left = self::_update($leftNode,$left+1,$aux);
        }
        $rightNode = $node->getRightNode();
        if ($rightNode) {
            $weight += $rightNode->getWeight();
            $aux = ($path << 1) | 1;
            $left = self::_update($rightNode,$left+1,$aux);
        }
        $node->right = ++$left;
        if ($node->getValue() == '') {
            $node->setWeight($weight);
        }
        return $left;
    }


    /**
     * Exibe a árvore
     *
     * @author Luiz Felipe Cunha <felipe.silvacunha@gmail.com>
     *
     * @static
     * @access public
     * @return null
     */
    public static function show() {
        $chartId = str_replace('.','',microtime(true));
        echo '<ul class="huffman" data-chart="'.$chartId.'" style="display:none">';
        echo '<li>';
        self::_show(self::$ROOT);
        echo '</li>';
        echo '</ul>';
        echo '<div id="'.$chartId.'" class="orgChart"></div>';

    }

    /**
     * Método para exibir a parte mais interna da árvore
     *
     * @author Luiz Felipe Cunha <felipe.silvacunha@gmail.com>
     *
     * @param Node $node
     * @static
     * @access private
     * @return null
     */
    private static function _show($node) {
        if (!empty($node)) {
            $value = str_pad(decbin($node->getPath()),$node->getLevel(),0,STR_PAD_LEFT).'<br/>'.$node->getValue().'<br/> Peso: '.$node->getWeight().'<br>'.$node->ID;
            echo $value;
            $left = $node->getLeftNode();
            $right = $node->getRightNode();
            if (!empty($left) || !empty($right)) {
                echo '<ul>';
                if (!empty($left)) {
                    echo '<li>';
                    self::_show($left);
                    echo '</li>';
                }
                if (!empty($right)) {
                    echo '<li>';
                    self::_show($right);
                    echo '</li>';
                }
                echo '</ul>';
            }
        }
    }


    /**
     * Obtenção de um nodo através de seu caminho na árvore
     *
     * @author Luiz Felipe Cunha <felipe.silvacunha@gmail.com>
     *
     * @param string $path
     * @static
     * @access public
     * @return null
     */
    public static function getNodeFromPath($path) {
        $node = null;
        $auxNode = self::$ROOT;
        if ($path[0] == 1) {
            return false;
        }
        for ($i=1;$i<strlen($path);$i++) {
            if (!$auxNode->getLeftNode() && !$auxNode->getRightNode()) {
                $node = $auxNode;
                break;
            }
            if ($path[$i] == 0) {
                if (!$auxNode->getLeftNode()) {
                    $node = $auxNode;
                    break;
                }
                $auxNode = $auxNode->getLeftNode();
            } else {
                if (!$auxNode->getRightNode()) {
                    $node = $auxNode;
                    break;
                }
                $auxNode = $auxNode->getRightNode();
            }
        }
        return array('node' => $node, 'path' => substr($path,$i));

    }

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
        $this->ID = self::$_geralID;
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
     * @access protected
     * @return Node
     */
    protected function _setPath($path) {
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
        return $this->ID == $node->ID;
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
     * @return Node
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
     * @return Node
     */
    public function setParent(Node $parent,$position,$upParent = true) {
        $this->_parent = $parent;
        $this->position = $position;
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
     * @return Node
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
     * @access protected
     * @return Node
     */
    protected function setLevel($level) {
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


}
