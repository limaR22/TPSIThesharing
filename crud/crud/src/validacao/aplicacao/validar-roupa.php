<?php

function validarRoupa($dados) {
    $erros = [];

    if (empty($dados['nome']) || strlen(trim($dados['nome'])) < 3) {
        $erros[] = 'O nome da roupa deve ter pelo menos 3 caracteres.';
    }

    if (empty($dados['descricao']) || strlen(trim($dados['descricao'])) < 10) {
        $erros[] = 'A descrição deve ter pelo menos 10 caracteres.';
    }

    if (empty($dados['grupo_id']) || !is_numeric($dados['grupo_id'])) {
        $erros[] = 'Grupo inválido.';
    }

    if (!empty($erros)) {
        return ['invalido' => $erros];
    }

    return [
        'nome' => trim($dados['nome']),
        'descricao' => trim($dados['descricao']),
        'grupo_id' => (int)$dados['grupo_id'],
    ];
}
