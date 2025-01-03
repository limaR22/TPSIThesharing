<?php
// Conectar à base de dados
require_once __DIR__ . '/../src/infraestrutura/basededados/criar-conexao.php';

// Verificar se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $roupa_id = $_POST['roupa_id'] ?? null;
    $utilizador_id = $_SESSION['id']; // ID do usuário autenticado

    if ($roupa_id && $utilizador_id) {
        // Inserir o aluguel na base de dados
        $stmt = $pdo->prepare("
            INSERT INTO alugueis (roupa_id, utilizador_id, data_aluguel) 
            VALUES (:roupa_id, :utilizador_id, NOW())
        ");
        $stmt->execute([
            ':roupa_id' => $roupa_id,
            ':utilizador_id' => $utilizador_id,
        ]);

        // Redirecionar para a página de sucesso ou mostrar uma mensagem
        header("Location: sucesso-aluguel.php");
        exit;
    } else {
        // Se algo deu errado, exibir uma mensagem de erro
        echo "Erro ao processar o aluguel.";
    }
}
?>
