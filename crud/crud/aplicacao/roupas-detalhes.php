<?php
// Carregar funções auxiliares e estabelecer a conexão com a base de dados
require_once __DIR__ . '/../src/middleware/middleware-utilizador.php';
require_once __DIR__ . '/../src/auxiliadores/auxiliador.php';
require_once __DIR__ . '/../src/infraestrutura/basededados/criar-conexao.php';

$utilizador = utilizador(); // Carregar o utilizador atual

// Verificar se o ID da roupa foi passado
if (!isset($_GET['id'])) {
    die("ID da roupa não especificado.");
}

$roupa_id = (int)$_GET['id'];

// Buscar os detalhes da roupa
$stmt = $pdo->prepare("SELECT * FROM roupas WHERE id = :id");
$stmt->execute([':id' => $roupa_id]);
$roupa = $stmt->fetch();

if (!$roupa) {
    die("Roupa não encontrada.");
}

// Carregar cabeçalho
$titulo = 'Detalhes da Roupa';
include_once __DIR__ . '/templates/cabecalho.php';
?>

<!-- Link para o CSS externo -->
<link rel="stylesheet" href="/Css/roupa-detalhes.css">



<!-- Área de conteúdo principal centralizado -->
<div class="container">
    <!-- Botões Voltar e Alugar -->
    <div class="botao-container">
        <!-- Botão Voltar que usa HTTP_REFERER -->
        <a href="<?= isset($_SERVER['HTTP_REFERER']) ? htmlspecialchars($_SERVER['HTTP_REFERER']) : 'index.php' ?>" class="btn btn-secondary voltar-btn">Voltar</a>
        <form method="POST" action="alugar-roupa.php">
            <input type="hidden" name="roupa_id" value="<?= $roupa['id'] ?>">
            <button type="submit" class="btn btn-primary alugar-btn">Alugar</button>
        </form>
    </div>

    <!-- Detalhes da Roupa -->
    <div class="roupa-detalhes">
        <h1 class="roupa-nome"><?= htmlspecialchars($roupa['nome']) ?></h1>
        <img src="<?= htmlspecialchars($roupa['imagem']) ?>" alt="<?= htmlspecialchars($roupa['nome']) ?>" class="roupa-imagem">

        <!-- Título para Descrição -->
        <h3 class="descricao-titulo">Descrição</h3>
        <p class="roupa-descricao"><?= htmlspecialchars($roupa['descricao']) ?></p>
    </div>
</div>

<!-- Rodapé -->
<?php include_once __DIR__ . '/templates/rodape.php'; ?>
