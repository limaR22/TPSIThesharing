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
    $utilizador_id = $_SESSION['id'];

    if ($foto && $foto['error'] === 0) {
        $fotoNome = uniqid('grupo_', true) . '.' . pathinfo($foto['name'], PATHINFO_EXTENSION);
        $fotoCaminho = 'uploads/' . $fotoNome;

        if (move_uploaded_file($foto['tmp_name'], __DIR__ . '/' . $fotoCaminho)) {
            $sql = "INSERT INTO grupo (nome, descricao, foto, utilizador_id) VALUES (:nome, :descricao, :foto, :utilizador_id)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':nome', $nome);
            $stmt->bindParam(':descricao', $descricao);
            $stmt->bindParam(':foto', $fotoCaminho);
            $stmt->bindParam(':utilizador_id', $utilizador_id);

            if ($stmt->execute()) {
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

// Verificar se o usuário se inscreveu em algum grupo
if (isset($_POST['inscrever_grupo_id'])) {
    $grupo_id = $_POST['inscrever_grupo_id'];
    $utilizador_id = $_SESSION['id'];

    $stmtInscricao = $pdo->prepare("INSERT INTO grupo_utilizador (id_utilizador, id_grupo) VALUES (:id_utilizador, :id_grupo)");
    $stmtInscricao->execute([':id_utilizador' => $utilizador_id, ':id_grupo' => $grupo_id]);

    header("Location: grupos.php");
    exit();
}

// Verificar se o usuário solicitou para sair de um grupo
if (isset($_POST['sair_grupo_id'])) {
    $grupo_id = $_POST['sair_grupo_id'];
    $utilizador_id = $_SESSION['id'];

    $stmtSair = $pdo->prepare("DELETE FROM grupo_utilizador WHERE id_grupo = :id_grupo AND id_utilizador = :id_utilizador");
    $stmtSair->execute([':id_grupo' => $grupo_id, ':id_utilizador' => $utilizador_id]);

    header("Location: grupos.php");
    exit();
}

// Carregar os grupos
if (!empty($searchTerm)) {
    $stmtGrupos = $pdo->prepare("SELECT * FROM grupo WHERE nome LIKE :search OR descricao LIKE :search");
    $stmtGrupos->execute([':search' => '%' . $searchTerm . '%']);
} else {
    $utilizador_id = $_SESSION['id'];
    $stmtGrupos = $pdo->prepare("
        SELECT g.* FROM grupo g
        LEFT JOIN grupo_utilizador gu ON g.id = gu.id_grupo
        WHERE g.utilizador_id = :utilizador_id OR gu.id_utilizador = :utilizador_id
    ");
    $stmtGrupos->execute([':utilizador_id' => $utilizador_id]);
}

$grupos = $stmtGrupos->fetchAll();

// Carregar cabeçalho
$titulo = '- Meus Grupos';
include_once __DIR__ . '/templates/cabecalho.php';
?>

<link rel="stylesheet" href="/Css/grupos.css">
<link rel="stylesheet" href="/Css/footer.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<body class="light-mode">
    <!-- Barra superior principal -->
    <div class="top-bar">
        <div class="titulo-container">
            <h1 class="titulo-elegante">THE SHARING</h1>
        </div>
        <div class="utilizador-info">
        <h3 class="titulo-utilizador">Olá, <?= $_SESSION['nome'] ?? 'Utilizador' ?>!</h3>
        </div>
        <button class="btn btn-outline-secondary mode-toggle" id="toggleMode">
            <i class="fas fa-moon"></i>
        </button>
    </div>
     <!-- Nova topbar para links de navegação -->
    <div class="nav-top-bar">
        <div class="search-bar">
            <form method="GET" action="grupos.php">
                <input type="text" class="form-control" name="search" placeholder="Pesquisar grupos..." value="<?= htmlspecialchars($searchTerm) ?>">
                <button type="submit" class="btn btn-primary">Pesquisar</button>
            </form>
        </div>
    <div class="nav-links">
        <a href="\aplicacao\index.php" class="nav-link">
            <i class="fas fa-house"></i> Inicio
        </a>
        <a href="\aplicacao\notificacoes.php" class="nav-link">
            <i class="fas fa-bell"></i> Notificações
        </a>
        <a href="\aplicacao\perfil.php" class="nav-link">
            <i class="fas fa-user"></i> Perfil
        </a>
        <a href="\aplicacao\roupas-alugadas.php" class="nav-link"> <!-- Link para a página de alugueis -->
                <i class="fas fa-tags"></i> Alugueis
         </a>
    </div>
    <div class="logout-section">
        <form action="/src/controlador/aplicacao/controlar-autenticacao.php" method="post">
            <button type="submit" class="btn btn-danger btn-sair" name="utilizador" value="logout">
                <i class="fas fa-sign-out-alt"></i> Sair
            </button>
        </form>
    </div>
 </div>
    <div class="main-content">
        <h1 class="titulo-elegante">Meus Grupos</h1>
        <div class="grupos-list">
            <?php if (empty($grupos)): ?>
                <p class="text-muted">
                    <?php if (!empty($searchTerm)): ?>
                       <div> Nenhum grupo encontrado para "<?= htmlspecialchars($searchTerm) ?>" 😓.</div>
                    <?php else: ?>
                        <div> Ainda não estás em nenhum grupo! Cria ou pesquisa um grupo para começar 🚀.</div>
                    <?php endif; ?>
                </p>
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

                                    <?php
                                    $stmtVerificaInscricao = $pdo->prepare("SELECT 1 FROM grupo_utilizador WHERE id_grupo = :id_grupo AND id_utilizador = :id_utilizador");
                                    $stmtVerificaInscricao->execute([':id_grupo' => $grupo['id'], ':id_utilizador' => $_SESSION['id']]);
                                    $inscrito = $stmtVerificaInscricao->fetchColumn();
                                    ?>

                                    <?php if ($_SESSION['id'] != $grupo['utilizador_id']): ?>
                                        <?php if ($inscrito): ?>
                                            <form method="POST" action="grupos.php" class="d-inline-block">
                                                <input type="hidden" name="sair_grupo_id" value="<?= $grupo['id'] ?>">
                                                <button type="submit" class="btn btn-danger">Sair do Grupo</button>
                                            </form>
                                        <?php else: ?>
                                            <form method="POST" action="grupos.php" class="d-inline-block">
                                                <input type="hidden" name="inscrever_grupo_id" value="<?= $grupo['id'] ?>">
                                                <button type="submit" class="btn btn-primary">Inscrever-se</button>
                                            </form>
                                        <?php endif; ?>
                                    <?php endif; ?>

                                    <a href="../aplicacao/grupo.php?grupo_id=<?= $grupo['id'] ?>" class="btn btn-primary">Entrar no Grupo</a>

                                    <?php if ($_SESSION['id'] == $grupo['utilizador_id']): ?>
                                        <form action="editar_grupo.php" method="post" style="display:inline;">
                                            <input type="hidden" name="grupo_id" value="<?= $grupo['id'] ?>">
                                            <a href="editar_grupo.php?grupo_id=<?= $grupo['id'] ?>" class="btn btn-warning">Editar</a>
                                        </form>
                                        <form action="excluir_grupo.php" method="post" style="display:inline;">
                                            <input type="hidden" name="grupo_id" value="<?= $grupo['id'] ?>">
                                            <button type="submit" class="btn btn-danger">Excluir</button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <button class="btn btn-primary btn-floating" id="criarGrupoBtn" style="position: fixed; bottom: 20px; right: 20px;">
            Criar Grupo
        </button>

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
    </div>

    <script>
        document.getElementById('criarGrupoBtn').addEventListener('click', function() {
            document.getElementById('formularioGrupo').style.display = 'block';
        });
        document.getElementById('cancelarBtn').addEventListener('click', function() {
            document.getElementById('formularioGrupo').style.display = 'none';
        });
    </script>


<script src="/javascript/modos.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
</body>

<?php include_once __DIR__ . '/../admin/templates/footer.php';?>

</html>
