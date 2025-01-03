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

<!-- Área de conteúdo -->
<div class="content">
  <h1><?= htmlspecialchars($roupa['nome']) ?></h1>
  <img src="<?= htmlspecialchars($roupa['imagem']) ?>" alt="<?= htmlspecialchars($roupa['nome']) ?>" class="roupa-imagem">
  <p><?= htmlspecialchars($roupa['descricao']) ?></p>

  <!-- Botão de Alugar -->
  <form method="POST" action="alugar-roupa.php">
    <input type="hidden" name="roupa_id" value="<?= $roupa['id'] ?>">
    <button type="submit" class="btn btn-primary">Alugar</button>
  </form>
</div>

<?php include_once __DIR__ . '/templates/rodape.php'; ?>
