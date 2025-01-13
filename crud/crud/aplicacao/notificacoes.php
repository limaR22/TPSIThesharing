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
<link rel="stylesheet" href="/Css/footer.css">

<!-- Link do Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<body class="light-mode">
    <!-- Barra superior -->
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
        <div class="nav-links">
            <a href="\aplicacao\index.php" class="nav-link">
                <i class="fas fa-house"></i> Inicio
            </a>
            <a href="\aplicacao\grupos.php" class="nav-link">
                <i class="fas fa-users"></i> Grupos
            </a>
            <a href="\aplicacao\perfil.php" class="nav-link">
                <i class="fas fa-user"></i> Perfil
            </a>
            <a href="\aplicacao\roupas-alugadas.php" class="nav-link">
                <i class="fas fa-tags"></i> Alugueis
            </a>
        </div>
        <div class="logout-section">
            <form action="/src/controlador/aplicacao/controlar-autenticacao.php" method="post">
                <button type="submit" class="btn btn-danger btn-sair" name="util izador" value="logout">
                    <i class="fas fa-sign-out-alt"></i> Sair
                </button>
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
    <!-- Incluir o footer -->
    <?php include_once __DIR__ . '/../admin/templates/footer.php'; ?>
    <!-- Font Awesome para ícones -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
    <!-- Modo escuro/claro e outras funções -->
    <script src="/javascript/modos.js"></script>
</body>
</html>
