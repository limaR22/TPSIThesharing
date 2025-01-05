<?php
# Middleware para garantir que apenas utilizadores autenticados acessem este sítio
require_once __DIR__ . '/../src/middleware/middleware-utilizador.php';

# Acessa funções auxiliares
require_once __DIR__ . '/../src/auxiliadores/auxiliador.php';

# Conectar ao banco de dados
require_once __DIR__ . '/../src/infraestrutura/basededados/criar-conexao.php';

# Obter o utilizador atual
$utilizador_id = $_SESSION['id'];

# Verificar se o ID da roupa foi enviado
if (!isset($_POST['roupa_id'])) {
    echo "Erro: Nenhuma peça de roupa selecionada.";
    exit();
}

$roupa_id = $_POST['roupa_id'];

# Verificar se o utilizador é o dono da roupa
$stmt = $pdo->prepare("SELECT utilizador_id FROM roupas WHERE id = :roupa_id");
$stmt->execute([':roupa_id' => $roupa_id]);
$dono_id = $stmt->fetchColumn();

if ($dono_id == $utilizador_id) {
    echo "Erro: Você não pode alugar a sua própria peça.";
    exit();
}

# Verificar se a peça está disponível para aluguel
$stmt = $pdo->prepare("SELECT status FROM roupas WHERE id = :roupa_id");
$stmt->execute([':roupa_id' => $roupa_id]);
$status = $stmt->fetchColumn();

if ($status != 'disponível') {
    echo "Erro: Esta peça já foi alugada e não está disponível.";
    exit();
}

# Processar o aluguel
try {
    # Atualizar o status da roupa para "alugada"
    $stmtUpdateStatus = $pdo->prepare("UPDATE roupas SET status = 'alugada' WHERE id = :roupa_id");
    $stmtUpdateStatus->execute([':roupa_id' => $roupa_id]);

    # Inserir o aluguel na tabela de alugueis
    $stmt = $pdo->prepare("INSERT INTO alugueis (roupa_id, alugador_id, data_aluguel) VALUES (:roupa_id, :alugador_id, :data_aluguel)");
    $stmt->execute([
        ':roupa_id' => $roupa_id,
        ':alugador_id' => $utilizador_id,
        ':data_aluguel' => date('Y-m-d H:i:s')
    ]);

    # Adicionar notificações
    $stmtNotificacaoDono = $pdo->prepare("INSERT INTO notificacoes (utilizador_id, titulo, mensagem, data) VALUES (:utilizador_id, :titulo, :mensagem, :data)");
    $stmtNotificacaoDono->execute([
        ':utilizador_id' => $dono_id,
        ':titulo' => 'Notificação de Aluguel',
        ':mensagem' => "A sua peça foi alugada!",
        ':data' => date('Y-m-d H:i:s')
    ]);

    $stmtNotificacaoAlugador = $pdo->prepare("INSERT INTO notificacoes (utilizador_id, titulo, mensagem, data) VALUES (:utilizador_id, :titulo, :mensagem, :data)");
    $stmtNotificacaoAlugador->execute([
        ':utilizador_id' => $utilizador_id,
        ':titulo' => 'Notificação de Aluguel',
        ':mensagem' => "Você alugou a peça com sucesso!",
        ':data' => date('Y-m-d H:i:s')
    ]);

    echo "Aluguel realizado com sucesso!";
} catch (PDOException $e) {
    echo "Erro ao processar o aluguel: " . $e->getMessage();
}
?>
