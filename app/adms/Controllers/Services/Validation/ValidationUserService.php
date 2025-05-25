<?php

namespace App\adms\Controllers\Services\Validation;

class ValidationUserService
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

        // Verificar se o campo nome está vazio
        if(empty($data['name'])){
            $errors['name'] = 'O campo nome é obrigatório.';
        }
        
        // Verificar se o campo email está vazio e o valor é do tipo e-mail
        if(empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)){
            $errors['email'] = 'O campo e-mail é obrigatório e deve ser um e-mail válido.';
        }
        
        // Verificar se o campo senha está vazio, se a senha possui mínimo 6 caracteres, ao menos uma letra maiúscula e um caractere especial
        if(empty($data['password']) || strlen($data['password']) < 6 || !preg_match('/[A-Z]/', $data['password']) || !preg_match('/[^\w\s]/', $data['password']) ){
            $errors['password'] = 'A senha deve ter no mínimo 6 caracteres, uma letra maiúscula e um caractere especial.';
        }

        return $errors;

    }
}