<?php
# Middleware para garantir que apenas utilizadores autenticados acessem este sítio
require_once __DIR__ . '/../src/middleware/middleware-utilizador.php';

# Acessa funções auxiliares
@require_once __DIR__ . '/../src/auxiliadores/auxiliador.php';

# Carrega o utilizador atual
$utilizador = utilizador();

# Carrega o cabeçalho padrão com o título
$titulo = '- Aplicação';
include_once __DIR__ . '/templates/cabecalho.php';
?>

<!-- Link para o CSS externo -->
<link rel="stylesheet" href="../Css/index.css">

<body class="light-mode">
    <!-- Barra superior -->
    <div class="top-bar">
        <!-- Saudação -->
        <div>
            <h3>Olá, <?= $utilizador['nome'] ?? 'Utilizador' ?>!</h3>
        </div>

        <!-- Barra de pesquisa -->
        <div class="search-bar">
            <input type="text" placeholder="Pesquisar grupos...">
        </div>

        <!-- Botão de alternar tema -->
        <button class="mode-toggle" id="toggleMode">
            <i class="fas fa-moon"></i>
        </button>
    </div>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="top-section">
            <h2>Menu</h2>
            <div class="nav-links">
                <a href="#">Grupos</a>
                <a href="#">Notificações</a>
                <a href="\aplicacao\perfil.php">Perfil</a>
            </div>
        </div>
        <div class="logout-section">
            <form action="/src/controlador/aplicacao/controlar-autenticacao.php" method="post">
                <button type="submit" name="utilizador" value="logout">Sair</button>
            </form>
        </div>
    </div>

    <!-- Conteúdo principal -->
    <div class="main-content">
        <h1>Bem-vindo à The Sharing!</h1>
    </div>

    <!-- Botão flutuante para abrir o modal -->
    <a href="#" class="floating-button" id="openModalButton">
        <i class="fas fa-plus"></i>
    </a>

    <!-- Modal de criação de grupo -->
    <div class="modal" id="createGroupModal">
        <h2>Criar Novo Grupo</h2>
        <form id="createGroupForm">
            <input type="text" name="nome" placeholder="Nome do Grupo" required>
            <select name="genero" required>
                <option value="" disabled selected>Selecione o Gênero</option>
                <option value="masculino">Masculino</option>
                <option value="feminino">Feminino</option>
            </select>
            <div class="size-checkboxes">
                <label><input type="checkbox" name="tamanho[]" value="S"> S</label>
                <label><input type="checkbox" name="tamanho[]" value="M"> M</label>
                <label><input type="checkbox" name="tamanho[]" value="L"> L</label>
                <label><input type="checkbox" name="tamanho[]" value="XL"> XL</label>
            </div>
            <textarea name="descricao" rows="4" placeholder="Descrição do Grupo" required></textarea>
            <button type="submit">Criar</button>
        </form>
    </div>
    <!-- Overlay para o modal -->
    <div class="modal-overlay" id="modalOverlay"></div>

    <!-- Font Awesome para ícones -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
    <script>
        const modal = document.getElementById('createGroupModal');
        const overlay = document.getElementById('modalOverlay');
        const openModalButton = document.getElementById('openModalButton');

        // Abrir o modal
        openModalButton.addEventListener('click', (e) => {