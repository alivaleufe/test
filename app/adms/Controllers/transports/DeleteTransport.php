<?php

namespace App\adms\Controllers\transports;

use App\adms\Helpers\CSRFHelper; //
use App\adms\Models\Repository\TransportsRepository;
use App\adms\Helpers\GenerateLog; //

class DeleteTransport
{
    private string|int|null $id;

    public function index(): void
    {
        $formData = filter_input_array(INPUT_POST, FILTER_DEFAULT);

        if (isset($formData['id']) && !empty($formData['id'])) {
            $this->id = (int) $formData['id'];
            $csrf_token_from_form = $formData['csrf_token'] ?? null;

            // Validar com o form_id padronizado
            if (CSRFHelper::validateCSRFToken('delete_transport_form', $csrf_token_from_form)) {
                $transportRepo = new TransportsRepository();
                if ($transportRepo->deleteTransport($this->id)) {
                    $_SESSION['success'] = "Transporte apagado com sucesso!";
                    GenerateLog::generateLog("info", "Transporte apagado com sucesso.", ['id' => $this->id]);
                } else {
                    $_SESSION['error'] = "Erro: Transporte não apagado! Tente novamente.";
                    GenerateLog::generateLog("error", "Falha ao apagar transporte no controller.", ['id' => $this->id]);
                }
            } else {
                $_SESSION['error'] = "Erro: Requisição inválida (Token CSRF inválido)!";
                GenerateLog::generateLog("warning", "Tentativa de apagar transporte falhou: Token CSRF inválido.", ['id' => $this->id ?? 'N/A', 'token_received' => $csrf_token_from_form]);
            }
        } else {
            $_SESSION['error'] = "Erro: ID do transporte não fornecido ou inválido para exclusão!";
            GenerateLog::generateLog("warning", "Tentativa de apagar transporte falhou: ID não fornecido.", []);
        }

        header("Location: {$_ENV['URL_ADM']}list-transports");
        exit;
    }
}