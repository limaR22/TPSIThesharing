<?php
session_start();
require_once __DIR__ . '/../../infraestrutura/basededados/repositorio-grupo.php';
require_once __DIR__ . '/../../validacao/aplicacao/validacao-grupo.php';
require_once __DIR__ . '/../../auxiliadores/auxiliador.php';
require_once __DIR__ . '/../../infraestrutura/basededados/criar-conexao.php'; 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
# Usa a conexão PDO criada em criar-conexao.php
$pdo = $GLOBALS['pdo'];
##############
### VERBOS ###
##############
# VERBOS POST
## CONTROLA A ROTA PARA CRIAÇÃO E ATUALIZAÇÃO DE UM grupo NA PÁGINA GRUPO
if (isset($_POST['grupo'])) {
    ## CONTROLA A CRIAÇÃO DE NOVOS grupo
    if ($_POST['grupo'] == 'criar') {
        # CRIA UM GRUPO
        criar($pdo, $_POST);
    }
    ## CONTROLA A ATUALIZAÇÃO DE DADOS DOS grupo
    if ($_POST['grupo'] == 'atualizar') {
        # ATUALIZA UM GRUPO
        atualizar($pdo, $_POST);
    }
    ## CONTROLA A ATUALIZAÇÃO DA IMAGEM DE Imagem do grupo 
    if ($_POST['grupo'] == 'imagem_do_grupo') {
        # ATUALIZA A Imagem do GRUPO DE UM GRUPO
        alterarImagemDoGrupo($pdo, $_POST);
    }
}
# VERBOS GET
## CONTROLA A ROTA PARA O CARREGAMENTO DE UM GRUPO NA PÁGINA ATUALIZAR-GRUPO
if (isset($_GET['grupo'])) {
    ## CONTROLA A ROTA PARA A CRIAÇÃO DE NOVOS GRUPO
    if ($_GET['grupo'] == 'atualizar') {
        # RECUPERA DADOS DO grupo PELO ID RECEBIDO
        $grupo = lerGrupoPorId($pdo, $_GET['id']); // Alterei para lergrupoPorId
        # CRIA A SESSÃO AÇÃO ATUALIZAR PARA MANIPULAR O BOTÃO DE ENVIO DO FORMULÁRIO grupo
        $grupo['acao'] = 'atualizar';
        # ENVIA PARÂMETROS COM DADOS DO grupo PARA A PÁGINA grupo RECUPERAR DADOS PARA MANIPULAR A ALTERAÇÃO
        $params = '?' . http_build_query($grupo);
        header('location: /../admin/grupo.php' . $params);
        exit; // Termina o script após redirecionamento
    }
    ## CONTROLA A ROTA PARA A EXCLUSÃO DE GRUPO
    if ($_GET['grupo'] == 'deletar') {
        # RECUPERA DADOS DO grupo
        $grupo = lerGrupoPorId($pdo, $_GET['id']); // Alterei para lergrupoPorId
        # DELETA grupo
        $sucesso = deletarGrupo($pdo, $grupo['id']);
        # REDIRECIONA UTILIZADOR PARA PÁGINA ADMIN COM MENSAGEM DE SUCCESO
        if ($sucesso) {
            # DEFINE MENSAGEM DE SUCESSO
            $_SESSION['sucesso'] = 'Grupo deletado com sucesso!';
            # REDIRECIONA UTILIZADOR COM DADOS DO FORMULÁRIO ANTERIORMENTE PREENCHIDO
            header('location: /../../../aplicacao/tabela-grupo.php');
            exit; // Termina o script após redirecionamento
        }
    }
}
###############
### FUNÇÕES ###
###############
/**
 * FUNÇÃO RESPONSÁVEL POR CRIAR UM NOVO GRUPO
 */

/**
 * FUNÇÃO RESPONSÁVEL POR CRIAR UM NOVO GRUPO
 */

