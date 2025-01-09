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
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="/Css/roupa-detalhes.css">


<?php include __DIR__ . '/sidebar.php'; ?>


<!-- Conteúdo principal -->
<!-- Conteúdo principal -->
<div class="content">
    <h1>Roupas Detalhes</h1>
    <p>Aqui você pode visualizar os detalhes da roupa.</p>
    <?php if (isset($roupa)): ?>
        <div class="detalhes-container">
            <!-- Imagem centralizada -->
            <div class="imagem-container">
                <img src="<?= htmlspecialchars($roupa['imagem']) ?>" alt="Imagem da roupa" class="roupa-img">
            </div>
            <!-- Detalhes ao lado da imagem -->
            <div class="detalhes-texto">
                <p>Descrição da roupa: <?= htmlspecialchars($roupa['descricao']) ?></p>
                <?php if (isset($roupa['preco'])): ?>
                    <p>Preço da roupa: <?= htmlspecialchars($roupa['preco']) ?></p>
                <?php endif; ?>
                <?php if (isset($roupa['categoria'])): ?>
                    <p>Categoria da roupa: <?= htmlspecialchars($roupa['categoria']) ?></p>
                <?php endif; ?>
                <p>Tamanho da roupa: <?= htmlspecialchars($roupa['tamanho'] ?? '') ?></p>
                <p>Gênero da roupa: <?= htmlspecialchars($roupa['genero'] ?? '') ?></p>
                <!-- Botão de alugar -->
                <form method="POST" action="alugar-roupa.php">
                    <input type="hidden" name="roupa_id" value="<?= $roupa['id'] ?>">
                    <button type="submit" class="btn btn-primary alugar-btn">Alugar <i class="bi bi-cart"></i></button>
                </form>
            </div>
        </div>
    <?php endif; ?>
</div>
<!-- Rodapé -->
<?php include_once __DIR__ . '/templates/rodape.php'; ?>
