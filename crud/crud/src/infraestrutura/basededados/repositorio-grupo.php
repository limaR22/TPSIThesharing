<?php
// INSERE DADOS DA CONEXÃO COM O PDO
require_once __DIR__ . '/criar-conexao.php';

/**
 * Verifica se a função 'criarGrupo' já foi definida antes de declará-la.
 * Isso evita o erro de múltiplas declarações.
 */
if (!function_exists('criarGrupo')) {
    /**
     * FUNÇÃO RESPONSÁVEL POR CRIAR UM NOVO GRUPO
     */
    function criarGrupo($pdo, $dados) {
        // Verificar se o campo 'nome' existe nos dados
        if (empty($dados['nome'])) {
            throw new Exception('Campo obrigatório ausente: nome');
        }
        
        // Verificar outros campos obrigatórios
        $camposObrigatorios = [
            'nome', 
            'genero', 
            'tamanho', 
            'descricao', 
        ];

        foreach ($camposObrigatorios as $campo) {
            if (empty($dados[$campo])) {
                throw new Exception("Campo obrigatório ausente: $campo");
            }
        }

        // Se todos os campos obrigatórios estiverem presentes, prossegue com a inserção
        $sqlCreate = "INSERT INTO cursos (
            nome, 
            genero, 
            tamanho, 
            descricao, 

        ) VALUES (
            :nome, 
            :genero, 
            :tamanho, 
            :descricao, 
        )";

        try {
            $PDOStatement = $pdo->prepare($sqlCreate);
            $sucesso = $PDOStatement->execute([
                ':nome' => $dados['nome'],
                ':genero' => $dados['genero'],
                ':tamanho' => $dados['tamanho'],
                ':descricao' => $dados['descricao']
            ]);

            if ($sucesso) {
                // Recupera o ID do último grupo inserido
                $dados['id'] = $pdo->lastInsertId();
            }

            return $sucesso;
        } catch (PDOException $e) {
            echo "Erro na execução do SQL: " . $e->getMessage();
            return false;
        }
    }
}

/**
 * Verifica se a função 'lerTodosgrupo' já foi definida antes de declará-la.
 */
if (!function_exists('lerTodosgrupo')) {
    /**
     * FUNÇÃO PARA CARREGAR TODOS OS grupos
     */
    function lerTodosCurso($pdo) {
        // Cria uma consulta SQL para recuperar todos os grupos
        $sql = 'SELECT * FROM grupos'; // Altere o nome da tabela conforme necessário
        $stmt = $pdo->prepare($sql);
        $stmt->execute();  // Executa a consulta no banco de dados
        return $stmt->fetchAll(PDO::FETCH_ASSOC);  // Retorna todos os grupos encontrados
    }
}
?>