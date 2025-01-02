<?php
// Iniciar sessão e carregar o utilizador
require_once __DIR__ . '/../src/middleware/middleware-utilizador.php';
require_once __DIR__ . '/../src/auxiliadores/auxiliador.php';

// Conectar à base de dados
require_once __DIR__ . '/../src/infraestrutura/basededados/criar-conexao.php';

// Verificar se o formulário de pesquisa foi enviado
$searchTerm = '';
if (isset($_GET['search'])) {
    $searchTerm = $_GET['search'];
}


// Verificar se o formulário de criação de grupo foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nome'], $_POST['descricao'])) {
    $nome = $_POST['nome'];
    $descricao = $_POST['descricao'];
    $foto = $_FILES['foto'];
    $utilizador_id = $_SESSION['id']; // ID do usuário autenticado

    // Verifica se o arquivo foi enviado corretamente
    if ($foto && $foto['error'] === 0) {
        // Definir um nome único para o arquivo (para evitar conflitos)
        $fotoNome = uniqid('grupo_', true) . '.' . pathinfo($foto['name'], PATHINFO_EXTENSION);
        $fotoCaminho = 'uploads/' . $fotoNome; // Pasta 'uploads/'

        // Move o arquivo para a pasta 'uploads/'
        if (move_uploaded_file($foto['tmp_name'], __DIR__ . '/' . $fotoCaminho)) {
            // Inserir os dados no banco de dados
            $sql = "INSERT INTO grupo (nome, descricao, foto, utilizador_id) VALUES (:nome, :descricao, :foto, :utilizador_id)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':nome', $nome);
            $stmt->bindParam(':descricao', $descricao);
            $stmt->bindParam(':foto', $fotoCaminho);
            $stmt->bindParam(':utilizador_id', $utilizador_id); // Associar ao usuário

            if ($stmt->execute()) {
                // Redirecionar após sucesso
                header("Location: grupos.php");
                exit();
            } else {
                echo "Erro ao criar o grupo!";
            }
        } else {
            echo "Erro ao mover o arquivo de imagem.";
        }
    } else {
        echo "Por favor, envie uma imagem válida.";
    }
}

// Carregar os grupos do usuário autenticado
$utilizador_id = $_SESSION['id']; // ID do usuário autenticado
$stmtGrupos = $pdo->prepare("SELECT * FROM grupo WHERE utilizador_id = :utilizador_id");
$stmtGrupos->execute([':utilizador_id' => $utilizador_id]);
$grupos = $stmtGrupos->fetchAll();

// Carregar cabeçalho
$titulo = '- Meus Grupos';
include_once __DIR__ . '/templates/cabecalho.php';
?>

<!-- Link para o CSS externo -->
<link rel="stylesheet" href="/Css/grupos.css">

<!-- Link do Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<body class="light-mode">
    <!-- Barra superior -->
    <div class="top-bar">
        <div>
            <h3>Olá, <?= $_SESSION['nome'] ?? 'Utilizador' ?>!</h3>
        </div>

        <div class="search-bar">
            <input type="text" class="form-control" placeholder="Pesquisar grupos...">
        </div>

        <button class="btn btn-outline-secondary mode-toggle" id="toggleMode">
            <i class="fas fa-moon"></i>
        </button>
    </div>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="top-section">
            <h2>Menu</h2>
            <div class="nav-links">
                <a href="/aplicacao/index.php" class="nav-link">Início</a>
                <a href="/aplicacao/notificacoes.php" class="nav-link">Notificações</a>
                <a href="/aplicacao/perfil.php" class="nav-link">Perfil</a>
            </div>
        </div>
        <div class="logout-section">
            <form action ```php
            = "/src/controlador/aplicacao/controlar-autenticacao.php" method="post">
                <button type="submit" class="btn btn-danger" name="utilizador" value="logout">Sair</button>
            </form>
        </div>
    </div>

    <!-- Conteúdo principal -->
    <div class="main-content">
        <h1>Meus Grupos</h1>

<!-- Lista de grupos -->
<div class="grupos-list">
    <?php if (empty($grupos)): ?>
        <p>Não estás em nenhum grupo neste momento... 😓.</p>
    <?php else: ?>
        <div class="row">
            <?php foreach ($grupos as $grupo): ?>
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <?php if ($grupo['foto']): ?>
                            <img src="<?= $grupo['foto'] ?>" class="card-img-top" alt="Foto do grupo" style="height: 200px; object-fit: cover;">
                        <?php else: ?>
                            <img src="default-image.jpg" class="card-img-top" alt="Foto do grupo" style="height: 200px; object-fit: cover;">
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($grupo['nome']) ?></h5>
                            <p class="card-text"><?= htmlspecialchars($grupo['descricao']) ?></p>
                            <!-- Link para entrar no grupo -->
                            <a href="../aplicacao/grupo.php?grupo_id=<?= $grupo['id'] ?>" class="btn btn-primary">Entrar no Grupo</a>
                            <!-- Botões de editar e excluir -->
                            <form action="editar_grupo.php" method="post" style="display:inline;">
                                <input type="hidden" name="grupo_id" value="<?= $grupo['id'] ?>">
                                <button type="submit" class="btn btn-warning">Editar</button>
                            </form>
                            <form action="excluir_grupo.php" method="post" style="display:inline;">
                                <input type="hidden" name="grupo_id" value="<?= $grupo['id'] ?>">
                                <button type="submit" class="btn btn-danger">Excluir</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

    <!-- Botão flutuante para abrir o formulário de criação de grupo -->
    <button class="btn btn-success btn-floating" id="criarGrupoBtn" style="position: fixed; bottom: 20px; right: 20px;">
        Criar Grupo
    </button>

    <!-- Formulário de criação de grupo (inicialmente escondido) -->
    <div id="formularioGrupo" style="display: none; position: fixed; bottom: 80px; right: 20px; width: 300px; background: white; padding: 20px; border-radius: 8px; box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);">
        <h5>Criar Novo Grupo</h5>
        <form id="formCriarGrupo" action="grupos.php" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="nome" class="form-label">Nome do Grupo</label>
                <input type="text" class="form-control" id="nome" name="nome" required>
            </div>
            <div class="mb-3">
                <label for="descricao" class="form-label">Descrição</label>
                <textarea class="form-control" id="descricao" name="descricao" required></textarea>
            </div>
            <div class="mb-3">
                <label for="foto" class="form-label">Foto do Grupo</label>
                <input type="file" class="form-control" id="foto" name="foto" accept="image/*">
            </div>
            <button type="submit" class="btn btn-primary">Criar Grupo</button>
            <button type="button" class="btn btn-secondary" id="cancelarBtn">Cancelar</button>
        </form>
    </div>

    <!-- JavaScript -->
    <script>
        // Mostrar o formulário quando o botão "Criar Grupo" for clicado
        document.getElementById('criarGrupoBtn').addEventListener('click', function() {
            document.getElementById('formularioGrupo').style.display = 'block';
        });

        // Esconder o formulário quando o botão "Cancelar" for clicado
        document.getElementById('cancelarBtn').addEventListener('click', function() {
            document.getElementById('formularioGrupo').style.display = 'none';
        });
    </script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>