function criar($pdo, $requisicao)
{
    # =======================================
    # VALIDA DADOS DO GRUPO
    # =======================================
    # FICHEIRO VALIDAÇÃO->APLICAÇAO->aplicacao->VALIDAR-GRUPO.PHP
    $dados = grupoValido($requisicao);
    # =======================================
    # VERIFICA SE EXISTEM ERROS DE VALIDAÇÃO
    # =======================================
    if (isset($dados['invalido'])) {
        # RECUPERA MENSAGEM DE ERRO, CASO EXISTA, E COLOCA EM SESSÃO PARA RECUPERAÇÃO NO FORMULÁRIO grupo
        $_SESSION['erros'] = $dados['invalido'];
        # RECUPERA DADOS DO FORMULÁRIO PARA RECUPERAR PREENCHIMENTO ANTERIOR
        $params = '?' . http_build_query($requisicao);
        # REDIRECIONA UTILIZADOR COM DADOS DO FORMULÁRIO ANTERIORMENTE PREENCHIDO
        header('location: /../aplicacao/grupo.php' . $params);
        exit; // Termina o script após redirecionamento
    }
    # =======================================
    # GUARDA IMAGEM DO GRUPO EM DIRETÓRIO LOCAL
    # =======================================
    # FUNÇÃO LOCAL: GUARDA ARQUIVO E RETORNA DADOS ATUALIZADOS
    $dados = guardaImagemDoGrupo($dados);
    # =======================================
    # GUARDA CURSO NA BASE DE DADOS (REPOSITÓRIO PDO)
    # =======================================
    $sucesso = criarGrupo($pdo, $dados);
    # =======================================
    # REDIRECIONA UTILIZADOR PARA A PÁGINA DE REGISTO COM MENSAGEM DE SUCESSO
    # =======================================
    if ($sucesso) {
        # DEFINE MENSAGEM DE SUCESSO
        $_SESSION['sucesso'] = 'Grupo criado com sucesso!';
        # REDIRECIONA O UTILIZADOR PARA A PÁGINA ADMIN
        header('location: /../../../aplicacao/tabela-grupo.php');
        exit; // Termina o script após redirecionamento
    }
}

function criarGrupo($pdo, $dados)
{
    try {
        // Verificar se há campos obrigatórios ausentes ou vazios
        $camposObrigatorios = [
            'grupo',
            'genero',
            'tamanho',
            'descricao'
        ];
        foreach ($camposObrigatorios as $campo) {
            if (empty($dados[$campo])) {
                throw new Exception("Campo obrigatório ausente ou vazio: $campo. Verifique os dados enviados.");
            }
        }
        // SQL de inserção
        $sql = 'INSERT INTO grupos (nome, genero, tamanho, descricao, imagem_do_grupo)
                VALUES (:nome, :genero, :tamanho, :descricao, :imagem_do_grupo)';
        $stmt = $pdo->prepare($sql);
        // Binding dos parâmetros
        $stmt->bindParam(':nome', $dados['nome']);
        $stmt->bindParam(':genero', $dados['genero']);
        $stmt->bindParam(':tamanho', $dados['tamanho']);
        $stmt->bindParam(':descricao', $dados['descricao']);
        $stmt->bindParam(':imagem_do_grupo', $dados['imagem_do_grupo']);
        // Executa a consulta
        return $stmt->execute();
    } catch (PDOException $e) {
        // Captura erros específicos do PDO
        $errorMessage = "Erro ao executar a query. Dados enviados: " . json_encode($dados) . " | Erro: " . $e->getMessage();
        throw new Exception($errorMessage);  // Relança a exceção com mensagem detalhada
    } catch (Exception $e) {
        // Captura erros gerais
        $errorMessage = "Erro geral: " . $e->getMessage();
        throw new Exception($errorMessage);  // Relança a exceção com a mensagem de erro
    }
}

/**

 * FUNÇÃO RESPONSÁVEL POR ATUALIZAR UM CURSO

 */

