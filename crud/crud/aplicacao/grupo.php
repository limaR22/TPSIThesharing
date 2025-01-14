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
    $marca = $_POST['marca'] ?? ''; // Novo campo: marca
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
                INSERT INTO roupas (nome, descricao, imagem, grupo_id, utilizador_id, tamanho, genero, marca)
                VALUES (:nome, :descricao, :imagem, :grupo_id, :utilizador_id, :tamanho, :genero, :marca)
            ");
            $stmtInserir->execute([
                ':nome' => $nome,
                ':descricao' => $descricao,
                ':imagem' => $fotoRelativa,
                ':grupo_id' => $grupo_id,
                ':utilizador_id' => $utilizador_id,
                ':tamanho' => $tamanho, // Novo campo: tamanho
                ':genero' => $genero, // Novo campo: gênero
                ':marca' => $marca, // Novo campo: marca
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

# Captura os filtros de busca
$sexoFiltro = $_GET['sexo'] ?? '';
$tamanhoFiltro = $_GET['tamanho'] ?? '';
$marcaFiltro = $_GET['marca'] ?? '';

# Captura o termo de pesquisa
$searchTerm = '';
if (isset($_GET['search'])) {
    $searchTerm = $_GET['search'];
}

# Busca as roupas com base no termo de pesquisa e nos filtros
$query = "SELECT * FROM roupas WHERE grupo_id = :grupo_id";
$parametros = [':grupo_id' => $grupo_id];

if (!empty($searchTerm)) {
    $query .= " AND (nome LIKE :search OR descricao LIKE :search OR marca LIKE :search)";
    $parametros[':search'] = '%' . $searchTerm . '%';
}

if (!empty($sexoFiltro)) {
    $query .= " AND genero = :genero";
    $parametros[':genero'] = $sexoFiltro;
}

if (!empty($tamanhoFiltro)) {
    $query .= " AND tamanho = :tamanho";
    $parametros[':tamanho'] = $tamanhoFiltro;
}

if (!empty($marcaFiltro)) {
    $query .= " AND marca = :marca";
    $parametros[':marca'] = $marcaFiltro;
}

$stmtRoupas = $pdo->prepare($query);
$stmtRoupas->execute($parametros);
$roupas = $stmtRoupas->fetchAll();


# Carregar cabeçalho
$titulo = '- Grupo: ' . htmlspecialchars($grupo['nome']);
include_once __DIR__ . '/templates/cabecalho.php';
?>

<!-- Link para o CSS externo -->
<link rel="stylesheet" href="/Css/grupo.css">
<link rel="stylesheet" href="/Css/footer.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- Barra superior -->

<div class="top-bar">
    <div class="titulo-container">
      <h3 class="titulo-utilizador">Olá, <?= $utilizador['nome'] ?? 'Utilizador' ?>!</h3>
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
                    <label for="marca">Marca:</label>
                    <input type="text" class="form-control" name="marca" required>
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
        <h2 class="titulo-utilizador">Utilizadores do Grupo</h2>
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
        <div class="row">
            <div class="col-md-6 text-right">
                <h2 class="roupas-titulo">Roupas do Grupo</h2>
                <button type="button" class="btn btn-primary btn-filtrar" data-bs-toggle="modal" data-bs-target="#filtrarRoupasModal">
                        <i class="fas fa-filter"></i> Filtrar
                </button>
                <br></br>
            </div>
        </div>

        <!-- Modal de Filtragem -->
        <div class="modal fade" id="filtrarRoupasModal" tabindex="-1" aria-labelledby="filtrarRoupasModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="filtrarRoupasModalLabel">Filtrar Roupas</h5>
                        <button type="button" class="btn-close btn-close-custom" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form method="GET" action="grupo.php">
                    <!-- Campo oculto para passar o grupo_id -->
                    <input type="hidden" name="grupo_id" value="<?= isset($_GET['grupo_id']) ? $_GET['grupo_id'] : '' ?>">

                    <div class="form-group mb-3">
                        <label for="genero" class="form-label">Sexo:</label>
                        <select class="form-select" name="genero" id="genero">
                            <option value="">Todos os sexos</option>
                            <option value="masculino">Masculino</option>
                            <option value="feminino">Feminino</option>
                            <option value="unisex">Unisex</option>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="tamanho" class="form-label">Tamanho:</label>
                        <select class="form-select" name="tamanho" id="tamanho">
                            <option value="">Todos os tamanhos</option>
                            <option value="S">S</option>
                            <option value="M">M</option>
                            <option value="L">L</option>
                            <option value="XL">XL</option>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="marca" class="form-label">Marca:</label>
                        <select class="form-select" name="marca" id="marca">
                            <option value="">Todas as marcas</option>
                            <?php
                            $stmtMarcas = $pdo->prepare("SELECT DISTINCT marca FROM roupas");
                            $stmtMarcas->execute();
                            $marcas = $stmtMarcas->fetchAll();
                            foreach ($marcas as $marca): ?>
                                <option value="<?= htmlspecialchars($marca['marca']) ?>"><?= htmlspecialchars($marca['marca']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Aplicar Filtros</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
        <?php if (empty($roupas)): ?>
            <p class="text-muted">Ainda não há nenhuma roupa neste grupo😓.</p>
        <?php else: ?>
            <div class="row">
                <?php foreach ($roupas as $roupa): ?>
                    <div class="col-md-4 mb-3">
                        <div class="card">
                            <?php if ($roupa['imagem']): ?>
                                <img src="<?= htmlspecialchars($roupa['imagem']) ?>" class="card-img-top" alt="<?= htmlspecialchars($roupa['nome']) ?>" style="object-fit: cover; height: 200px; width: 100%;">
                            <?php endif; ?>
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($roupa['nome']) ?></h5>
                                <p class="card-text">Marca: <?= htmlspecialchars($roupa['marca']) ?></p>
                                <p class="card-text">Tamanho: <?= htmlspecialchars($roupa['tamanho']) ?></p>
                                <p class="card-text">Gênero: <?= htmlspecialchars($roupa['genero']) ?></p>
                                <a href="roupas-detalhes.php?id=<?= $roupa['id'] ?>" class="btn btn-info">Detalhes</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
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

    <?php include_once __DIR__ . '/../admin/templates/footer.php';?>