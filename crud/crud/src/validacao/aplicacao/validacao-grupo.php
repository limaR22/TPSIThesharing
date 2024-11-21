<?php

// Verifica se a função 'grupoValido' já foi definida antes de declará-la
if (!function_exists('grupoValido')) {
    function grupoValido($requisicao)
    {
        $erros = [];

        // Remover espaços extras de cada valor de $
        foreach ($requisicao as $key => $value) {
            $requisicao[$key] = trim($value);
        }
        if (empty($dados['nome'])) {
            $dados['invalido']['nome'] = 'O campo nome é obrigatório.';
        }
        
        // Validando o campo genero
        if (empty($dados['genero'])) {
            $dados['invalido']['genero'] = 'O campo genero é obrigatório.';
        }

        // Validando o campo tamanho
        if (empty($dados['tamanho'])) {
            $dados['invalido']['tamanho'] = 'O campo tamanho é obrigatório.';
        }
        // Validando o campo Descrição
        if (empty($requisicao['descricao']) || strlen($requisicao['descricao']) < 5) {
            $erros['descricao'] = 'O campo Descrição não pode estar vazio e deve ter no mínimo 5 caracteres.';
        }

        // Retornando os erros ou os dados validados
        if (!empty($erros)) {
            return ['invalido' => $erros];
        }

        return $requisicao;
    }
}
?>
