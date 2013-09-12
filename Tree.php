<?php
require_once "Node.php";

/**
 * Árvore binária com propriedades para alocação de nodos com base no algorítmo de Huffman Adaptativo
 *
 * @author Luiz Felipe Cunha <felipe.silvacunha@gmail.com>
 */
class Tree {

    /**
     * Nodo de marcação para Nodo não encontrado
     *
     * @var Node
     * @access protected
     */
    protected $_nyt;

    /**
     * Nodo raiz da árvore
     *
     * @var Node
     * @access protected
     */
    protected $_root;

    /**
     * Construtor da classe
     *
     * @author Luiz Felipe Cunha <felipe.silvacunha@gmail.com>
     *
     * @access public
     */
    public function __construct() {
        $this->_nyt = new Node('nyt');
        $this->_nyt->setPosition(Node::LEFT);
        $this->_root = $this->_nyt;
    }

    /**
     * Adiciona um novo elemento
     *
     * @author Luiz Felipe Cunha <felipe.silvacunha@gmail.com>
     *
     * @param string $element
     * @access public
     * @return Node Nodo com o elemento adicionado | valor nulo quando nodo já existe
     */
    public function addElement($element) {
        $node = $this->getNode($element);
        if (is_null($node)) {
            $newNode = new Node($element);
            $this->_addNode($newNode);
        } else {
            $newNode = $node;
        }
        $this->_arrangeNode($newNode);
        return $node;
    }

    /**
     * Obtenção de um nodo com base em seu valor
     *
     * @author Luiz Felipe Cunha <felipe.silvacunha@gmail.com>
     *
     * @param string $value Valor do nodo
     * @param Node $node    Nodo inicial para a procura
     * @access public
     * @return Node Nodo buscado | Valor nulo para quando não encontrado
     */
    public function getNode($value, $node = null) {
        $element = null;
        // primeira execução sem recursão?
        if (is_null($node)) {
            $node = $this->_root;
        }

        // encontrado?
        if ($node->getValue() == $value) {
            $element = $node;
        } else {
            // verifica primeiramente pelos nodos a direira pois existe maior probabilidade de encontrar
            $rightNode = $node->getRightNode();
            // existe nodo a direita?
            if (!is_null($rightNode)) {
                // chamada recursiva a partir do nodo a direita do atual
                $element = $this->getNode($value,$rightNode);
            }

            // nodo ainda não encontrado?
            if (is_null($element)) {
                $leftNode = $node->getLeftNode();
                // existe nodo à esquerda?
                if (!is_null($leftNode)) {
                    // chamada recursiva a partir do nodo a esquerda do atual
                    $element = $this->getNode($value,$leftNode);
                }
            }
        }

        // apresentação
        return $element;
    }

    /**
     * Obtenção dos nodos de um determinado nível da árvore
     *
     * @author Luiz Felipe Cunha <felipe.silvacunha@gmail.com>
     *
     * @param int $level Nível desejado
     * @param Node $node Nodo inicial de procura
     * @access protected
     * @return array Conjunto de nodos do nível desejado
     */
    protected function _getNodesByLevel($level,$node = null) {
        // primeira execução sem recursão?
        if (is_null($node)) {
            $node = $this->_root;
        }

        $nodes = array();
        // nodo é de mesmo nível que o solicitado?
        if ($node->getLevel() == $level) {
            // adiciona o nodo
            $nodes[] = $node;
        // nodo é de nível inferior ao solicitado?
        } elseif ($node->getLevel() < $level) {
            // chama recursivamente o teste para os nodos filhos do atual
            if ($node->getRightNode()) {
                $nodes = array_merge($nodes,$this->_getNodesByLevel($level,$node->getRightNode()));
            }
            if ($node->getLeftNode()) {
                $nodes = array_merge($nodes,$this->_getNodesByLevel($level,$node->getLeftNode()));
            }
        }

        // apresentação
        return $nodes;
    }

    /**
     * Obtenção de nodo em melhor posição que um determinado nodo
     *
     * @author Luiz Felipe Cunha <felipe.silvacunha@gmail.com>
     *
     * @param Node $node Nodo base de comparação
     * @access protected
     * @return Node
     */
    protected function _getNodeAtBetterPosition($node) {
        $result = null;
        // é nodo raiz?
        if (!$node->getParent()) {
            return null;
        }

        // varre os nodos a esquerda da árvore até que o nodo tenha peso inferior ou igual ao nodo base ou até que o
        // nodo esteja em mesmo nível que o nodo base
        $treeNode = $this->_root;
        while ($treeNode && $treeNode->getWeight() > $node->getWeight() && $treeNode->getLevel() <= $node->getLevel()) {
            $treeNode = $treeNode->getLeftNode();
        }

        // é um nodo válido?
        if ($treeNode && $treeNode->getLevel() != 0 && $treeNode->getWeight() != 0) {
            // obtenção do nível onde existe nodo de melhor posição
            $level = $treeNode->getLevel();
            // obtenção dos nodos do nível
            $nodes = $this->_getNodesByLevel($level);
            // varre os nodos do nível a procurar nodo compatível com o nodo base
            foreach ($nodes as $n) {
                // é o próprio nodo base?
                if ($n->isEqual($node)) {
                    continue;
                }

                // nodo é compatível?
                if (
                    $n->getWeight() == $node->getWeight() &&
                    !$n->isEqual($node->getParent()) &&
                    !$n->isEqual($this->_nyt) &&
                    !$n->getParent()->isEqual($node) &&
                    (
                        $n->getLevel() < $node->getLevel() ||
                        $node->getRight() < $n->getRight()
                    )
                ){
                    $result = $n;
                    break;
                }
            }
        }

        // apresentação
        return $result;
    }

