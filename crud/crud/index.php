<?php
# FUNÇÕES AUXILIADORAS
require_once __DIR__ . '/src/middleware/middleware-nao-autenticado.php';
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>The Sharing</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome para ícones -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            height: 100vh;
            display: flex;
            transition: background-color 0.3s, color 0.3s;
        }
        .light-mode {
            background-color: #ffffff; /* Fundo branco */
            color: #000; /* Texto preto */
        }
        .dark-mode {
            background-color: #000; /* Fundo preto */
            color: #fff; /* Texto branco */
        }
        .left-section {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #ffffff; /* Fundo claro para destacar a imagem */
            transition: background-color 0.3s;
        }
        .dark-mode .left-section {
            background-color: #000000; /* Fundo escuro no modo dark */
        }
        .left-section img {
            max-width: 80%;
            height: auto;
        }
        .right-section {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 0 50px;
        }
        h1 {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 2rem;
        }
        .btn-custom-white, .btn-custom-blue {
            font-size: 1rem;
            padding: 0.8rem 1rem;
            border-radius: 20px;
            width: 300px;
            margin: 10px 0;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: background-color 0.3s, color 0.3s;
        }
        .btn-custom-white {
            background-color: #fff;
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
        .btn-custom-blue {
            background-color: #007bff;
            color: white;
            border: none;
        }
        .btn-custom-blue:hover {
            background-color: #0056b3;
        }
        footer {
            position: absolute;
            bottom: 20px;
            font-size: 0.8rem;
            text-align: center;
            width: 100%;
        }
        footer a {
            color: #007bff;
            text-decoration: none;
        }
        footer a:hover {
            text-decoration: underline;
        }
        /* Botão da lua */
        .mode-toggle {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: transparent;
            border: none;
            font-size: 1.5rem;
            color: #000;
            cursor: pointer;
            transition: color 0.3s;
        }
        .dark-mode .mode-toggle {
            color: #fff; /* Ícone da lua fica branco no modo escuro */
        }
    </style>
</head>
<body class="light-mode">
    <button class="mode-toggle" id="toggleMode">
        <i class="fas fa-moon"></i>
    </button>
    <div class="left-section">
    <img src="Img/imagemthesharing1.png" alt="Logo grande">
    </div>
    <div class="right-section">
        <h1>Partilhe agora!</h1>
        <button class="btn-custom-white">
            <i class="fab fa-google"></i> Inscrever-se com Google       
        </button><br>
        <button class="btn-custom-blue"onclick="location.href='/aplicacao/registo.php'">Criar Conta</button><br>
        <h3>Já tem uma conta?</h3>
        <button class="btn-custom-blue" onclick="location.href='/aplicacao/login.php'">Entrar</button>
    </div>
    <footer>
        Bem-vindo ao The Sharing! A plataforma de compartilhamento de roupas onde tu pode compartilhar e emprestar peças com teus amigos! <br>
        Para mais detalhes, consulte <a href="">Mais Sobre</a>.
    </footer>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Font Awesome JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
    <script>
        // JavaScript para alternar entre modos
        const toggleButton = document.getElementById('toggleMode');
        const body = document.body;

        toggleButton.addEventListener('click', () => {
            body.classList.toggle('dark-mode');
            body.classList.toggle('light-mode');

            // Atualizar o ícone da lua
            if (body.classList.contains('dark-mode')) {
                toggleButton.innerHTML = '<i class="fas fa-sun"></i>'; // Ícone do sol
            } else {
                toggleButton.innerHTML = '<i class="fas fa-moon"></i>'; // Ícone da lua
            }
        });
    </script>
</body>
</html>
