<?php
// Definir $searchTerm como uma string vazia se não estiver definida
$searchTerm = $_GET['search'] ?? '';  // Recebe o valor da query string ou uma string vazia
?>

<link rel="stylesheet" href="/Css/sidebar.css">

<div class="top-bar">
    <div>
        <h3>Olá, <?= $_SESSION['nome'] ?? 'Utilizador' ?>!</h3>
    </div>


    <button class="btn btn-outline-secondary mode-toggle" id="toggleMode">
        <i class="fas fa-moon"></i>
    </button>
</div>
<div class="sidebar">
    <div class="top-section">
        <h2>Menu</h2>
        <div class="nav-links">
            <a href="/aplicacao/index.php" class="nav-link">Início</a>
            <a href="/aplicacao/notificacoes.php" class="nav-link">Notificações</a>
            <a href="/aplicacao/perfil.php" class="nav-link">Perfil</a>
        </div>
    </div>
    <div class="logout-section">
        <form action="/src/controlador/aplicacao/controlar-autenticacao.php" method="post">
            <button type="submit" class="btn btn-danger" name="utilizador" value="logout">Sair</button>
        </form>
    </div>
</div>