    /**
     * Posiciona o nodo corretamente na árvore
     *
     * @author Luiz Felipe Cunha <felipe.silvacunha@gmail.com>
     *
     * @param Node $node Nodo a ser posicionado
     * @access protected
     * @return null
     */
    protected function _arrangeNode($node) {
        // laço para posicionar o nodo
        while($node) {
            // encontra e troca de posição com nodo em melhor posição
            $this->_searchBetterNode($node);

            // nodo folha?
            if ($node->getValue() !== '') {
                // adiciona ocorrência
                $node->addWeight();
            // nodo normal
            } else {
                // recalcula o peso da árvore
                $weight = 0;
                if ($node->getLeftNode()) {
                    $weight += $node->getLeftNode()->getWeight();
                }
                if ($node->getRightNode()) {
                    $weight += $node->getRightNode()->getWeight();
                }
                $node->setWeight($weight);
            }
            $node = $node->getParent();
        }
        // atualiza a árvore
        $this->update();
    }

    /**
     * Procura e troca de posição com um nodo de melhor posição
     *
     * @author Luiz Felipe Cunha <felipe.silvacunha@gmail.com>
     *
     * @param Node $node
     * @access protected
     * @return bool Ocorrência de troca
     */
    protected function _searchBetterNode($node) {
        // obtenção de nodo de melhor posição
        $betterNode = $this->_getNodeAtBetterPosition($node);
        $change = false;
        // há nodo em melhor posição
        if ($betterNode) {
            // troca os nodos de posição
            $level = $betterNode->getLevel();
            $betterNode->setLevel($node->getLevel());
            $node->setLevel($level);
            $parent = $betterNode->getParent();
            $position = $betterNode->getPosition();
            $betterNode->setParent($node->getParent(),$node->getPosition());
            $node->setParent($parent,$position);
            $change = true;
        }

        return $change;
    }

    /**
     * Adiciona um nodo
     *
     * @author Luiz Felipe Cunha <felipe.silvacunha@gmail.com>
     *
     * @param Node $node Novo nodo
     * @access protected
     * @return Tree Próprio objeto para encadeamento
     */
    protected function _addNode(Node $node) {
        // obtenção do pai do NYT
        $parent = $this->_nyt->getParent();

        // cria novo pai
        $newParent = new Node();

        // NYT não tinha PAI?
        if (is_null($parent)) {
            // define o novo pai como Raiz da árvore
            $this->_root = $newParent;
        } else {
            // define o novo pai como filho a esquerda do pai do NYT
            $parent->setLeftNode($newParent);
        }

        // adiciona o novo nodo ao lado do nodo NYT
        $newParent->setLeftNode($this->_nyt);
        $newParent->setRightNode($node);
        $level = $this->_nyt->getLevel();
        $newParent->setLevel($level);
        return $this;
    }

    /**
     * Atualiza a árvore
     *
     * @author Luiz Felipe Cunha <felipe.silvacunha@gmail.com>
     *
     * @param Node $node Nodo inicial da atualização
     * @param int $left  Valor de esquerda inicial
     * @param int $path  Caminho inicial
     * @access public
     * @return int Valor de direita do nodo
     */
    public function update(Node $node = null, $left = 0, $path = 0) {
        // deve partir da raiz da árvore?
        if (is_null($node)) {
            $node = $this->_root;
        }

        // define o valor de esquerda para o nodo
        $node->setLeft($left);
        // define o caminho para o nodo
        $node->setPath($path);

        // realiza chamadas recursivas para os nodos filhos
        $leftNode = $node->getLeftNode();
        $weight = 0;
        if ($leftNode) {
            $weight += $leftNode->getWeight();
            $leftPath = $path << 1;
            $left = $this->update($leftNode,$left+1,$leftPath);
        }

        $rightNode = $node->getRightNode();
        if ($rightNode) {
            $weight += $rightNode->getWeight();
            $rightPath = $path >> 1 | 1;
            $left = $this->update($rightNode,$left+1,$rightPath);
        }

        // define o valor de direita do nodo
        $node->setRight(++$left);

        // é um nodo de ligação
        if ($node->getValue() === '') {
            // atualiza o peso do nodo
            $node->setWeight($weight);
        }

        return $left;
    }

    /**
     * Obtenção do nodo através de seu caminho
     *
     * @author Luiz Felipe Cunha <felipe.silvacunha@gmail.com>
     *
     * @param string $path Caminho para o nodo
     * @access public
     * @return mixed Array com o nodo e a sobra do caminho para o nodo | falso para quando caminho é inválido
     */
    public function getNodeFromPath($path) {
        $node = null;
        $auxNode = $this->_root;

        // caminho inválido?
        if ($path[0] == 1) {
            return false;
        }

        // caminha na árvore a procura do nodo
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
     * Obtenção do nodo NYT
     *
     * @author Luiz Felipe Cunha <felipe.silvacunha@gmail.com>
     *
     * @access public
     * @return Node
     */
    public function getNYT() {
        return $this->_nyt;
    }

    /**
     * Obtenção do nodo raiz
     *
     * @author Luiz Felipe Cunha <felipe.silvacunha@gmail.com>
     *
     * @access public
     * @return Node
     */
    public function getRoot() {
        return $this->_root;
    }
}
