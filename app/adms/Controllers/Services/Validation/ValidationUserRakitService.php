<?php

namespace App\adms\Controllers\Services\Validation;

use Rakit\Validation\Validator;

class ValidationUserRakitService
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

        // Instanciar a classe validar formulário
        $validator = new Validator();

        $validator->addValidator('uniqueInColumns', new UniqueInColumnsRule());

        // Definir as regras de validação
        $rules = [
            'name'                  => 'required',
            'email'                 => 'required|email',
        ];

        // Se estiver ausente o ID, então é uma criação (cadastrar)
        if(!isset($data['id'])){
            $rules['email'] = 'required|email|uniqueInColumns:adms_users,email;username';
            $rules['password'] = 'required|min:6|regex:/[A-Z]/|regex:/[^\w\s]/';
            $rules['confirm_password'] = 'required|same:password';
        } else{
            // Para edição, adicionar validação de id e ignorar o próprio usuário na verificação de email
            $rules['id'] = 'required|integer';
            $rules['username'] = 'required|min:8|regex:/^\S*$/|uniqueInColumns:adms_users,email;username,' . $data['id'];
            $rules['email'] = 'required|email|uniqueInColumns:adms_users,email;username,' . $data['id'];
        }

        $messages = [
            'id:required'                   => 'Dados inválidos.',
            'id:integer'                    => 'Dados inválidos.',
            'name:required'                 => 'O campo nome é obrigatório.',            
            'email:required'                => 'O campo e-mail é obrigatório.',
            'email:email'                   => 'O campo e-mail deve ser um e-mail válido.',
            'email:uniqueInColumns'         => 'Já existe um usuário cadastrado com este e-mail.',

            'username:required'             => 'O campo usuário é obrigatório.',
            'username:min'                  => 'O usuário deve ter no mínimo 8 caracteres.',
            'username:regex'                => 'O nome de usuário não pode conter espaços em branco.',
            'username:uniqueInColumns'      => 'Já existe um registro com esse usuário de acesso.',

            'password:required'             => 'O campo senha é obrigatório.',
            'password:min'                  => 'A senha deve ter no mínimo 6 caracteres.',
            'password:regex'                => 'A senha deve ter pelo menos uma letra maiúscula e um caractere especial.',
            'confirm_password:required'     => 'Necessário confirmar a senha.',
            'confirm_password:same'         => 'A confirmação da senha deve ser igual à senha.',

        ];

        // Criar o validador com os dados e regras fornecidos
        $validation = $validator->make($data, $rules);

        // Definir as mensagens de erro personalizadas
        $validation->setMessages($messages);

        // Validar os dados
        $validation->validate();

        // Retornar erros se houver
        if($validation->fails()){

            // Recuperar os erros 
            $arrayErrors = $validation->errors();

            // Percorrer o array de erros
            // firstOfAll - obter a primeira mensagem de erro para cada campo validado.
            foreach($arrayErrors->firstOfAll() as $key => $message){
                $errors[$key] = $message;
            }
        }
        
        return $errors;
    }
}