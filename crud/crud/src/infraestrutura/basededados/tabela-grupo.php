<?php
# TRATA-SE DE UMA FORMA RÁPIDA PARA REINICIAR O BANCO DE DADOS EM AMBIENTE DE DESENVOLVIMENTO
# ESTE FICHEIRO NÃO DEVE ESTAR DISPONÍVEL EM PRODUÇÃO

# INSERE DADOS DA CONEXÃO COM O PDO UTILIZANDO SQLITE

require __DIR__ . '/criar-conexao.php';

# APAGA TABELA SE ELA EXISTIR
$pdo->exec('DROP TABLE IF EXISTS cursos;');
echo 'Tabela grupos apagada!' . PHP_EOL;

# CRIA A TABELA CURSOS COM NOMES DE COLUNAS VÁLIDOS
$pdo->exec(
    'CREATE TABLE grupos (
        id INTEGER PRIMARY KEY AUTOINCREMENT, 
        nome TEXT NOT NULL, 
        genero TEXT NOT NULL, 
        tamanho TEXT NOT NULL,  
        descricao TEXT NOT NULL
    );'
);
echo 'Tabela grupos criada!' . PHP_EOL;

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