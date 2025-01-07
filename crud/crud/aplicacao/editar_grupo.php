<?php
require_once __DIR__ . '/../src/infraestrutura/basededados/criar-conexao.php';
session_start();

// Verifica se o usuário está autenticado
if (!isset($_SESSION['id'])) {
    die("Você precisa estar logado para editar um grupo.");
}

// Verifica se o ID do grupo foi passado na URL
if (!isset($_GET['grupo_id'])) {
    die("ID do grupo não especificado.");
}

$grupo_id = (int)$_GET['grupo_id'];

// Busca o grupo para editar
$stmtGrupo = $pdo->prepare("SELECT * FROM grupo WHERE id = :id");
$stmtGrupo->execute([':id' => $grupo_id]);
$grupo = $stmtGrupo->fetch();

if (!$grupo) {
    die("Grupo não encontrado.");
}

// Verifica se o formulário de edição foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'] ?? '';
    $descricao = $_POST['descricao'] ?? '';
    $foto = $_FILES['foto'];

    // Atualiza o grupo no banco de dados
    $stmtModificar = $pdo->prepare("
        UPDATE grupo 
        SET nome = :nome, descricao = :descricao 
        WHERE id = :id
    ");
    $stmtModificar->execute([
        ':nome' => $nome,
        ':descricao' => $descricao,
        ':id' => $grupo_id,
    ]);

    // Se uma nova foto foi enviada, atualiza a foto
    if ($foto && $foto['error'] === 0) {
        $fotoNome = uniqid('grupo_', true) . '.' . pathinfo($foto['name'], PATHINFO_EXTENSION);
        $fotoCaminho = 'uploads/' . $fotoNome;

        if (move_uploaded_file($foto['tmp_name'], __DIR__ . '/' . $fotoCaminho)) {
            $stmtAtualizarFoto = $pdo->prepare("
                UPDATE grupo 
                SET foto = :foto 
                WHERE id = :id
            ");
            $stmtAtualizarFoto->execute([
                ':foto' => $fotoCaminho,
                ':id' => $grupo_id,
            ]);
        }
    }

    header("Location: grupos.php");
    exit();
}

function validarEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function validarSenha($senha) {
    return strlen($senha) >= 8; 
}



$titulo = '- Editar Grupo';
include_once __DIR__ . '/templates/cabecalho.php';
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/Css/editar_grupo.css">
    <title>Editar Grupo</title>
</head>
<body>
    <div class="container">
        <h1>Editar Grupo: <?= htmlspecialchars($grupo['nome']) ?></h1>
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="nome" class="form-label">Nome do Grupo:</label>
                <input type="text" name="nome" class="form-control" value="<?= htmlspecialchars($grupo['nome']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="descricao" class="form-label">Descrição:</label>
                <textarea name="descricao" class="form-control" rows="5" required><?= htmlspecialchars($grupo['descricao']) ?></textarea>
            </div>
            <div class="mb-3">
                <label for="foto" class="form-label">Nova Foto do Grupo:</label>
                <input type="file" name="foto" class="form-control">
            </div>
            <button type="submit" class="btn btn-primary">Salvar Alterações</button>
            <a href="grupos.php" class="btn btn-secondary">Voltar</a>
        </form>
    </div>
</body>
</html>
