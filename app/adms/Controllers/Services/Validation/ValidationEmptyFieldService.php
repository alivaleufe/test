<?php

namespace App\adms\Controllers\Services\Validation;

class ValidationEmptyFieldService
{

    /**
     * Validar os dados do formulário.
     *
     * @param array $data Dados do formulário.
     * @return array Lista de erros.
     */
    public function validate(array $data): array
    {

        // Criar o array para receber as mensagens de erro
        $errors = [];

        // Retirar espaço em branco no valor
        $data = array_map('trim', $data);

        // Verificar se algum elemento do array está vazio indicando que o campo não possui valor
        if(in_array('', $data)){
            $errors['msg'] = 'Necessário preencher todos os campos.';
        }

        return $errors;
    }
}