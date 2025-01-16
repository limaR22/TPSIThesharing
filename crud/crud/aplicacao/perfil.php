<?php
# CARREGA MIDDLEWARE PAGARA GARANTIR QUE APENAS UTILIZADORES AUTENTICADOS ACESSEM ESTE SITIO
require_once __DIR__ . '/../src/middleware/middleware-utilizador.php';

# CARREGA O CABECALHO PADRÃO COM O TÍTULO
$titulo = ' - Perfil';
include_once __DIR__ . '/templates/cabecalho.php';

# ACESSA DE FUNÇÕES AUXILIADORAS
@require_once __DIR__ . '/../src/auxiliadores/auxiliador.php';
$utilizador = utilizador();

# Consultar a imagem de perfil no banco de dados (supondo que existe um campo 'imagem_perfil' no banco de dados)
$imagem_perfil = isset($utilizador['perfil']) && !empty($utilizador['perfil']) ? $utilizador['perfil'] : 'https://i.pinimg.com/236x/21/9e/ae/219eaea67aafa864db091919ce3f5d82.jpg'; // Imagem padrão
?>

<body class="light-mode">
  
  <div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h1 class="fw-bold text-dark">O meu Perfil</h1>
    </div>

    <!-- Exibir a imagem de perfil -->
    <div class="text-center mb-4">
      <img src="<?= $imagem_perfil ?>" alt="Imagem de Perfil" class="img-fluid rounded-circle" style="width: 150px; height: 150px; object-fit: cover;">
    </div>

    <section class="mb-4">
      <div class="d-flex justify-content-between mb-3">
        <a href="/aplicacao/" class="btn btn-outline-secondary">Voltar</a>
        <a href="/aplicacao/palavra-passe.php" class="btn btn-warning">Alterar Palavra Passe</a>
      </div>

      <!-- Mostrar mensagens de sucesso ou erro -->
      <?php if (isset($_SESSION['sucesso'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          <?= $_SESSION['sucesso'] ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['sucesso']); ?>
      <?php endif; ?>

      <?php if (isset($_SESSION['erros'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
          <?php foreach ($_SESSION['erros'] as $erro): ?>
            <p><?= $erro ?></p>
          <?php endforeach; ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['erros']); ?>
      <?php endif; ?>
    </section>

    <section>
      <form enctype="multipart/form-data" action="/src/controlador/admin/controlar-utilizador.php" method="post" class="shadow-sm p-4 bg-white rounded">
        <div class="mb-3">
          <label for="nome" class="form-label">Nome</label>
          <input type="text" class="form-control" name="nome" id="nome" value="<?= isset($_REQUEST['nome']) ? $_REQUEST['nome'] : $utilizador['nome'] ?>" required>
        </div>

        <div class="mb-3">
          <label for="apelido" class="form-label">Apelido</label>
          <input type="text" class="form-control" name="apelido" id="apelido" value="<?= isset($_REQUEST['apelido']) ? $_REQUEST['apelido'] : $utilizador['apelido'] ?>" required>
        </div>

        <div class="mb-3">
          <label for="nif" class="form-label">NIF</label>
          <input type="tel" class="form-control" name="nif" id="nif" maxlength="9" value="<?= isset($_REQUEST['nif']) ? $_REQUEST['nif'] : $utilizador['nif'] ?>" required>
        </div>

        <div class="mb-3">
          <label for="telemovel" class="form-label">Telemóvel</label>
          <input type="tel" class="form-control" name="telemovel" id="telemovel" maxlength="9" value="<?= isset($_REQUEST['telemovel']) ? $_REQUEST['telemovel'] : $utilizador['telemovel'] ?>" required>
        </div>

        <div class="mb-3">
          <label for="email" class="form-label">E-mail</label>
          <input type="email" class="form-control" name="email" id="email" value="<?= isset($_REQUEST['email']) ? $_REQUEST['email'] : $utilizador['email'] ?>" required>
        </div>
        <div class="text-center">
          <button class="btn btn-dark w-100" type="submit" name="utilizador" value="perfil">Alterar</button>
        </div>
      </form>
    </section>
  </div>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

  <script src="/javascript/modos.js"></script>
</body>
