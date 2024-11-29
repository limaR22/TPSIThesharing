<?php
# CARREGA MIDDLEWARE PAGARA GARANTIR QUE APENAS UTILIZADORES AUTENTICADOS ACESSEM ESTE SITIO
@include_once __DIR__ . '.\src\middleware\middleware-utilizador.php';

# CARREGA O CABECALHO PADRÃO COM O TÍTULO
$titulo = ' - Alterar Palavra Passe';
include_once __DIR__ . '/templates/cabecalho.php';

# ACESSA DE FUNÇÕES AUXILIADORAS. 
# NOTA: O SIMBOLO ARROBA SERVE PARA NÃO MOSTRAR MENSAGEM DE WARNING, POIS A FUNÇÃO ABAIXO TAMBÉM INICIA SESSÕES
@include_once __DIR__ . '/../src/auxiliadores/auxiliador.php';

# CARREGA O UTILIZADOR ATUAL. PROVENIENTE DE FUNÇÕES AUXILIADORAS ACIMA
$utilizador = utilizador();
?>

<link rel="stylesheet" href="/Css/palavra.css">

<body class="light-mode">
  <div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h1 class="fw-bold text-dark">Alterar Palavra Passe</h1>
      <!-- Botão de alternância de modo -->
    </div>

    <section class="mb-4">
      <div class="d-flex justify-content-between mb-3">
        <a href="/aplicacao/perfil.php" class="btn btn-outline-secondary">Voltar</a>
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
      <form action="/src/controlador/admin/controlar-utilizador.php" method="post" class="shadow-sm p-4 bg-white rounded">
        <div class="mb-3">
          <label for="nome" class="form-label">Nome</label>
          <input type="text" class="form-control" name="nome" id="nome" value="<?= $utilizador['nome'] ?>" readonly>
        </div>

        <div class="mb-3">
          <label for="palavra_passe" class="form-label">Nova Palavra Passe</label>
          <input type="password" class="form-control" name="palavra_passe" id="palavra_passe" maxlength="255" required>
        </div>

        <div class="mb-3">
          <label for="confirmar_palavra_passe" class="form-label">Confirmar Palavra Passe</label>
          <input type="password" class="form-control" name="confirmar_palavra_passe" id="confirmar_palavra_passe" maxlength="255" required>
        </div>

        <div class="text-center">
          <button class="btn btn-dark w-100" type="submit" name="utilizador" value="palavra_passe">Alterar</button>
        </div>
      </form>
    </section>
  </div>

  <?php include_once __DIR__ . '/templates/rodape.php'; ?>
  <script src="/javascript/modos.js"></script>
</body>
