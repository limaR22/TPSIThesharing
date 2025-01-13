<?php
require_once __DIR__ . '/../src/middleware/middleware-utilizador.php';
require_once __DIR__ . '/../src/infraestrutura/basededados/criar-conexao.php';

// Obter o utilizador atual
$utilizador_id = $_SESSION['id'];

// Verificar se a ação foi enviada
if (!isset($_POST['acao']) || !isset($_POST['aluguel_id'])) {
    die("Ação ou ID do aluguel não especificado.");
}

$acao = $_POST['acao'];
$aluguel_id = (int)$_POST['aluguel_id'];

$mensagem = ''; // Variável para armazenar a mensagem de sucesso ou erro

try {
    if ($acao === 'devolucao') {
        // Solicitar devolução
        $stmt = $pdo->prepare("
            UPDATE alugueis 
            SET status = 'devolucao_solicitada' 
            WHERE id = :aluguel_id AND alugador_id = :utilizador_id
        ");
        $stmt->execute([':aluguel_id' => $aluguel_id, ':utilizador_id' => $utilizador_id]);

        // Notificar o dono da roupa
        $stmtRoupa = $pdo->prepare("
            SELECT r.utilizador_id AS dono_id, r.descricao 
            FROM roupas r 
            JOIN alugueis a ON r.id = a.roupa_id 
            WHERE a.id = :aluguel_id
        ");
        $stmtRoupa->execute([':aluguel_id' => $aluguel_id]);
        $roupa = $stmtRoupa->fetch();

        $dono_id = $roupa['dono_id'];
        $descricao_roupa = htmlspecialchars($roupa['descricao']);

        $stmtNotificacao = $pdo->prepare("
            INSERT INTO notificacoes (utilizador_id, titulo, mensagem, data) 
            VALUES (:utilizador_id, :titulo, :mensagem, :data)
        ");
        $stmtNotificacao->execute([
            ':utilizador_id' => $dono_id,
            ':titulo' => 'Pedido de Devolução',
            ':mensagem' => "O alugador solicitou a devolução da roupa: '$descricao_roupa'.",
            ':data' => date('Y-m-d H:i:s')
        ]);

        $mensagem = 'Devolução solicitada com sucesso!';
    } elseif ($acao === 'cancelar') {
        // Cancelar aluguel
        $stmt = $pdo->prepare("
            DELETE FROM alugueis 
            WHERE id = :aluguel_id AND alugador_id = :utilizador_id
        ");
        $stmt->execute([':aluguel_id' => $aluguel_id, ':utilizador_id' => $utilizador_id]);

        $mensagem = 'Aluguel cancelado com sucesso!';
    } else {
        $mensagem = 'Ação inválida.';
    }
} catch (PDOException $e) {
    $mensagem = 'Erro ao processar a ação: ' . $e->getMessage();
}
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">  
<link rel="stylesheet" href="/Css/alugarroupa.css">

<div class="container">
    <div class="page-header text-center">
        <!-- Logotipo da Aplicação -->
        <img src="Img/imagemthesharing1.png" alt="Logotipo" class="logo">
        <h1>Processamento de Devolução</h1>
        <p class="message"><?= htmlspecialchars($mensagem) ?></p>
    </div>

    <a href="roupas-alugadas.php" class="btn-back">Voltar</a>
</div>