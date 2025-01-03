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
    $utilizador_id = $_SESSION['id']; // ID do usuário autenticado

    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $fotoNome = uniqid('roupa_', true) . '.' . pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $fotoCaminho = __DIR__ . '/uploads/' . $fotoNome;

        if (move_uploaded_file($_FILES['foto']['tmp_name'], $fotoCaminho)) {
            $fotoRelativa = 'uploads/' . $fotoNome;

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

# Busca os membros do grupo
$stmtMembros = $pdo->prepare("
    SELECT u.id, u.nome, CASE WHEN g.utilizador_id = u.id THEN 1 ELSE 0 END AS is_admin 
    FROM utilizadores u
    JOIN grupo_utilizador gu ON u.id = gu.id_utilizador
    LEFT JOIN grupo g ON g.id = gu.id_grupo
    WHERE gu.id_grupo = :grupo_id
    ORDER BY is_admin DESC, u.nome
");
$stmtMembros->execute([':grupo_id' => $grupo_id]);
$membros = $stmtMembros->fetchAll();

# Carregar cabeçalho
$titulo = '- Grupo: ' . htmlspecialchars($grupo['nome']);
include_once __DIR__ . '/templates/cabecalho.php';
?>

<!-- Link para o CSS externo -->
<link rel="stylesheet" href="/Css/grupo.css">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Barra de pesquisa -->
<div class="top-bar">
  <div>
    <h3>Olá, <?= $utilizador['nome'] ?? 'Utilizador' ?>!</h3>
  </div>

  <div class="search-bar">
    <form method="GET" action="" class="mb-3">
      <input type="text" class="form-control" name="search" placeholder="Pesquisar grupos...">
      <button type="submit" class="btn btn-primary">Pesquisar</button>
    </form>
  </div>

  <button class="btn btn-outline-secondary mode-toggle" id="toggleMode">
    <i class="fas fa-moon"></i>
  </button>
</div>

<!-- Barra de navegação lateral -->
<div class="sidebar">
  <div class="top-section">
    <h2>Menu</h2>
    <div class="nav-links">
      <a href="\aplicacao\grupos.php" class="nav-link">Grupos</a>
      <a href="\aplicacao\notificacoes.php" class="nav-link">Notificações</a>
      <a href="\aplicacao\perfil.php" class="nav-link">Perfil</a>
    </div>
  </div>
  <div class="logout-section">
    <form action="/src/controlador/aplicacao/controlar-autenticacao.php" method="post">
      <button type="submit" class="btn btn-danger" name="utilizador" value="logout">Sair</button>
    </form>
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
          <label for="foto">Foto:</label>
          <input type="file" class="form-control" name="foto" accept="image/*" required>
        </div>
        <button type="submit" class="btn btn-success">Adicionar Roupa</button>
      </form>
    </div>
  </div>

<!-- Lista de roupas -->
<div class="roupas-list">
  <h2>Roupas do Grupo</h2>
  <ul>
    <?php foreach ($roupas as $roupa): ?>
      <li>
        <strong><?= htmlspecialchars($roupa['nome']) ?></strong>: <?= htmlspecialchars($roupa['descricao']) ?>
        <?php if ($roupa['imagem']): ?>
          <img src="<?= htmlspecialchars($roupa['imagem']) ?>" alt="<?= htmlspecialchars($roupa['nome']) ?>" width="100">
        <?php endif; ?>

        <!-- Link ou botão para os detalhes -->
        <a href="roupas-detalhes.php?id=<?= $roupa['id'] ?>" class="btn btn-info">Detalhes</a>
      </li>
    <?php endforeach; ?>
  </ul>
</div>


  <!-- Lista de utilizadores -->
  <div class="utilizadores-list">
    <h2>Utilizadores do Grupo</h2>
    <ul>
      <?php foreach ($membros as $membro): ?>
        <li>
          <?= htmlspecialchars($membro['nome']) ?> <?= $membro['is_admin'] ? '(Admin)' : '' ?>
        </li>
      <?php endforeach; ?>
    </ul>
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

<?php include_once __DIR__ . '/templates/rodape.php'; ?>
