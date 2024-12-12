<?php
# TRATA-SE DE UMA FORMA RÁPIDA PARA REINICIAR O BANCO DE DADOS EM AMBIENTE DE DESENVOLVIMENTO
# ESTE FICHEIRO NÃO DEVE ESTAR DISPONÍVEL EM PRODUÇÃO

# INSERE DADOS DA CONEXÃO COM O PDO UTILIZANDO SQLITE

require_once __DIR__ . '/src/infraestrutura/basededados/criar-conexao.php';


# APAGA TABELA SE ELA EXISTIR
$pdo->exec('DROP TABLE IF EXISTS cursos;');
echo 'Tabela grupos apagada!' . PHP_EOL;

# CRIA A TABELA CURSOS COM NOMES DE COLUNAS VÁLIDOS
try {
    $sql = "
        CREATE TABLE IF NOT EXISTS grupo (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            nome TEXT NOT NULL,
            descricao TEXT,
            foto TEXT,
            criado_por INTEGER,
            criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );
    ";
    $pdo->exec($sql);
    echo "Tabela 'grupo' criada/verificada com sucesso!";
} catch (PDOException $e) {
    echo "Erro ao criar/verificar tabela 'grupo': " . $e->getMessage();
}

# ABAIXO UM ARRAY SIMULANDO OS DADOS DE UM CURSO
$curso = [
    'nome' => 'Nike',
    'genero' => 'Masculino',
    'tamanho' => 'L',
    'descricao' => 'roupas Nike'
];

# INSERE CURSOS
$sqlCreate = "INSERT INTO 
    grupos (
        nome,  
        genero, 
        tamanho, 
        descricao
    ) 
    VALUES (
        :nome, 
        :genero, 
        :tamanho,  
        :descricao
    )";

# PREPARA A QUERY
$PDOStatement = $pdo->prepare($sqlCreate);

# EXECUTA A QUERY RETORNANDO VERDADEIRO SE A CRIAÇÃO FOI FEITA
$sucesso = $PDOStatement->execute([
    ':nome' => $grupo['nome'],
    ':genero' => $grupo['genero'],
    ':tamanho' => $grupo['tamanho'],
    ':descricao' => $grupo['descricao']
]);

if ($sucesso) {
    echo 'grupo padrão criado com sucesso!';
} else {
    echo 'Erro ao criar o grupo!';
}
?>