function atualizar($pdo, $requisicao)
{
    # VALIDA DADOS DO CURSO
    $dados = grupoValido($requisicao);
    # VERIFICA SE EXISTEM ERROS DE VALIDAÇÃO
    if (isset($dados['invalido'])) {
        # RECUPERA MENSAGEM DE ERRO, CASO EXISTA
        $_SESSION['erros'] = $dados['invalido'];
        # CRIA A SESSÃO AÇÃO ATUALIZAR PARA MANIPULAR O BOTÃO DE ENVIO DO FORMULÁRIO CURSO
        $_SESSION['acao'] = 'atualizar';
        # RECUPERA DADOS DO FORMULÁRIO PARA RECUPERAR PREENCHIMENTO ANTERIOR
        $params = '?' . http_build_query($requisicao);
        # REDIRECIONA UTILIZADOR COM DADOS DO FORMULÁRIO ANTERIORMENTE PREENCHIDO
        header('location: /../aplicacao/grupo.php' . $params);
        exit; // Termina o script após redirecionamento
    }
    # RECUPERA DADOS DO CURSO
    $curso = lerGrupoPorId($pdo, $dados['id']); // Alterei para lerCursoPorId
    # GUARDA Imagem do Curso EM DIRETÓRIO LOCAL E APAGA A Imagem do Curso ANTIGA ORIUNDA DA REQUISIÇÃO (FUNÇÃO LOCAL)
    if (isset($_FILES['imagem_do_grupo']) && !empty($_FILES['imagem_do_grupo']['name'])) {
        $dados = guardaImagemDoGrupo($dados, $requisicao);
    }
    # ATUALIZA CURSO (REPOSITÓRIO PDO)
    $sucesso = atualizarGrupo($pdo, $dados);
    # REDIRECIONA UTILIZADOR PARA PÁGINA DE ALTERAÇÃO COM MENSAGEM DE SUCCESO
    if ($sucesso) {
        # DEFINE MENSAGEM DE SUCESSO
        $_SESSION['sucesso'] = 'Grupo alterado com sucesso!';
        # DEFINE BOTÃO DE ENVIO DO FORMULÁRIO
        $dados['acao'] = 'atualizar';
        # RECUPERA DADOS DO FORMULÁRIO PARA RECUPERAR PREENCHIMENTO ANTERIOR
        $params = '?' . http_build_query($dados);
        # REDIRECIONA UTILIZADOR COM DADOS DO FORMULÁRIO ANTERIORMENTE PREENCHIDO
        header('location: /../admin/grupo.php' . $params);
        exit; // Termina o script após redirecionamento
    }
}

/**
 * FUNÇÃO RESPONSÁVEL POR ATUALIZAR A Imagem do Curso DO CURSO
 */

function alterarimagemdogrupo($pdo, $requisicao)
{
    # VALIDA DADOS DO CURSO (VALIDAÇÃO)
    $dados = imagemdogrupoValida($requisicao);
    # VERIFICA SE EXISTEM ERROS DE VALIDAÇÃO
    if (isset($dados['invalido'])) {
        # RECUPERA MENSAGEM DE ERRO, CASO EXISTA
        $_SESSION['erros'] = $dados['invalido'];
        # RECUPERA DADOS DO FORMULÁRIO PARA RECUPERAR PREENCHIMENTO ANTERIOR
        $params = '?' . http_build_query($requisicao);
        # REDIRECIONA UTILIZADOR COM DADOS DO FORMULÁRIO ANTERIORMENTE PREENCHIDO
        header('location: /../admin/imagem do grupo.php' . $params);
        exit; // Termina o script após redirecionamento
    } else {
        # GUARDA Imagem do Curso EM DIRETÓRIO LOCAL E APAGA A Imagem do Curso ANTIGA ORIUNDA DA REQUISIÇÃO
        if (!empty($_FILES['imagem_do_grupo']['name'])) {
            # GUARDA Imagem do Curso EM DIRETÓRIO LOCAL
            $dados = guardaImagemDoGrupo($dados, $requisicao);
        }
        # ATUALIZA CURSO
        $sucesso = atualizarImagemDoGrupo($pdo, $dados);
        # REDIRECIONA UTILIZADOR PARA PÁGINA DE ALTERAÇÃO COM MENSAGEM DE SUCCESO
        if ($sucesso) {
            # DEFINE MENSAGEM DE SUCESSO
            $_SESSION['sucesso'] = 'imagem do grupo alterada com sucesso!';
            # REDIRECIONA UTILIZADOR COM DADOS DO FORMULÁRIO ANTERIORMENTE PREENCHIDO
            header('location: /../admin/grupo.php?id=' . $dados['id']);
            exit; // Termina o script após redirecionamento
        }
    }
}
/**
 * FUNÇÃO RESPONSÁVEL POR GUARDAR A Imagem do Curso DO CURSO EM DIRETÓRIO LOCAL
 */
/**
 * Função responsável por guardar a imagem do curso no diretório e atualizar os dados
 */

