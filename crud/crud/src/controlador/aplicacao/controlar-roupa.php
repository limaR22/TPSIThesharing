<?php
session_start();
require_once __DIR__ . '/../../infraestrutura/basededados/criar-conexao.php';
require_once __DIR__ . '/../../validacao/aplicacao/validar-roupa.php';

// Verifica se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Valida os dados do formulário
        $dadosValidados = validarRoupa($_POST);

        if (isset($dadosValidados['invalido'])) {
            $_SESSION['erros'] = $dadosValidados['invalido'];
            header('Location: /aplicacao/grupo.php?grupo_id=' . $_POST['grupo_id']);
            exit();
        }

        $nome = trim($dadosValidados['nome']);
        $descricao = trim($dadosValidados['descricao']);
        $grupo_id = (int) $dadosValidados['grupo_id'];
        $utilizador_id = (int) $_SESSION['id'];

        // Processar imagem
        $foto = $_FILES['foto'];
        $caminhoFoto = null;

        if ($foto && $foto['error'] === UPLOAD_ERR_OK) {
            $fotoNome = uniqid('roupa_', true) . '.' . pathinfo($foto['name'], PATHINFO_EXTENSION);
            $fotoCaminho = __DIR__ . '/../../../uploads/' . $fotoNome;

            if (move_uploaded_file($foto['tmp_name'], $fotoCaminho)) {
                $caminhoFoto = 'uploads/' . $fotoNome;
            } else {
                throw new Exception('Erro ao mover o arquivo de imagem.');
            }
        }

        // Inserir dados no banco
        $sql = "INSERT INTO roupas (nome, descricao, imagem, grupo_id, utilizador_id) 
                VALUES (:nome, :descricao, :imagem, :grupo_id, :utilizador_id)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':descricao', $descricao);
        $stmt->bindParam(':imagem', $caminhoFoto);
        $stmt->bindParam(':grupo_id', $grupo_id, PDO::PARAM_INT);
        $stmt->bindParam(':utilizador_id', $utilizador_id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            $_SESSION['sucesso'] = 'Roupa adicionada com sucesso!';
        } else {
            throw new Exception('Erro ao inserir roupa no banco de dados.');
        }

        header('Location: /aplicacao/grupo.php?grupo_id=' . $grupo_id);
        exit();
    } catch (Exception $e) {
        $_SESSION['erros'] = [$e->getMessage()];
        header('Location: /aplicacao/grupo.php?grupo_id=' . $_POST['grupo_id']);
        exit();
    }
} else {
    header('Location: /aplicacao/grupos.php');
    exit();
}
