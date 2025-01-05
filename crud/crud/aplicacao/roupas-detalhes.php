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

<!-- Barra superior com pesquisa -->
<div class="top-bar">
    <div>
        <h3>Olá, <?= htmlspecialchars($utilizador['nome']) ?>!</h3>
    </div>
    <form method="GET" action="pesquisa.php" class="search-form">
        <input type="text" name="query" placeholder="Pesquisar roupas..." class="form-control search-input">
        <button type="submit" class="btn btn-outline-primary">Pesquisar</button>
    </form>
</div>

<!-- Layout com barra lateral -->
<div class="container">
    <div class="sidebar">
        <div class="top-section">
            <h2>Menu</h2>
            <div class="nav-links">
                <a href="/aplicacao/grupos.php" class="nav-link">Grupos</a>
                <a href="/aplicacao/index.php" class="nav-link">Início</a>
                <a href="/aplicacao/perfil.php" class="nav-link">Perfil</a>
                <a href="/aplicacao/notificacoes.php" class="nav-link">Notificações</a>
            </div>
        </div>
        <div class="logout-section">
            <form action="/src/controlador/aplicacao/controlar-autenticacao.php" method="post">
                <button type="submit" class="btn btn-danger" name="utilizador" value="logout">Sair</button>
            </form>
        </div>
    </div>

    <!-- Área de conteúdo principal -->
    <div class="main-content">
        <h1><?= htmlspecialchars($roupa['nome']) ?></h1>
        <img src="<?= htmlspecialchars($roupa['imagem']) ?>" alt="<?= htmlspecialchars($roupa['nome']) ?>" class="roupa-imagem">
        <p><?= htmlspecialchars($roupa['descricao']) ?></p>

        <!-- Botão de Alugar -->
        <form method="POST" action="alugar-roupa.php">
            <input type="hidden" name="roupa_id" value="<?= $roupa['id'] ?>">
            <button type="submit" class="btn btn-primary">Alugar</button>
        </form>
    </div>
</div>

<!-- Rodapé -->
<?php include_once __DIR__ . '/templates/rodape.php'; ?>
