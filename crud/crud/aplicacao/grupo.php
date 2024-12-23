<?php
require_once realpath(__DIR__ . '/../src/infraestrutura/basededados/criar-conexao.php');

// Verifica se o ID do grupo foi passado na URL
if (!isset($_GET['grupo_id'])) {
    die("ID do grupo não especificado.");
}

$grupo_id = (int)$_GET['grupo_id'];

// Verifica se o grupo existe
$stmtGrupo = $pdo->prepare("SELECT * FROM grupo WHERE id = :id");
$stmtGrupo->execute([':id' => $grupo_id]);
$grupo = $stmtGrupo->fetch();

if (!$grupo) {
    die("Grupo não encontrado.");
}

// Adiciona nova roupa se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'] ?? '';
    $descricao = $_POST['descricao'] ?? '';
    $utilizador_id = 1; // Simula um ID de usuário para testes

    // Verifica se o arquivo foi enviado corretamente
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $fotoNome = uniqid('roupa_', true) . '.' . pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $fotoCaminho = __DIR__ . '/uploads/' . $fotoNome;

        if (move_uploaded_file($_FILES['foto']['tmp_name'], $fotoCaminho)) {
            $fotoRelativa = 'uploads/' . $fotoNome;

            // Insere a roupa no banco de dados
            $stmtInserir = $pdo->prepare("
                INSERT INTO roupas (nome, descricao, imagem, grupo_id, utilizador_id) 
                VALUES (:nome, :descricao, :imagem, :grupo_id, :utilizador_id)
            ");
            $stmtInserir->execute([
                ':nome' => $nome,
                ':descricao' => $descricao,
                ':imagem' => $fotoRelativa,
                ':grupo_id' => $grupo_id,
                ':utilizador_id' => $utilizador_id,
            ]);

            echo "Roupa adicionada com sucesso!";
        } else {
            echo "Erro ao salvar a imagem.";
        }
    } else {
        echo "Erro no envio da imagem.";
    }
}

// Busca as roupas deste grupo
$stmtRoupas = $pdo->prepare("SELECT * FROM roupas WHERE grupo_id = :grupo_id");
$stmtRoupas->execute([':grupo_id' => $grupo_id]);
$roupas = $stmtRoupas->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Grupo: <?= htmlspecialchars($grupo['nome']) ?></title>
    <link rel="stylesheet" href="../Css/grupo.css">
</head>
<body>
    <div class="container">
        <h1>Grupo: <?= htmlspecialchars($grupo['nome']) ?></h1>
        <p><?= htmlspecialchars($grupo['descricao']) ?></p>

        <h2>Roupas no Grupo</h2>
        <ul class="roupas-list">
            <?php if (empty($roupas)): ?>
                <li>Nenhuma roupa adicionada a este grupo ainda.</li>
            <?php else: ?>
                <?php foreach ($roupas as $roupa): ?>
                    <li>
                        <?php if ($roupa['imagem']): ?>
                            <img src="<?= htmlspecialchars($roupa['imagem']) ?>" alt="Foto da roupa">
                        <?php endif; ?>
                        <div>
                            <h3><?= htmlspecialchars($roupa['nome']) ?></h3>
                            <p><?= htmlspecialchars($roupa['descricao']) ?></p>
                        </div>
                    </li>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>
    </div>

    <!-- Formulário para adicionar roupa -->
    <div class="formulario-roupa">
        <h2>Adicionar Roupa</h2>
        <form method="POST" enctype="multipart/form-data">
            <label for="nome">Nome:</label>
            <input type="text" name="nome" id="nome" required>
            
            <label for="descricao">Descrição:</label>
            <textarea name="descricao" id="descricao" required></textarea>
            
            <label for="foto">Foto:</label>
            <input type="file" name="foto" id="foto" accept="image/*" required>
            
            <button type="submit">Adicionar Roupa</button>
        </form>
    </div>

    <!-- Botão para abrir o formulário -->
    <button class="btn-adicionar" onclick="toggleForm()">+</button>

    <script>
        // Função para mostrar/ocultar o formulário
        function toggleForm() {
            var form = document.querySelector('.formulario-roupa');
            form.style.display = form.style.display === 'block' ? 'none' : 'block';
        }
    </script>
</body>
</html>
