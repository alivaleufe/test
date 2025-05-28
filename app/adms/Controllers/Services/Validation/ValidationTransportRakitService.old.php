<?php

namespace App\adms\Controllers\Services\Validation;

use Rakit\Validation\Validator;
use App\adms\Controllers\Services\Validation\UniqueRule;

class ValidationTransportRakitService
{
    /**
     * Validar os dados do formulário de transporte.
     *
     * @param array $data Dados do formulário.
     * @return array Lista de erros.
     */
    public function validate(array $data): array
    {
        $errors = [];
        $validator = new Validator();

        // Adicionar a regra de validação única personalizada para a placa
        // O construtor de UniqueRule não precisa de argumentos aqui,
        // pois a tabela e coluna serão passadas na regra.
        $validator->addValidator('unique', new UniqueRule());

        // Definir as regras de validação
        // Baseado na sua tabela adms_transports:
        // placa, modelo, marca, tipo, capacidade, ano_fabricacao, nome_motorista, observacoes
        $rules = [
            'placa'          => 'required|max:10|unique:adms_transports,placa', // Placa única na tabela adms_transports
            'modelo'         => 'required|max:100',
            'marca'          => 'required|max:50',
            'tipo'           => 'required|max:50',
            'capacidade'     => 'required|integer|min:1', // Capacidade deve ser um número inteiro e pelo menos 1
            'ano_fabricacao' => 'required|integer|digits:4|min:1900|max:' . (date('Y') + 1), // Ano com 4 dígitos, um range razoável
            'nome_motorista' => 'max:255', // Opcional, mas com tamanho máximo
            'observacoes'    => '', // Opcional, sem validação específica além do tipo (text)
        ];

        // Se for uma edição (você passaria o ID do transporte em $data['id']),
        // a regra 'unique' para a placa precisa ignorar o registro atual.
        if (isset($data['id'])) {
            $rules['id'] = 'required|integer';
            $rules['placa'] = 'required|max:10|unique:adms_transports,placa,' . $data['id']; // Ignora o ID atual na verificação de unicidade
        }

        // Mensagens de erro personalizadas
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
            $arrayErrors = $validation->errors();
            foreach ($arrayErrors->firstOfAll() as $key => $message) {
                $errors[$key] = $message;
            }
        }

        return $errors;
    }
}