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

<!-- Link do Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<body class="light-mode">
    <!-- Barra superior -->
    <div class="top-bar">
    <div class="titulo-container">
        <h1 class="titulo-elegante">THE SHARING</h1>
    </div>
        <div class="utilizador-info">
            <h3 class="titulo-utilizador">Olá, <?= $utilizador['nome'] ?? 'Utilizador' ?>!</h3>
        </div>

        <div class="search-bar">
            <input type="text" class="form-control" placeholder="Pesquisar grupos...">
        </div>

        <button class="btn btn-outline-secondary mode-toggle" id="toggleMode">
            <i class="fas fa-moon"></i>
        </button>
    </div>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="top-section">
            <h2 class="titulo-elegante">Menu</h2>
            <br></br>
            <div class="nav-links">
                <a href="\aplicacao\grupos.php" class="nav-link">Grupos</a>
                <a href="\aplicacao\notificacoes.php" class="nav-link">Notificações</a>
                <a href="\aplicacao\perfil.php" class="nav-link">Perfil</a>
            </div>
        </div>
        <div class="logout-section">
            <form action="/src/controlador/aplicacao/controlar-autenticacao.php" method="post">
                <button type="submit" class="btn btn-danger btn-sair" name="utilizador" value="logout">Sair</button>
            </form>
        </div>
    </div>

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
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Anterior</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators2" data-bs-slide="next">
                            <span class="carousel-control-next-icon aria-hidden="true"></span>
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
<<<<<<< Updated upstream

    <!-- Botão flutuante no canto inferior direito -->
    <button type="button" class="btn btn-primary position-fixed bottom-0 end-0 m-3" data-bs-toggle="modal" data-bs-target="#createGroupModal">
        <i class="fas fa-plus"></i>
    </button>

    <!-- Modal de criação de grupo -->
    <div class="modal fade" id="createGroupModal" tabindex="-1" aria-labelledby="createGroupModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createGroupModalLabel">Criar Novo Grupo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="createGroupForm">
                        <div class="mb-3">
                            <label for="groupName" class="form-label">Nome do Grupo</label>
                            <input type="text" class="form-control" id="groupName" name="nome" required>
                        </div>
                        <div class="mb-3">
                            <label for="groupGenre" class="form-label">Gênero</label>
                            <select class="form-select" id="groupGenre" name="genero" required>
                                <option value="" disabled selected>Selecione o Gênero</option>
                                <option value="masculino">Masculino</option>
                                <option value="feminino">Feminino</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tamanhos</label>
                            <div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="tamanho[]" value="S">
                                    <label class="form-check-label">S</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="tamanho[]" value="M">
                                    <label class="form-check-label">M</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="tamanho[]" value="L">
                                    <label class="form-check-label">L</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="tamanho[]" value="XL">
                                    <label class="form-check-label">XL</label>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="groupDescription" class="form-label">Descrição do Grupo</label>
                            <textarea class="form-control" id="groupDescription" name="descricao" rows="4" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-success">Criar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

=======
</div>
>>>>>>> Stashed changes
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Font Awesome para ícones -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>

    <!-- Modo escuro/claro e outras funções -->
    <script src="/javascript/modos.js"></script>
</body>
</html>
