<?php

namespace App\adms\Controllers\Services\Validation;

use Rakit\Validation\Validator;

/**
 * Classe ValidationUserPasswordService
 * 
 * Esta classe é responsável por validar os campos de senha e confirmação de senha em um formulário de usuário.
 * Ela garante que a senha atenda a critérios específicos de segurança e que a confirmação da senha coincida com a senha fornecida.
 * 
 * @package App\adms\Controllers\Services\Validation
 */
class ValidationUserPasswordService
{
    /**
     * Validar os dados do formulário.
     * 
     * Este método valida os campos de senha e confirmação de senha, garantindo que a senha seja forte o suficiente e que a confirmação coincida com a senha.
     * 
     * @param array $data Dados do formulário.
     * @return array Lista de erros. Se não houver erros, o array será vazio.
     */
    public function validate(array $data): array
    {
        // Criar o array para receber as mensagens de erro
        $errors = [];

        // Instanciar a classe Validator para validar o formulário
        $validator = new Validator();

        // Definir as regras de validação
        $validation = $validator->make($data, [
            'email' => 'required|email',
            'password' => 'required|min:6|regex:/[A-Z]/|regex:/[^\w\s]/',
            'confirm_password' => 'required|same:password',
        ]);

        // Definir mensagens personalizadas
        $validation->setMessages([
            'email:required' => 'O campo e-mail é obrigatório',
            'email:email' => 'O campo e-mail deve ser um e-mail válido.',
            'password:required' => 'O campo senha é obrigatório.',
            'password:min' => 'A senha deve ter no mínimo 6 caracteres.',
            'password:regex' => 'A senha deve ter pelo menos uma letra maiúscula e um caractere especial.',
            'confirm_password:required' => 'Necessário confirmar a senha.',
            'confirm_password:same' => 'A confirmação da senha deve ser igual à senha.',
        ]);

        // Validar os dados
        $validation->validate();

        // Retornar erros se houver
        if ($validation->fails()) {
            // Recuperar os erros 
            $arrayErrors = $validation->errors();

            // Percorrer o array de erros e armazenar a primeira mensagem de erro para cada campo validado
            foreach ($arrayErrors->firstOfAll() as $key => $message) {
                $errors[$key] = $message;
            }
        }
        
        return $errors;
    }
}
