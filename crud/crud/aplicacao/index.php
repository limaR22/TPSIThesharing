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

<style>
    /* Reset básico */
    body {
        margin: 0;
        font-family: Arial, sans-serif;
        display: flex;
        height: 100vh;
        transition: background-color 0.3s, color 0.3s;
    }

    /* Modos claro e escuro */
    .light-mode {
        background-color: #ffffff; /* Fundo branco */
        color: #000000; /* Texto preto */
    }

    .dark-mode {
        background-color: #000000; /* Fundo preto */
        color: #ffffff; /* Texto branco */
    }

    /* Sidebar estilizada */
    .sidebar {
        width: 250px;
        background-color: #111111;
        color: #ffffff;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        padding: 20px;
        box-sizing: border-box;
        transition: background-color 0.3s, color 0.3s;
        margin-top: 60px; /* Altura da barra superior */
        height: calc(100vh - 60px); /* Altura da tela menos a altura da barra superior */
        position: fixed;
        top: 0;
        left: 0;
    }

    .light-mode .sidebar {
        background-color: #f7f7f7;
        color: #000000;
    }

    .sidebar .top-section h2 {
        font-size: 1.2rem;
        margin-bottom: 20px;
    }

    .sidebar .nav-links {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .sidebar .nav-links a {
        text-decoration: none;
        color: inherit;
        font-size: 1rem;
    }

    .sidebar .nav-links a:hover {
        opacity: 0.8;
    }

    .logout-section form button {
        width: 100%;
        padding: 10px;
        background-color: #dc3545;
        border: none;
        border-radius: 5px;
        color: #ffffff;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .light-mode .logout-section form button {
        background-color: #ff6666;
    }

    .logout-section form button:hover {
        background-color: #a71d2a;
    }

    .main-content {
        margin-left: 250px; /* Largura da sidebar */
        margin-top: 60px; /* Altura da barra superior */
        padding: 20px;
        flex: 1;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    /* Barra superior */
    .top-bar {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 60px;
        background-color: #f1f1f1;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 20px;
        box-sizing: border-box;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        transition: background-color 0.3s, color 0.3s;
        z-index: 1000;
    }

    .dark-mode .top-bar {
        background-color: #222222;
        color: #ffffff;
    }

    /* Barra de pesquisa */
    .search-bar {
        flex: 1;
        margin: 0 20px;
        display: flex;
        justify-content: center;
    }

    .search-bar input {
        width: 50%;
        padding: 10px;
        font-size: 1rem;
        border: 1px solid #ccc;
        border-radius: 20px;
        outline: none;
        transition: border-color 0.3s;
    }

    .dark-mode .search-bar input {
        background-color: #333333;
        color: #ffffff;
        border: 1px solid #555555;
    }

    .search-bar input:focus {
        border-color: #007bff;
    }

    /* Botão de alternar tema */
    .mode-toggle {
        font-size: 1.5rem;
        background-color: transparent;
        border: none;
        color: inherit;
        cursor: pointer;
        transition: transform 0.2s;
    }

    .mode-toggle:hover {
        transform: scale(1.1);
    }
</style>

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
            <div class="nav-links">
                <a href="/grupos">Grupos</a>
                <a href="/criar">Criar grupos</a>
                <a href="/notificacoes">Notificações</a>
                <a href="/aplicacao/perfil.php">Perfil</a>
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

    <!-- Font Awesome para ícones -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
    <script>
        // Alternar entre modo claro e escuro
        const toggleButton = document.getElementById('toggleMode');
        const body = document.body;

        toggleButton.addEventListener('click', () => {
            body.classList.toggle('dark-mode');
            body.classList.toggle('light-mode');

            // Atualiza o ícone do botão
            if (body.classList.contains('dark-mode')) {
                toggleButton.innerHTML = '<i class="fas fa-sun"></i>'; // Ícone do sol
            } else {
                toggleButton.innerHTML = '<i class="fas fa-moon"></i>'; // Ícone da lua
            }
        });
    </script>
</body>
<?php
?>
