<?php
require_once 'Graph.php';
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
    print('</ul>');
} else {
    if(count($graph->getContentFile()) > 0) {

        print('<p><strong>Conteúdo do arquivo:</strong></p><ul>');
        foreach ($graph->getContentFile() as $item) {
            printf('<li>%s</li>', $item);
        }
        print('</ul>');

        if(isset($_POST['action'])) {
            switch ($_POST['action'])
            {
                case 'verify_adjacency_nodes':
                    $status = $graph->verifyAdjacencyNodes($_POST['v1'], $_POST['v2']);
                    if($status) {
                        $msg_return = sprintf("Os vértices [%s,%s] são adjacentes!", $graph->formatValue($_POST['v1']), $graph->formatValue($_POST['v2']));
                    } else {
                        $msg_return = sprintf("Os vértices [%s,%s] não são adjacentes!", $graph->formatValue($_POST['v1']), $graph->formatValue($_POST['v2']));
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

                case 'visit_all_edges':
                        $result = $graph->visitAllEdges();
                        $msg_return = sprintf("Todas as arestas {%s} do grafo foram visitadas!", implode(',', $result));
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
    <br>
    <hr>
    <br>
    <h3>Calcular o grau de um vértice:</h3>
    <form method="post" action="index.php">
        <fieldset>
            <label>Digite o vértice:</label>
            <input type="text" name="v" required>
        </fieldset>
        <input type="hidden" name="action" value="verify_degree_nodes">
        <input type="submit" name="submit" value="Calcular" style="margin-top: 15px">
    </form>
    <br>
    <hr>
    <br>
    <h3>Buscar todos os vizinhos de um vértice:</h3>
    <form method="post" action="index.php">
        <fieldset>
            <label>Digite o vértice:</label>
            <input type="text" name="v" required>
        </fieldset>
        <input type="hidden" name="action" value="list_adjacency_nodes">
        <input type="submit" name="submit" value="Buscar" style="margin-top: 15px">
    </form>
    <br>
    <hr>
    <br>
    <h3>Visitar todas as arestas do grafo:</h3>
    <form method="post" action="index.php">
        <input type="hidden" name="action" value="visit_all_edges">
        <input type="submit" name="submit" value="Visitar arestas" style="margin-top: 15px">
    </form>
    <br>
    <hr>
    <br>
    <h3>Visualizar o grafo:</h3>
    <a href="graph.html" target="_blank">Clique aqui para visualizar o grafo</a>
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
