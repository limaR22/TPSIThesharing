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

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="/Css/roupas-alugadas.css">
<link rel="stylesheet" href="/Css/footer.css">

<div class="container mt-5">
    <h1>Minha Conta</h1>

    <!-- Secção 1: Roupas Alugadas -->
    <h2>Roupas Alugadas</h2>
    <?php if (count($roupas_alugadas) > 0): ?>
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
    <?php else: ?>
        <p>Você ainda não alugou nenhuma roupa.</p>
    <?php endif; ?>

    <!-- Secção 2: Roupas Disponíveis para Alugar -->
    <h2>Roupas que Coloquei para Alugar</h2>
    <?php if (count($roupas_para_alugar) > 0): ?>
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
                        <td><img src="<?= htmlspecialchars($roupa['imagem']) ?>" alt="Imagem da roupa" class="img-thumbnail " style="width: 100px;"></td>
                        <td><?= htmlspecialchars($roupa['descricao']) ?></td>
                        <td><?= htmlspecialchars($roupa['status']) ?></td>
                        <td>
                            <a href="roupas-detalhes.php?id=<?= $roupa['id'] ?>" class="btn btn-info btn-sm">Ver Detalhes</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Você ainda não colocou nenhuma roupa para alugar.</p>
    <?php endif; ?>
</div>
<?php include_once __DIR__ . '/../admin/templates/footer.php';?>