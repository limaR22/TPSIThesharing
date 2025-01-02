<?php
require_once __DIR__ . '/../src/infraestrutura/basededados/criar-conexao.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['grupo_id'])) {
    $grupo_id = $_POST['grupo_id'];

    // Excluir o grupo do banco de dados
    $stmt = $pdo->prepare("DELETE FROM grupo WHERE id = :id");
    $stmt->execute([':id' => $grupo_id]);

    header("Location: grupos.php"); // Redireciona após exclusão
    exit();
}
?>