<?php
require_once realpath(__DIR__ . '/../src/middleware/middleware-utilizador.php');
require_once realpath(__DIR__ . '/../src/infraestrutura/basededados/criar-conexao.php');

// Verifica se o ID do grupo foi passado na URL
if (!isset($_GET['grupo_id'])) {
    echo "ID do grupo não especificado.";
    exit();
}

// Obtém o ID do grupo da URL
$grupo_id = $_GET['grupo_id'];

// Prepara a consulta para buscar o grupo pelo ID
$sql = "SELECT * FROM grupo WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':id', $grupo_id, PDO::PARAM_INT);
$stmt->execute();
$grupo = $stmt->fetch();

// Verifica se o grupo foi encontrado
if (!$grupo) {
    echo "Grupo não encontrado.";
    exit();
}

// Inclui o cabeçalho da página
include_once __DIR__ . '/templates/cabecalho.php';
?>

<div class="container">
    <div class="group-header">
        <h1><?= htmlspecialchars($grupo['nome']) ?></h1>
        <p class="group-description"><?= htmlspecialchars($grupo['descricao']) ?></p>
    </div>

    <div class="add-clothing">
        <h2>Adicionar Roupa</h2>
        <form action="/src/controlador/aplicacao/controlar-roupa.php" method="POST" enctype="multipart/form-data">
    <div class="mb-3">
        <label for="roupaNome" class="form-label">Nome da Roupa</label>
        <input type="text" class="form-control" id="roupaNome" name="nome" required>
    </div>
    <div class="mb-3">
        <label for="roupaDescricao" class="form-label">Descrição da Roupa</label>
        <textarea class="form-control" id="roupaDescricao" name="descricao" required></textarea>
    </div>
    <div class="mb-3">
        <label for="roupaFoto" class="form-label">Foto da Roupa</label>
        <input type="file" class="form-control" id="roupaFoto" name="foto" accept="image/*" required>
    </div>
    <input type="hidden" name="grupo_id" value="<?= $grupo_id ?>">
    <button type="submit" class="btn btn-success">Adicionar Roupa</button>
</form>
    </div>

    <div class="roupas-list">
        <h2>Roupas no Grupo</h2>
        <ul class="list-group">
            <?php
            // Aqui você deve buscar as roupas associadas a este grupo
            $sqlRoupas = "SELECT * FROM roupas WHERE grupo_id = :grupo_id";
            $stmtRoupas = $pdo->prepare($sqlRoupas);
            $stmtRoupas->bindParam(':grupo_id', $grupo_id, PDO::PARAM_INT);
            $stmtRoupas->execute();
            $roupas = $stmtRoupas->fetchAll();

            foreach ($roupas as $roupa) {
                echo '<li class="list-group-item">';
                echo '<h5>' . htmlspecialchars($roupa['nome']) . '</h5>';
                echo '<p>' . htmlspecialchars($roupa['descricao']) . '</p>';
                if ($roupa['foto']) {
                    echo '<img src="' . htmlspecialchars($roupa['foto']) . '" alt="Foto da roupa" style="width: 100px; height: auto;">';
                }
                echo '</li>';
            }
            ?>
        </ul>
    </div>
</div>

<!-- Link do Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- JavaScript do Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<?php include_once __DIR__ . '/templates/rodape.php'; ?>