<?php
# Middleware para garantir que apenas utilizadores autenticados acessem este sítio
require_once __DIR__ . '/../src/middleware/middleware-utilizador.php';

# Acessa funções auxiliares
@require_once __DIR__ . '/../src/auxiliadores/auxiliador.php';

# Carrega o utilizador atual
$utilizador = utilizador();

# Carregar grupos do utilizador
# Supondo que você tenha uma função que retorne os grupos aos quais o utilizador pertence
# Carrega o cabeçalho padrão com o título
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
            <h3>Olá, <?= $utilizador['nome'] ?? 'Utilizador' ?>!</h3>
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
                <a href="\aplicacao\index.php" class="nav-link">Início</a> <!-- Link para a página inicial -->
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

    <!-- Conteúdo principal -->
    <div class="main-content">
        <h1>Meus Grupos</h1>

        <!-- Lista de grupos -->
        <div class="grupos-list">
            <?php if (empty($grupos)): ?>
                <p>Não estás em nenhum grupo neste momento... 😓.</p>
            <?php else: ?>
                <ul class="list-group">
                    <?php foreach ($grupos as $grupo): ?>
                        <li class="list-group-item">
                            <h5><?= htmlspecialchars($grupo['nome']) ?></h5>
                            <p><?= htmlspecialchars($grupo['descricao']) ?></p>
                            <p><strong>Gênero:</strong> <?= ucfirst($grupo['genero']) ?></p>
                            <p><strong>Tamanhos disponíveis:</strong> <?= implode(', ', $grupo['tamanhos']) ?></p>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Font Awesome para ícones -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>

    <script src="/javascript/modos.js"></script>
    
</body>
</html>
