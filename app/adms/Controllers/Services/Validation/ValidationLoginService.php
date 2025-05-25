<?php

namespace App\adms\Controllers\Services\Validation;

use Rakit\Validation\Validator;

/**
 * Classe ValidationLoginService
 * 
 * Esta classe é responsável por validar os campos de senha e confirmação de senha em um formulário de usuário.
 * Ela garante que a senha atenda a critérios específicos de segurança e que a confirmação da senha coincida com a senha fornecida.
 * 
 * @package App\adms\Controllers\Services\Validation
 * @author Cesar <cesar@celke.com.br>
 */
class ValidationLoginService
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
            'username' => 'required',
            'password' => 'required',
        ]);

        // Definir mensagens personalizadas
        $validation->setMessages([
            'username:required' => 'O campo usuário é obrigatório.',
            'password:required' => 'O campo senha é obrigatório.',
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
