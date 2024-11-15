<?php
# Middleware para garantir que apenas utilizadores não autenticados vejam a página de login
require_once __DIR__ . '/../src/middleware/middleware-nao-autenticado.php';

# Define o título da página
$titulo = 'Entrar - The Sharing';

# Inicia o cabeçalho
include_once __DIR__ . '/templates/cabecalho.php';
?>

<style>
    /* Reset básico */
    body {
        margin: 0;
        padding: 0;
        font-family: Arial, sans-serif;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        transition: background-color 0.3s, color 0.3s;
    }

    .light-mode {
        background-color: #ffffff; /* Fundo branco */
        color: #000; /* Texto preto */
    }

    .dark-mode {
        background-color: #000000; /* Fundo preto */
        color: #fff; /* Texto branco */
    }

    /* Centralizar conteúdo */
    .login-container {
        text-align: center;
        width: 100%;
        max-width: 400px;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    h1 {
        font-size: 1.8rem;
        font-weight: bold;
        margin-bottom: 20px;
    }

    .btn-custom-white,
    .btn-custom-black {
        font-size: 1rem;
        padding: 0.8rem 1rem;
        border-radius: 20px;
        width: 100%;
        margin: 10px 0;
        text-align: center;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        transition: background-color 0.3s, color 0.3s;
    }

    /* Botão branco */
    .btn-custom-white {
        background-color: #ffffff;
        color: #000;
        border: 1px solid #ddd;
    }

    .btn-custom-white:hover {
        background-color: #f5f5f5;
    }

    .dark-mode .btn-custom-white {
        background-color: #333;
        color: #fff;
        border: 1px solid #444;
    }

    /* Botão preto */
    .btn-custom-black {
        background-color: #000;
        color: #fff;
        border: 1px solid #ddd;
    }

    .dark-mode .btn-custom-black {
        background-color: #fff;
        color: #000;
        border: 1px solid #ddd;
    }

    .btn-custom-black:hover {
        opacity: 0.9;
    }

    .input-field {
        width: 100%;
        padding: 0.8rem;
        border: 1px solid #ccc;
        border-radius: 10px;
        margin: 15px 0;
        font-size: 1rem;
        outline: none;
        transition: border-color 0.3s;
    }

    .input-field:focus {
        border-color: #007bff;
    }

    footer {
        margin-top: 20px;
        font-size: 0.9rem;
    }

    footer a {
        color: #007bff;
        text-decoration: none;
    }

    footer a:hover {
        text-decoration: underline;
    }

    /* Botões de topo */
    .top-buttons {
        position: absolute;
        top: 20px;
        left: 20px;
        display: flex;
        gap: 10px;
    }

    .mode-toggle {
        font-size: 1.5rem;
        background-color: transparent;
        border: none;
        cursor: pointer;
        transition: transform 0.2s;
    }

    .mode-toggle:hover {
        transform: scale(1.1);
    }

    .back-home {
        font-size: 1rem;
        padding: 5px 15px;
        background-color: transparent;
        color: inherit;
        border: 1px solid currentColor;
        border-radius: 5px;
        text-decoration: none;
        transition: background-color 0.3s, color 0.3s;
    }

    .light-mode .back-home:hover {
        background-color: #f5f5f5;
    }

    .dark-mode .back-home:hover {
        background-color: #333;
        color: #fff;
    }

    .dark-mode .mode-toggle {
        color: #fff; /* Ícone da lua fica branco no modo escuro */
    }
</style>

<body class="light-mode">
    <!-- Botões de topo -->
    <div class="top-buttons">
        <!-- Botão para voltar ao home -->
        <a href="/" class="back-home">Voltar</a>

        <!-- Botão para alternar tema -->
        <button class="mode-toggle" id="toggleMode">
            <i class="fas fa-moon"></i>
        </button>
    </div>

    <!-- Container principal -->
    <div class="login-container">
        <h1>Entrar no The Sharing</h1>

        <!-- Botão para login com Google -->
        <button class="btn-custom-white">
            <i class="fab fa-google"></i> Fazer login com o Google
        </button>

        <!-- Separador -->
        <p>ou</p>

        <!-- Exibição de erros caso existam -->
        <?php
        if (isset($_SESSION['erros'])) {
            echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">';
            foreach ($_SESSION['erros'] as $erro) {
                echo $erro . '<br>';
            }
            echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
            unset($_SESSION['erros']);
            echo '</div>';
        }
        ?>

        <!-- Formulário de login -->
        <form action="/src/controlador/aplicacao/controlar-autenticacao.php" method="post">
            <!-- Campo de entrada -->
            <input type="email" class="input-field" placeholder="E-mail do Utilizador" name="email" maxlength="255" 
                value="<?= isset($_REQUEST['email']) ? $_REQUEST['email'] : '' ?>">

            <!-- Campo de entrada -->
            <input type="password" class="input-field" placeholder="Senha" name="palavra_passe" maxlength="255" 
                value="<?= isset($_REQUEST['palavra_passe']) ? $_REQUEST['palavra_passe'] : '' ?>">

            <!-- Botão de avançar -->
            <button class="btn-custom-black" type="submit" name="utilizador" value="login">Avançar</button>
        </form>

        <!-- Link de esquecimento de senha -->
        <div>
        <a href="/aplicacao/EsqueceuSenha.php" class="btn-custom-white">Esqueceu sua senha?</a>
        </div>
        <!-- Link para criar uma conta -->
        <footer>
            Não tem uma conta? <a href="/aplicacao/registo.php">Inscreva-se</a>
        </footer>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Font Awesome JS -->
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
# Inclui o rodapé
include_once __DIR__ . '/templates/rodape.php';
?>
