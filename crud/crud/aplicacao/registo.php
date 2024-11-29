<?php
# Middleware para garantir que apenas utilizadores não autenticados vejam a página de registro
require_once __DIR__ . '/../src/middleware/middleware-nao-autenticado.php';

# Define o título da página
$titulo = 'Inscrever-se - The Sharing';

# Inicia o cabeçalho
include_once __DIR__ . '/templates/cabecalho.php';
?>

<link rel="stylesheet" href="/Css/registo.css">

<body class="light-mode">
    <div class="top-buttons">
        <a href="/" class="back-home">
            <i class="fas fa-times"></i>
        </a>
        <button class="mode-toggle" id="toggleMode">
            <i class="fas fa-moon"></i>
        </button>
    </div>

    <div class="register-container">
        <h1>Inscrever-se no The Sharing</h1>

        <!-- Botão para inscrever-se com Google -->
        <button class="btn-custom-white">
            <i class="fab fa-google"></i> Inscrever-se com o Google
        </button>

        <p>ou</p>

        <!-- Mensagens de erro ou sucesso -->
        <?php
        if (isset($_SESSION['sucesso'])) {
            echo '<div class="alert alert-success alert-dismissible fade show" role="alert">';
            echo $_SESSION['sucesso'];
            echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
            unset($_SESSION['sucesso']);
        }
        if (isset($_SESSION['erros'])) {
            echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">';
            foreach ($_SESSION['erros'] as $erro) {
                echo $erro . '<br>';
            }
            echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
            unset($_SESSION['erros']);
        }
        ?>

        <!-- Formulário de registro -->
        <form action="/src/controlador/aplicacao/controlar-registo.php" method="post">
            <input type="text" class="input-field" name="nome" placeholder="Nome" maxlength="100" required>
            <input type="email" class="input-field" name="email" placeholder="Email" required>
            <input type="password" class="input-field" name="palavra_passe" placeholder="Senha" minlength="6" required>
            <input type="password" class="input-field" name="confirmar_palavra_passe" placeholder="Confirmar senha" minlength="6" required>
            <button class="btn-custom-black" type="submit" name="utilizador" value="registo">Avançar</button>
        </form>

        <footer>
            Já tem uma conta? <a href="/aplicacao/login.php">Faça login</a>
        </footer>
    </div>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Font Awesome JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
    
    <script src="/javascript/modos.js"></script>
</body>

<?php
include_once __DIR__ . '/templates/rodape.php';
?>
