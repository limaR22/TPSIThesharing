<?php
# Middleware para garantir que apenas utilizadores autenticados acessem este sítio
require_once __DIR__ . '/../src/middleware/middleware-utilizador.php';

# Acessa funções auxiliares
@require_once __DIR__ . '/../src/auxiliadores/auxiliador.php';

# Carrega o utilizador atual
$utilizador = utilizador();

# Conectar à base de dados
require_once __DIR__ . '/../src/infraestrutura/basededados/criar-conexao.php';

# Verifica se o ID do grupo foi passado na URL
if (!isset($_GET['grupo_id'])) {
    die("ID do grupo não especificado.");
}

$grupo_id = (int)$_GET['grupo_id'];

# Verifica se o grupo existe
$stmtGrupo = $pdo->prepare("SELECT * FROM grupo WHERE id = :id");
$stmtGrupo->execute([':id' => $grupo_id]);
$grupo = $stmtGrupo->fetch();

if (!$grupo) {
    die("Grupo não encontrado.");
}

# Adiciona nova roupa se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'] ?? '';
    $descricao = $_POST['descricao'] ?? '';
    $tamanho = $_POST['tamanho'] ?? ''; // Novo campo: tamanho
    $genero = $_POST['genero'] ?? '';
    $utilizador_id = $_SESSION['id']; // ID do usuário autenticado

    // Verifica se o arquivo de foto foi enviado corretamente
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        // Gera um nome único para a foto
        $fotoNome = uniqid('roupa_', true) . '.' . pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $fotoCaminho = __DIR__ . '/uploads/' . $fotoNome;

        // Move a foto para o diretório de uploads
        if (move_uploaded_file($_FILES['foto']['tmp_name'], $fotoCaminho)) {
            $fotoRelativa = 'uploads/' . $fotoNome;

            // Prepara e executa a inserção no banco de dados
            $stmtInserir = $pdo->prepare("
                INSERT INTO roupas (nome, descricao, imagem, grupo_id, utilizador_id, tamanho, genero)
                VALUES (:nome, :descricao, :imagem, :grupo_id, :utilizador_id, :tamanho, :genero)
            ");
            $stmtInserir->execute([
                ':nome' => $nome,
                ':descricao' => $descricao,
                ':imagem' => $fotoRelativa,
                ':grupo_id' => $grupo_id,
                ':utilizador_id' => $utilizador_id,
                ':tamanho' => $tamanho, // Novo campo: tamanho
                ':genero' => $genero, // Novo campo: gênero
            ]);

            // Redireciona após a inserção para evitar reenvio do formulário
            header('Location: ' . $_SERVER['REQUEST_URI']);
            exit;
        }
    }
}

# Busca as roupas deste grupo
$stmtRoupas = $pdo->prepare("SELECT * FROM roupas WHERE grupo_id = :grupo_id");
$stmtRoupas->execute([':grupo_id' => $grupo_id]);
$roupas = $stmtRoupas->fetchAll();

$stmtMembros = $pdo->prepare("SELECT u.id, u.nome, CASE WHEN g.utilizador_id = u.id THEN 1 ELSE 0 END AS is_admin FROM grupo_utilizador gu JOIN utilizadores u ON gu.id_utilizador = u.id JOIN grupo g ON g.id = gu.id_grupo WHERE gu.id_grupo = :grupo_id ORDER BY is_admin DESC, u.nome;");
$stmtMembros->execute([':grupo_id' => $grupo_id]);
$membros = $stmtMembros->fetchAll();

# Captura o termo de pesquisa
$searchTerm = '';
if (isset($_GET['search'])) {
    $searchTerm = $_GET['search'];
}

# Busca as roupas com base no termo de pesquisa
if (!empty($searchTerm)) {
    $stmtRoupas = $pdo->prepare("SELECT * FROM roupas WHERE grupo_id = :grupo_id AND (nome LIKE :search OR descricao LIKE :search)");
    $stmtRoupas->execute([':grupo_id' => $grupo_id, ':search' => '%' . $searchTerm . '%']);
} else {
    $stmtRoupas->execute([':grupo_id' => $grupo_id]);
}
$roupas = $stmtRoupas->fetchAll();

# Carregar cabeçalho
$titulo = '- Grupo: ' . htmlspecialchars($grupo['nome']);
include_once __DIR__ . '/templates/cabecalho.php';
?>

<!-- Link para o CSS externo -->
<link rel="stylesheet" href="/Css/grupo.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Barra superior -->