function guardaImagemDoGrupo($dados)
{
    // Verifique se o arquivo foi enviado
    if (isset($_FILES['imagem_do_grupo']) && $_FILES['imagem_do_grupo']['error'] == 0) {
        // Verifique o tipo de arquivo (para garantir que é uma imagem)
        $extensao = pathinfo($_FILES['imagem_do_grupo']['name'], PATHINFO_EXTENSION);
        $extensoesPermitidas = ['jpg', 'jpeg', 'png', 'gif'];
        // Verifica se a extensão do arquivo é permitida
        if (in_array(strtolower($extensao), $extensoesPermitidas)) {
            // Gera um nome único para o arquivo para evitar conflitos
            $nomeImagem = 'grupo_' . time() . '.' . $extensao;
            // Define o caminho para salvar a imagem (certifique-se de que esse diretório existe e tem permissões adequadas)
            $diretorioDestino = __DIR__ . '/../../../Image/ImagemDoGrupo/' . $nomeImagem; // Defina o caminho do diretório de destino
            // Verifica se o diretório de destino existe
            if (!is_dir(__DIR__ . '/../../../Image/ImagemDoGrupo/')) {
                mkdir(__DIR__ . '/../../../Image/ImagemDoGrupo/', 0777, true); // Cria o diretório, se não existir
            }
            // Move o arquivo para o diretório de destino
            if (move_uploaded_file($_FILES['imagem_do_grupo']['tmp_name'], $diretorioDestino)) {
                // Se o arquivo foi movido com sucesso, atualiza o caminho da imagem nos dados do grupo
                $dados['imagem_do_grupo'] = '/../../../Image/ImagemDoGrupo/' . $nomeImagem;
            } 
        } else {
            // Se o formato do arquivo não for permitido, registra o erro
            $_SESSION['erros'][] = 'O formato da imagem não é permitido. Aceitamos jpg, jpeg, png ou gif.';
        }
    }
    // Retorna os dados com o caminho da imagem atualizado (ou vazio se falhou)
    return $dados;
}
/**
 * FUNÇÃO RESPONSÁVEL POR VALIDAR A Imagem do grupo DO grupo
 */

function imagemdogrupoValida($requisicao)
{
    // Implemente a lógica de validação da imagem do grupo aqui
    // Exemplo básico: verificar tipo de arquivo, tamanho, etc.
    $dados = [];
    // Verificação básica para fins de exemplo
    if ($_FILES['imagem_do_grupo']['size'] > 1000000) {
        $dados['invalido']['imagem_do_grupo'] = 'A imagem do grupo deve ter no máximo 1MB.';
    }
    return $dados;
}
/**
 * FUNÇÃO RESPONSÁVEL POR ATUALIZAR A Imagem do grupo DO grupo NO BANCO DE DADOS
 */

function atualizarImagemDoGrupo($pdo, $dados)
{
    // Implemente a lógica de atualização da imagem do grupo no banco de dados aqui
    // Exemplo básico de como seria a atualização:
    $sql = 'UPDATE grupos SET imagem_do_grupo = :imagem_do_grupo WHERE id = :id';
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':imagem_do_grupo', $dados['imagem_do_grupo']);
    $stmt->bindParam(':id', $dados['id']);
    return $stmt->execute();
}
/**
 * FUNÇÃO RESPONSÁVEL POR LER UM grupo PELO SEU ID
 */
// Modificação na função lergrupoPorId e verificação do ID

