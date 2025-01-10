<?php
// Iniciar sessão
session_start();

// Middleware para garantir autenticação
require_once __DIR__ . '/../src/middleware/middleware-utilizador.php';

// Conectar à base de dados
require_once __DIR__ . '/../src/infraestrutura/basededados/criar-conexao.php';

// Verificar se o utilizador está autenticado
if (!isset($_SESSION['id'])) {
    die("Utilizador não autenticado. Por favor, faça login.");
}

// Carregar notificações do utilizador autenticado
$stmtNotificacoes = $pdo->prepare("SELECT titulo, mensagem, data FROM notificacoes WHERE utilizador_id = :utilizador_id ORDER BY data DESC");
$stmtNotificacoes->execute([':utilizador_id' => $_SESSION['id']]);
$notificacoes = $stmtNotificacoes->fetchAll();

# Carregar notificações do utilizador
# Supondo que você tenha uma função que retorne as notificações do utilizador


# Carrega o cabeçalho padrão com o título
$titulo = '- Notificações';
include_once __DIR__ . '/templates/cabecalho.php';
?>

<!-- Link para o CSS externo -->
<link rel="stylesheet" href="/Css/notificacoes.css">

<!-- Link do Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<body class="light-mode">
    <!-- Barra superior -->
    <div class="top-bar">
        <div>
            <h3>Olá, <?= $utilizador['nome'] ?? 'Utilizador' ?>!</h3>
        </div>

        <button class="btn btn-outline-secondary mode-toggle" id="toggleMode">
            <i class="fas fa-moon"></i>
        </button>
    </div>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="top-section">
            <h2 class="titulo-elegante">Menu</h2>
            <br></br>
            <div class="nav-links">
                 <!-- Link para a página inicial -->
                <a href="\aplicacao\grupos.php" class="nav-link">Grupos</a>
                <a href="\aplicacao\index.php" class="nav-link">Início</a>
                <a href="\aplicacao\perfil.php" class="nav-link">Perfil</a>
            </div>
        </div>
        <div class="logout-section">
            <form action="/src/controlador/aplicacao/controlar-autenticacao.php" method="post">
                <button type="submit" class="btn btn-danger btn-sair" name="utilizador" value="logout">Sair</button>
            </form>
        </div>
    </div>

    <!-- Conteúdo principal -->
    <div class="main-content">
    <h2 class="titulo-utilizador">Notificações</h2>

        <!-- Lista de notificações -->
        <div class="notificacoes-list">
            <?php if (empty($notificacoes)): ?>
                <p>Não tens notificações neste momento...🗞️.</p>
            <?php else: ?>
                <ul class="list-group">
                    <?php foreach ($notificacoes as $notificacao): ?>
                        <li class="list-group-item">
                            <h5><?= htmlspecialchars($notificacao['titulo']) ?></h5>
                            <p><?= htmlspecialchars($notificacao['mensagem']) ?></p>
                            <p><strong>Data:</strong> <?= htmlspecialchars($notificacao['data']) ?></p>
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