<div class="top-bar">
    <div>
        <h3>Olá, <?= $_SESSION['nome'] ?? 'Utilizador' ?>!</h3>
    </div>

    <div class="search-bar">
        <form method="GET" action="">
            <input type="text" class="form-control" name="search" placeholder="Pesquisar roupas..." value="<?= htmlspecialchars($searchTerm) ?>">
            <input type="hidden" name="grupo_id" value="<?= $grupo_id ?>">
            <button type="submit" class="btn btn-primary">Pesquisar</button>
        </form>
    </div>

    <div class="mode-toggle-container">
        <div class="d-flex align-items-center">
            <button class="btn btn-outline-secondary mode-toggle" id="toggleMode">
                <i class="fas fa-moon"></i>
            </button>
            <a href="grupos.php" class="btn btn-secondary ms-2">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>
</div>

<!-- Área de conteúdo -->
<div class="content">
    <h1><?= htmlspecialchars($grupo['nome']) ?></h1>
    <p><?= htmlspecialchars($grupo['descricao']) ?></p>

    <!-- Botão de adicionar roupa -->
    <button class="btn btn-success" id="adicionar-roupa-btn">Adicionar Roupa</button>

    <!-- Modal para adicionar roupa -->
    <div id="adicionar-roupa-modal" class="modal">
        <div class="modal-content">
            <span class="close-btn" id="close-modal">&times;</span>
            <h2>Adicionar Roupa</h2>
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="nome">Nome:</label>
                    <input type="text" class="form-control" name="nome" required>
                </div>
                <div class="form-group">
                    <label for="descricao">Descrição:</label>
                    <textarea class="form-control" name="descricao" required></textarea>
                </div>
                <div class="form-group">
                    <label for="tamanho">Tamanho:</label>
                    <select class="form-control" name="tamanho" required>
                        <option value="">Selecione o tamanho</option>
                        <option value="S">S</option>
                        <option value="M">M</option>
                        <option value="L">L</option>
                        <option value="XL">XL</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="genero">Gênero:</label>
                    <select class="form-control" name="genero" required>
                        <option value="">Selecione o gênero</option>
                        <option value="Masculino">Masculino</option>
                        <option value="Feminino">Feminino</option>
                        <option value="Unisex">Unisex</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="foto">Foto:</label>
                    <input type="file" class="form-control" name="foto" accept="image/*" required>
                </div>
                <button type="submit" class="btn btn-success">Adicionar Roupa</button>
            </form>
        </div>
    </div>

    <!-- Lista de utilizadores -->
    <div class="sidebar">
        <h2>Utilizadores do Grupo</h2>
        <ul>
            <?php foreach ($membros as $membro): ?>
                <?php if (!$membro['is_admin']): ?>
                    <li>
                        <span class="user-icon">👤</span>
                        <span class="user-name"><?= htmlspecialchars($membro['nome']) ?></span>
                        <span class="role utilizador">(Utilizador)</span>
                    </li>
                <?php endif; ?>
            <?php endforeach; ?>
        </ul>
    </div>

    <!-- Lista de roupas -->
    <div class="roupas-list">
        <h2 class="roupas-titulo">Roupas do Grupo</h2>
        <div class="row">
            <?php foreach ($roupas as $roupa): ?>
                <div class="col-md-4 mb-3">
                    <div class="card">
                        <?php if ($roupa['imagem']): ?>
                            <img src="<?= htmlspecialchars($roupa['imagem']) ?>" class="card-img-top" alt="<?= htmlspecialchars($roupa['nome']) ?>" style="object-fit: cover; height: 200px; width: 100%;">
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($roupa['nome']) ?></h5>
                            <p class="card-text"><?= htmlspecialchars($roupa['descricao']) ?></p>
                            <p class="card-text">Tamanho: <?= htmlspecialchars($roupa['tamanho'] ?? '') ?></p>
                            <p class="card-text">Gênero: <?= htmlspecialchars($roupa['genero'] ?? '') ?></p>
                            <a href="roupas-detalhes.php?id=<?= $roupa['id'] ?>" class="btn btn-info">Detalhes</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- JavaScript para mostrar o modal -->
    <script>
        document.getElementById('adicionar-roupa-btn').addEventListener('click', function() {
            document.getElementById('adicionar-roupa-modal').style.display = 'flex';
        });

        document.getElementById('close-modal').addEventListener('click', function() {
            document.getElementById('adicionar-roupa-modal').style.display = 'none';
        });

        window.onclick = function(event) {
            if (event.target === document.getElementById('adicionar-roupa-modal')) {
                document.getElementById('adicionar-roupa-modal').style.display = 'none';
            }
        };
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
    <script src="/javascript/modos.js"></script>

<?php include_once __DIR__ . '/templates/rodape.php'; ?>