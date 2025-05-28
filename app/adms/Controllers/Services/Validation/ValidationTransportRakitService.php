<?php

namespace App\adms\Controllers\Services\Validation;

use Rakit\Validation\Validator;
use App\adms\Controllers\Services\Validation\UniqueRule; //

class ValidationTransportRakitService
{
    public function validate(array $data): array
    {
        error_log("DEBUG: ValidationTransportRakitService::validate() CALLED. Data: " . print_r($data, true)); // Log de entrada

        $errors = [];
        $validator = new Validator();
        $validator->addValidator('unique', new UniqueRule());

        $rules = [
            // Para cadastro, o 'except_value' pode ser NULL ou uma string que sua UniqueRule e Repository tratem como "sem exceção"
            'placa'          => 'required|max:10|unique:adms_transports,placa,NULL,id',
            'modelo'         => 'required|max:100',
            'marca'          => 'required|max:50',
            'tipo'           => 'required|max:50',
            'capacidade'     => 'required|integer|min:1',
            'ano_fabricacao' => 'required|integer|digits:4|min:1900|max:' . (date('Y') + 1),
            'nome_motorista' => 'max:255',
            'observacoes'    => '',
        ];

        if (isset($data['id']) && !empty($data['id'])) {
            $rules['id'] = 'required|integer';
            // Para edição, passamos o ID do registro atual e o nome da coluna de ID
            $rules['placa'] = 'required|max:10|unique:adms_transports,placa,' . $data['id'] . ',id';
        }

        error_log("DEBUG: Validation Rules: " . print_r($rules, true)); // Log das regras

        $messages = [
            'id:required'                   => 'Dados inválidos para edição.',
            'id:integer'                    => 'ID inválido para edição.',
            'placa:required'                => 'O campo placa é obrigatório.',
            'placa:max'                     => 'A placa deve ter no máximo 10 caracteres.',
            'placa:unique'                  => 'Esta placa já está cadastrada.',
            'modelo:required'               => 'O campo modelo é obrigatório.',
            'modelo:max'                    => 'O modelo deve ter no máximo 100 caracteres.',
            'marca:required'                => 'O campo marca é obrigatório.',
            'marca:max'                     => 'A marca deve ter no máximo 50 caracteres.',
            'tipo:required'                 => 'O campo tipo é obrigatório.',
            'tipo:max'                      => 'O tipo deve ter no máximo 50 caracteres.',
            'capacidade:required'           => 'O campo capacidade é obrigatório.',
            'capacidade:integer'            => 'A capacidade deve ser um número inteiro.',
            'capacidade:min'                => 'A capacidade deve ser de no mínimo 1.',
            'ano_fabricacao:required'       => 'O campo ano de fabricação é obrigatório.',
            'ano_fabricacao:integer'        => 'O ano de fabricação deve ser um número.',
            'ano_fabricacao:digits'         => 'O ano de fabricação deve ter 4 dígitos.',
            'ano_fabricacao:min'            => 'Ano de fabricação inválido (mínimo 1900).',
            'ano_fabricacao:max'            => 'Ano de fabricação não pode ser futuro.',
            'nome_motorista:max'            => 'O nome do motorista deve ter no máximo 255 caracteres.',
        ];

        $validation = $validator->make($data, $rules);
        $validation->setMessages($messages);
        $validation->validate();

        if ($validation->fails()) {
            error_log("DEBUG: Validation FAILED."); // Log se a validação falhar
            $arrayErrors = $validation->errors();
            foreach ($arrayErrors->firstOfAll() as $key => $message) {
                $errors[$key] = $message;
            }
        } else {
            error_log("DEBUG: Validation PASSED."); // Log se a validação passar
        }
        
        error_log("DEBUG: ValidationTransportRakitService::validate() RETURNED. Errors: " . print_r($errors, true)); // Log de saída
        return $errors;
    }
}