<?php
# Middleware para garantir que apenas utilizadores não autenticados vejam a página de login
require_once __DIR__ . '/../src/middleware/middleware-nao-autenticado.php';

# Define o título da página
$titulo = 'Entrar - The Sharing';

# Inicia o cabeçalho
include_once __DIR__ . '/templates/cabecalho.php';

?>

<link rel="stylesheet" href="/Css/login.css">

<body class="light-mode">
    <!-- Botões de topo -->
    <div class="top-buttons">
        <!-- Botão para voltar ao home -->
        <a href="/" class="back-home">
            <i class="fas fa-times"></i>
        </a>
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

    <script src="/javascript/modos.js"></script>
</body>

<?php
# Inclui o rodapé
include_once __DIR__ . '/templates/rodape.php';
?>