function lergrupoPorId($pdo, $id)
{
    $sql = 'SELECT * FROM grupos WHERE id = :id';
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
// Código da ação de deletar
if (isset($_GET['grupo']) && $_GET['grupo'] == 'deletar') {
    // Verifica se o ID foi passado na URL
    if (isset($_GET['id']) && !empty($_GET['id'])) {
        $grupoId = $_GET['id'];
        // Lê os dados do grupo com base no ID
        $grupo = lerGrupoPorId($pdo, $grupoId);
        // Verifica se o grupo foi encontrado
        if ($grupo) {
            // Chama a função para deletar o grupo
            $sucesso = deletarGrupo($pdo, $grupo['id']);
            if ($sucesso) {
                // Define mensagem de sucesso
                $_SESSION['sucesso'] = 'Grupo deletado com sucesso!';
                header('location: /../../../aplicacao/tabela-grupo.php');
                exit; // Termina o script após o redirecionamento
            } else {
                // Caso ocorra algum erro ao deletar o grupo
                $_SESSION['erros'] = ['Erro ao tentar deletar o grupo.'];
                header('location: /../../../aplicacao/tabela-grupo.php');
                exit; // Termina o script após o redirecionamento
            }
        } else {
            // Caso o grupo não tenha sido encontrado
            $_SESSION['erros'] = ['Grupo não encontrado.'];
            header('location: /../../../aplicacao/tabela-grupo.php');
            exit; // Termina o script após o redirecionamento
        }
    } else {
        // Caso o ID não tenha sido passado ou esteja vazio
        $_SESSION['erros'] = ['ID do grupo não foi fornecido.'];
        header('location: /../../../aplicacao/tabela-grupo.php');
        exit; // Termina o script após o redirecionamento
    }
}

function grupoValido($requisicao)
{
    $dados = $requisicao; // Aqui você pode aplicar sua lógica de validação
    // Se algum dado for inválido, você pode adicionar o erro
    if (empty($dados['titulo'])) {
        $dados['invalido'][] = 'O título é obrigatório.';
    }
    // Validar campo obrigatório "titulo"
    if (empty($requisicao['titulo'])) {
        $dados['invalido']['titulo'] = 'O título do curso é obrigatório.';
    } else {
        $dados['titulo'] = $requisicao['titulo'];
    }
    // Validar campo obrigatório "instrutor"
    if (empty($requisicao['instrutor'])) {
        $dados['invalido']['instrutor'] = 'O instrutor do curso é obrigatório.';
    } else {
        $dados['instrutor'] = $requisicao['instrutor'];
    }
    // Validar campo obrigatório "categoria"
    if (empty($requisicao['categoria'])) {
        $dados['invalido']['categoria'] = 'A categoria do curso é obrigatória.';
    } else {
        $dados['categoria'] = $requisicao['categoria'];
    }
    // Validar campo obrigatório "codigo_do_curso"
    if (empty($requisicao['codigo_do_curso'])) {
        $dados['invalido']['codigo_do_curso'] = 'O código do curso é obrigatório.';
    } else {
        $dados['codigo_do_curso'] = $requisicao['codigo_do_curso'];
    }
    // Validar campo obrigatório "duracao_em_horas"
    if (empty($requisicao['duracao_em_horas'])) {
        $dados['invalido']['duracao_em_horas'] = 'A duração do curso é obrigatória.';
    } else {
        $dados['duracao_em_horas'] = $requisicao['duracao_em_horas'];
    }
    // Validar campo obrigatório "ano_de_lancamento"
    if (empty($requisicao['ano_de_lancamento'])) {
        $dados['invalido']['ano_de_lancamento'] = 'O ano de lançamento é obrigatório.';
    } else {
        $dados['ano_de_lancamento'] = $requisicao['ano_de_lancamento'];
    }
    // Validar campo obrigatório "descricao"
    if (empty($requisicao['descricao'])) {
        $dados['invalido']['descricao'] = 'A descrição do curso é obrigatória.';
    } else {
        $dados['descricao'] = $requisicao['descricao'];
    }
    // Validar imagem do curso
    if (!isset($requisicao['imagem_do_curso']) || empty($requisicao['imagem_do_curso'])) {
        $dados['imagem_do_curso'] = null;
    } else {
        $dados['imagem_do_curso'] = $requisicao['imagem_do_curso'];
    }
    return $dados;
}
/**
 * FUNÇÃO RESPONSÁVEL POR ATUALIZAR UM CURSO
 */

function atualizarCurso($pdo, $requisicao)
{
    # VALIDA DADOS DO CURSO (VALIDAÇÃO)
    $dados = cursoValido($requisicao);  // função para validar dados do curso (exemplo: cursoValido)
    # VERIFICA SE EXISTEM ERROS DE VALIDAÇÃO
    if (isset($dados['invalido'])) {
        # RECUPERA MENSAGEM DE ERRO, CASO EXISTA
        $_SESSION['erros'] = $dados['invalido'];
        # CRIA A SESSÃO AÇÃO ATUALIZAR PARA MANIPULAR O BOTÃO DE ENVIO DO FORMULÁRIO CURSO
        $_SESSION['acao'] = 'atualizar';
        # RECUPERA DADOS DO FORMULÁRIO PARA RECUPERAR PREENCHIMENTO ANTERIOR
        $params = '?' . http_build_query($requisicao);
        # REDIRECIONA UTILIZADOR COM DADOS DO FORMULÁRIO ANTERIORMENTE PREENCHIDO
        header('location: /admin/curso.php' . $params);
        exit; // Termina o script após redirecionamento
    } else {
        # VERIFICA SE O CURSO EXISTE ANTES DE ATUALIZAR (GARANTE QUE O CURSO REALMENTE EXISTE)
        $cursoExistente = lerCursoPorId($pdo, $dados['id']);
        if (!$cursoExistente) {
            $_SESSION['erros'] = ['Curso não encontrado.'];
            header('location: /admin/curso.php');
            exit; // Termina o script caso o curso não exista
        }
        # VERIFICA SE A IMAGEM DO CURSO FOI ENVIADA
        if (!empty($_FILES['imagem_do_curso']['name'])) {
            # GUARDA A IMAGEM EM DIRETÓRIO LOCAL E APAGA A IMAGEM ANTERIOR
            $dados = guardaImagemDoCurso($dados, $requisicao);  // Função que processa a imagem
        }
        # ATUALIZA O CURSO NO BANCO DE DADOS
        $sucesso = atualizarCursoNoBanco($pdo, $dados);  // Função que atualiza o curso no banco de dados
        # SE A ATUALIZAÇÃO FOI REALIZADA COM SUCESSO
        if ($sucesso) {
            # DEFINE MENSAGEM DE SUCESSO
            $_SESSION['sucesso'] = 'Curso alterado com sucesso!';
            # DEFINE O BOTÃO DE ENVIO DO FORMULÁRIO (caso precise manipular na interface)
            $_SESSION['acao'] = 'atualizar';
            # RECUPERA DADOS DO FORMULÁRIO PARA MANTER PREENCHIMENTO ANTERIOR
            $params = '?' . http_build_query($dados);
            # REDIRECIONA O UTILIZADOR PARA A PÁGINA DE CURSO COM MENSAGEM DE SUCESSO
            header('location: /admin/curso.php' . $params);
            exit; // Termina o script após o redirecionamento
        } else {
            // Caso não tenha sido possível atualizar, retorna mensagem de erro
            $_SESSION['erros'] = ['Ocorreu um erro ao tentar atualizar o curso.'];
            header('location: /admin/curso.php');
            exit;
        }
    }
}
/**
 * Função responsável por atualizar os dados do curso no banco de dados
 */

function atualizarCursoNoBanco($pdo, $dados)
{
    try {
        // SQL de atualização
        $sql = 'UPDATE cursos SET 
                titulo = :titulo, 
                instrutor = :instrutor, 
                categoria = :categoria, 
                codigo_do_curso = :codigo_do_curso, 
                duracao_em_horas = :duracao_em_horas, 
                ano_de_lancamento = :ano_de_lancamento, 
                descricao = :descricao, 
                imagem_do_curso = :imagem_do_curso
                WHERE id = :id';
        // Prepara a consulta SQL
        $stmt = $pdo->prepare($sql);
        // Vincula os parâmetros da consulta SQL
        $stmt->bindParam(':titulo', $dados['titulo']);
        $stmt->bindParam(':instrutor', $dados['instrutor']);
        $stmt->bindParam(':categoria', $dados['categoria']);
        $stmt->bindParam(':codigo_do_curso', $dados['codigo_do_curso']);
        $stmt->bindParam(':duracao_em_horas', $dados['duracao_em_horas']);
        $stmt->bindParam(':ano_de_lancamento', $dados['ano_de_lancamento']);
        $stmt->bindParam(':imagem_do_curso', $dados['imagem_do_curso']);
        $stmt->bindParam(':descricao', $dados['descricao']);
        $stmt->bindParam(':id', $dados['id']); // Vincula o ID para atualizar o curso correto
        // Executa a consulta
        return $stmt->execute(); // Retorna true em caso de sucesso
    } catch (PDOException $e) {
        // Grava erro no log em caso de falha
        $_SESSION['erros'] = ['Erro ao atualizar o curso: ' . $e->getMessage()];
        return false;
    }
}
function deletarCurso($pdo, $id)
{
    $sql = "DELETE FROM cursos WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([':id' => $id]);
}
?>