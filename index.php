<?php
class Graph {

    public $file;
    public $content_file = [];
    public $edges = [];
    public $matrix = [];
    public $nodes = [];

    private $separator_edge = ",";
    private $errors = [];

    public function __construct(){}

    public function openFile($file)
    {
        if(file_exists(__DIR__ . "/" . $file)) {
            $this->file = $file;
            $data_file = file(__DIR__ . "/" . $this->file, FILE_SKIP_EMPTY_LINES | FILE_IGNORE_NEW_LINES);

            foreach ($data_file as $line_num => $line) {
                $this->content_file[] = sprintf("Linha #<b>{%s}</b> : %s<br>", $line_num,  htmlspecialchars($line) );
                if($line_num > 0)
                    $this->setEdges($line);
            }

            $this->setNodes();
            $this->setMatrix();

        } else {
            $this->setErrors('O arquivo ' . $file . ' não foi localizado neste diretório');
        }
    }

    public function setErrors($error) {
        $this->errors[] = $error;
    }

    public function getErrors() {
        return $this->errors;
    }

    public function getContentFile() {
        return $this->content_file;
    }

    public function setMatrix()
    {
        $matrix = [];
        $nodes = $this->getNodes();
        $edges = $this->getEdges();

        for ($i=0;$i<count($nodes);$i++) {
           for ($z=0;$z<count($nodes);$z++) {
               if(in_array(sprintf("%s%s%s", $nodes[$i], $this->separator_edge, $nodes[$z]), $edges) ||
                   in_array(sprintf("%s%s%s", $nodes[$z], $this->separator_edge, $nodes[$i]), $edges)
               ) {
                   $matrix[$nodes[$i]][$nodes[$z]] = 1;
               }
               else {
                   $matrix[$nodes[$i]][$nodes[$z]] = 0;
               }
           }
        }

        $this->matrix = $matrix;
    }

    public function getMatrix()
    {
        return $this->matrix;
    }

    public function setEdges($edge)
    {
        $this->edges[] = $edge;
    }

    public function getEdges()
    {
        return $this->edges;
    }

    public function setNodes()
    {
        $nodes = [];

        foreach ($this->getEdges() as $edge) {
            $node = explode($this->separator_edge, $edge);

            if($node[0] != "" && !in_array($node[0], $nodes))
                $nodes[] = $node[0];

            if($node[1] != "" && !in_array($node[1], $nodes))
                $nodes[] = $node[1];
        }

        $this->nodes = $nodes;
    }

    public function getNodes()
    {
        return $this->nodes;
    }

    public function verifyAdjacencyNodes($v1, $v2): bool
    {
        $node1 = $this->formatValue($v1);
        $node2 = $this->formatValue($v2);
        $matrix = $this->getMatrix();

        if($matrix[$node1][$node2] == 1) {
            return true;
        }
        else {
            return false;
        }
    }

    public function verifyDegreeNodes($v): int
    {
        $node = $this->formatValue($v);
        $degree = 0;

        foreach ($this->getEdges() as $edge) {
            $e = explode(',', $edge);
            if(count($e) > 1) {
                if($node == $e[0] || $node == $e[1])
                    $degree++;
            }
        }

        return $degree;
    }

    public function listAdjacencyNodes($v): array
    {
        $node = $this->formatValue($v);
        $nodes_adjacency = [];

        foreach ($this->getEdges() as $edge) {
            $e = explode(',', $edge);
            if(count($e) > 1) {
                if($node == $e[0]) {
                    $nodes_adjacency[] = $e[1];
                } elseif($node == $e[1]) {
                    $nodes_adjacency[] = $e[0];
                }
            }
        }

        return $nodes_adjacency;
    }

    public function formatValue($value): string
    {
        return strtoupper(trim($value));
    }
}
?>
<!doctype html>
<html class="no-js" lang="">

<head>
    <meta charset="utf-8">
    <title>Projeto de Teoria dos Grafos - UEMA</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>

<body>
<h1 style="text-align: center">Projeto de Teoria dos Grafos - UEMA</h1>
<h4 style="text-align: center">Aluno: Fernando Pontes</h4>
<h4 style="text-align: center">Matriz de Adjacência do tipo Não Dirigido</h4>

<div style="width: 50%; margin: 0 auto">
<?php
$graph = new Graph();
$graph->openFile('source.txt');

if(count($graph->getErrors()) > 0) {
    print('<p style="text-align: center; color: brown">Os seguintes erros foram encontrados:</p><ul>');
    foreach ($graph->getErrors() as $error) {
        printf('<li>%s</li>', $error);
    }
    print('</u>');
} else {
    if(count($graph->getContentFile()) > 0) {

        print('<p><strong>Conteúdo do arquivo:</strong></p><ul>');
        foreach ($graph->getContentFile() as $item) {
            printf('<li>%s</li>', $item);
        }
        print('</ul>');

        print_r($graph->getMatrix());

        if(isset($_POST['action'])) {
            switch ($_POST['action'])
            {
                case 'verify_adjacency_nodes':
                    $status = $graph->verifyAdjacencyNodes($_POST['v1'], $_POST['v2']);
                    if($status) {
                        $msg_return = "Os dois vértices são adjacentes!";
                    } else {
                        $msg_return = "Os dois vértices não são adjacentes!";
                    }
                    break;

                case 'verify_degree_nodes':
                    $result = $graph->verifyDegreeNodes($_POST['v']);
                    $msg_return = sprintf("O vértice %s possui grau %d", $graph->formatValue($_POST['v']), $result);
                    break;

                case 'list_adjacency_nodes':
                    $result = $graph->listAdjacencyNodes($_POST['v']);
                    if(count($result) > 0) {
                        $msg_return = sprintf("O vértice %s possui os seguintes vizinhos: %s", $graph->formatValue($_POST['v']), implode(', ', $result));
                    } else {
                        $msg_return = sprintf("O vértice %s não possui vizinhos", $graph->formatValue($_POST['v']));
                    }

                    break;
            }

            printf('<h2 style="text-align:center; color: brown">%s</h2>', $msg_return);
        }
?>
<h3>Verificar se dois vértices são ou não adjacentes:</h3>
<form method="post" action="index.php">
    <fieldset>
    <label>Digite o primeiro vértice:</label>
        <input type="text" name="v1" required>
    </fieldset>
    <fieldset style="margin-top: 15px">
        <label>Digite o segundo vértice:</label>
        <input type="text" name="v2" required>
    </fieldset>
    <input type="hidden" name="action" value="verify_adjacency_nodes">
    <input type="submit" name="submit" value="Verificar" style="margin-top: 15px">
</form>
<hr>
<h3>Calcular o grau de um vértice:</h3>
<form method="post" action="index.php">
    <fieldset>
        <label>Digite o vértice:</label>
        <input type="text" name="v" required>
    </fieldset>
    <input type="hidden" name="action" value="verify_degree_nodes">
    <input type="submit" name="submit" value="Calcular" style="margin-top: 15px">
</form>

<h3>Buscar todos os vizinhos de um vértice:</h3>
<form method="post" action="index.php">
    <fieldset>
        <label>Digite o vértice:</label>
        <input type="text" name="v" required>
    </fieldset>
    <input type="hidden" name="action" value="list_adjacency_nodes">
    <input type="submit" name="submit" value="Buscar" style="margin-top: 15px">
</form>
<?php
    }
    else {
        print('<p style="text-align: center; color: brown">Arquivo sem conteúdo!</p>');
    }
}
?>
</div>

</body>

</html>
