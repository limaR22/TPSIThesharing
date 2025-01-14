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
<link rel="stylesheet" href="/Css/index.css">
<link rel="stylesheet" href="/Css/footer.css">

<!-- Link do Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<body class="light-mode">
    <!-- Barra superior principal -->
    <div class="top-bar">
        <div class="titulo-container">
            <h1 class="titulo-elegante">THE SHARING</h1>
        </div>
        <div class="utilizador-info">
            <h3 class="titulo-utilizador">Olá, <?= $utilizador['nome'] ?? 'Utilizador' ?>!</h3>
        </div>
        <button class="btn btn-outline-secondary mode-toggle" id="toggleMode">
            <i class="fas fa-moon"></i>
        </button>
    </div>

   <!-- Nova topbar para links de navegação -->
<div class="nav-top-bar">
    <div class="nav-links">
        <a href="\aplicacao\grupos.php" class="nav-link">
            <i class="fas fa-users"></i> Grupos
        </a>
        <a href="\aplicacao\notificacoes.php" class="nav-link">
            <i class="fas fa-bell"></i> Notificações
        </a>
        <a href="\aplicacao\perfil.php" class="nav-link">
            <i class="fas fa-user"></i> Perfil
        </a>
        <a href="\aplicacao\roupas-alugadas.php" class="nav-link"> <!-- Link para a página de alugueis -->
                <i class="fas fa-tags"></i> Alugueis
         </a>
    </div>
    <div class="logout-section">
        <form action="/src/controlador/aplicacao/controlar-autenticacao.php" method="post">
            <button type="submit" class="btn btn-danger btn-sair" name="utilizador" value="logout">
                <i class="fas fa-sign-out-alt"></i> Sair
            </button>
        </form>
    </div>
</div>
<!-- Conteúdo principal -->
<div class="main-content">
    <div class="container">
        <div class="row">
            <!-- Primeiro Carrossel -->
            <div class="col-md-4">
                <div class="carousel-container">
                    <div id="carouselExampleIndicators1" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-indicators">
                            <button type="button" data-bs-target="#carouselExampleIndicators1" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                            <button type="button" data-bs-target="#carouselExampleIndicators1" data-bs-slide-to="1" aria-label="Slide 2"></button>
                            <button type="button" data-bs-target="#carouselExampleIndicators1" data-bs-slide-to="2" aria-label="Slide 3"></button>
                        </div>
                        <div class="carousel-inner">
                            <div class="carousel-item active">
                                <img src="Img/1.png" class="d-block w-100" alt="Primeira imagem">
                            </div>
                            <div class="carousel-item">
                                <img src="Img/2.png" class="d-block w-100" alt="Segunda imagem">
                            </div>
                            <div class="carousel-item">
                                <img src="Img/3.png" class="d-block w-100" alt="Terceira imagem">
                            </div>
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIndicators1" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Anterior</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators1" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Próximo</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Segundo Carrossel -->
            <div class="col-md-4">
                <div class="carousel-container">
                    <div id="carouselExampleIndicators2" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-indicators">
                            <button type="button" data-bs-target="#carouselExampleIndicators2" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                            <button type="button" data-bs-target="#carouselExampleIndicators2" data-bs-slide-to="1" aria-label="Slide 2"></button>
                            <button type="button" data-bs-target="#carouselExampleIndicators2" data-bs-slide-to="2" aria-label="Slide 3"></button>
                        </div>
                        <div class="carousel-inner">
                            <div class="carousel-item active">
                                <img src="Img/4.png" class="d-block w-100" alt="Primeira imagem">
                            </div>
                            <div class="carousel-item">
                                <img src="Img/5.png" class="d-block w-100" alt="Segunda imagem">
                            </div>
                            <div class="carousel-item">
                                <img src="Img/6.png" class="d-block w-100" alt="Terceira imagem">
                            </div>
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIndicators2" data-bs-slide="prev">
                            <span class="carousel-control -prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Anterior</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators2" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Próximo</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Terceiro Carrossel -->
            <div class="col-md-4">
                <div class="carousel-container">
                    <div id="carouselExampleIndicators3" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-indicators">
                            <button type="button" data-bs-target="#carouselExampleIndicators3" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                            <button type="button" data-bs-target="#carouselExampleIndicators3" data-bs-slide-to="1" aria-label="Slide 2"></button>
                            <button type="button" data-bs-target="#carouselExampleIndicators3" data-bs-slide-to="2" aria-label="Slide 3"></button>
                        </div>
                        <div class="carousel-inner">
                            <div class="carousel-item active">
                                <img src="Img/7.png" class="d-block w-100" alt="Primeira imagem">
                            </div>
                            <div class="carousel-item">
                                <img src="Img/8.png" class="d-block w-100" alt="Segunda imagem">
                            </div>
                            <div class="carousel-item">
                                <img src="Img/9.png" class="d-block w-100" alt="Terceira imagem">
                            </div>
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIndicators3" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Anterior</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators3" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Próximo</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Font Awesome para ícones -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>

    <!-- Modo escuro/claro e outras funções -->
    <script src="/javascript/modos.js"></script>

</body>
<!-- Incluir o footer -->
<?php include_once __DIR__ . '/../admin/templates/footer.php'; ?>
</html>