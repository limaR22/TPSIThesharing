<?php
session_start();
require_once __DIR__ . '/../../infraestrutura/basededados/repositorio-utilizador.php';
require_once __DIR__ . '/../../infraestrutura/basededados/criar-conexao.php';

// Verifica se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verifica se os dados necessários estão presentes
    if (isset($_POST['nome'], $_POST['descricao'], $_FILES['foto'], $_POST['grupo_id'])) {
        $nome = $_POST['nome'];
        $descricao = $_POST['descricao'];
        $grupo_id = $_POST['grupo_id'];
        $utilizador_id = $_SESSION['id']; // Supondo que você tenha o ID do usuário na sessão

        // Processa a imagem
        $foto = $_FILES['foto'];
        $caminhoFoto = '';

        // Verifica se a imagem foi enviada corretamente
        if ($foto && $foto['error'] === 0) {
            // Define um nome único para o arquivo
            $fotoNome = uniqid('roupa_', true) . '.' . pathinfo($foto['name'], PATHINFO_EXTENSION);
            $fotoCaminho = 'uploads/' . $fotoNome; // Pasta 'uploads/'

            // Move o arquivo para a pasta 'uploads/'
            if (move_uploaded_file($foto['tmp_name'], __DIR__ . '/' . $fotoCaminho)) {
                $caminhoFoto = $fotoCaminho;
            } else {
                $_SESSION['erros'] = ['Erro ao mover o arquivo de imagem.'];
                header('Location: /aplicacao/grupo.php?grupo_id=' . $grupo_id);
                exit();
            }
        } else {
            $_SESSION['erros'] = ['Por favor, envie uma imagem válida.'];
            header('Location: /aplicacao/grupo.php?grupo_id=' . $grupo_id);
            exit();
        }

        // Insere a roupa no banco de dados
        $sql = "INSERT INTO roupas (nome, descricao, imagem, grupo_id, utilizador_id) VALUES (:nome, :descricao, :imagem, :grupo_id, :utilizador_id)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':descricao', $descricao);
        $stmt->bindParam(':imagem', $caminhoFoto);
        $stmt->bindParam(':grupo_id', $grupo_id);
        $stmt->bindParam(':utilizador_id', $utilizador_id); // Adicionando o ID do usuário

        if ($stmt->execute()) {
            $_SESSION['sucesso'] = 'Roupa adicionada com sucesso!';
        } else {
            $_SESSION['erros'] = ['Erro ao adicionar a roupa.'];
            error_log(print_r($stmt->errorInfo(), true)); // Log de erro
        }
    } else {
        $_SESSION['erros'] = ['Dados incompletos.'];
    }

    // Redireciona de volta para a página do grupo
    header('Location: /aplicacao/grupo.php?grupo_id=' . $grupo_id);
    exit();
} else {
    // Se não for um POST, redireciona para a página do grupo
    header('Location: /aplicacao/grupos.php');
    exit();
}