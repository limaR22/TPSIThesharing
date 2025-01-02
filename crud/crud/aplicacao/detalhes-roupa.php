<?php
require_once __DIR__ . '/../src/infraestrutura/basededados/criar-conexao.php';

// Verifica se o ID da roupa foi passado na URL
if (!isset($_GET['roupa_id'])) {
    die("ID da roupa não especificado.");
}

$roupa_id = (int)$_GET['roupa_id'];

// Verifica se a roupa existe
$stmtRoupa = $pdo->prepare("SELECT * FROM roupas WHERE id = :id");
$stmtRoupa->execute([':id' => $roupa_id]);
$roupa = $stmtRoupa->fetch();

if (!$roupa) {
    die("Roupa não encontrada.");
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Detalhes da Roupa: <?= htmlspecialchars($roupa['nome']) ?></title>
    <link rel="stylesheet" href="../Css/grupo.css">
</head>
<body>
    <div class="container">
        <h1><?= htmlspecialchars($roupa['nome']) ?></h1>
        <img src="<?= htmlspecialchars($roupa['imagem']) ?>" alt="Foto da roupa">
        <p><?= htmlspecialchars($roupa['descricao']) ?></p>
        <button class="btn btn-success">Reservar</button>
    </div>
</body>
</html>