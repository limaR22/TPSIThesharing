<?php
require_once __DIR__ . '/../src/middleware/middleware-utilizador.php';
require_once __DIR__ . '/../src/auxiliadores/auxiliador.php';
require_once __DIR__ . '/../src/infraestrutura/basededados/criar-conexao.php';

// Obter o utilizador atual
$utilizador_id = $_SESSION['id'];

// Buscar roupas alugadas pelo utilizador, excluindo as que estão com devolução solicitada
$stmt = $pdo->prepare("
    SELECT a.id AS aluguel_id, r.*, a.data_aluguel
    FROM alugueis a
    JOIN roupas r ON a.roupa_id = r.id
    WHERE a.alugador_id = :utilizador_id AND a.status != 'devolucao_solicitada'
");
$stmt->execute([':utilizador_id' => $utilizador_id]);
$roupas_alugadas = $stmt->fetchAll();

// Buscar roupas que o utilizador colocou para alugar
$stmt = $pdo->prepare("
    SELECT * 
    FROM roupas 
    WHERE utilizador_id = :utilizador_id
");
$stmt->execute([':utilizador_id' => $utilizador_id]);
$roupas_para_alugar = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Minha Conta - THE SHARING</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="/Css/roupas-alugadas.css">
    <link rel="stylesheet" href="/Css/footer.css">
    <!-- Font Awesome para ícones -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
     <!-- Modo escuro/claro e outras funções -->
    <script src="/javascript/modos.js"></script>
</head>
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
        <div class="nav-links">
            <a href="\aplicacao\grupos.php" class="nav-link">
                <i class="fas fa-users"></i> Grupos
            </a>
            <a href="\aplicacao\notificacoes.php" class="nav-link">
                <i class="fas fa-bell"></i> Notificações
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
                <button type="submit" class="btn btn-danger btn-sair" name="utilizador" value="logout">
                    <i class="fas fa-sign-out-alt"></i> Sair
                </button>
            </form>
        </div>
    </div>
    <!-- Conteúdo principal -->
    <div class="container mt-5">
    <h1>Minha Conta</h1>

    <!-- Verificar se ambas as listas estão vazias -->
    <?php if (count($roupas_alugadas) === 0 && count($roupas_para_alugar) === 0): ?>
        <div class="placeholder-container">
            <h2>Nada por aqui ainda!</h2>
            <p>Você ainda não alugou nenhuma roupa nem colocou roupas para alugar. Que tal explorar nossas opções?</p>
        </div>
    <?php else: ?>

        <!-- Secção 1: Roupas Alugadas -->
        <?php if (count($roupas_alugadas) > 0): ?>
            <h2>Roupas Alugadas</h2>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Imagem</th>
                        <th>Descrição</th>
                        <th>Data do Alugamento</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($roupas_alugadas as $roupa): ?>
                        <tr>
                            <td><img src="<?= htmlspecialchars($roupa['imagem']) ?>" alt="Imagem da roupa" class="img-thumbnail" style="width: 100px;"></td>
                            <td><?= htmlspecialchars($roupa['descricao']) ?></td>
                            <td><?= htmlspecialchars($roupa['data_aluguel']) ?></td>
                            <td>
                                <form method="POST" action="processar-acao-aluguel.php" style="display: inline;">
                                    <input type="hidden" name="acao" value="devolucao">
                                    <input type="hidden" name="aluguel_id" value="<?= $roupa['aluguel_id'] ?>">
                                    <button type="submit" class="btn btn-warning btn-sm">Solicitar Devolução</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <!-- Secção 2: Roupas que Coloquei para Alugar -->
        <?php if (count($roupas_para_alugar) > 0): ?>
            <h2>Roupas que Coloquei para Alugar</h2>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Imagem</th>
                        <th>Descrição</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($roupas_para_alugar as $roupa): ?>
                        <tr>
                            <td><img src="<?= htmlspecialchars($roupa['imagem']) ?>" alt="Imagem da roupa" class="img-thumbnail" style="width: 100px;"></td>
                            <td><?= htmlspecialchars($roupa['descricao']) ?></td>
                            <td><?= htmlspecialchars($roupa['status']) ?></td>
                            <td>
                                <a href="roupas-detalhes.php?id=<?= $roupa['id'] ?>" class="btn btn-info btn-sm">Ver Detalhes</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

    <?php endif; ?>
</div>
    <?php include_once __DIR__ . '/../admin/templates/footer.php'; ?>
</body>
</html>