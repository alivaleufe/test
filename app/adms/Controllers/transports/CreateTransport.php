<?php

namespace App\adms\Controllers\transports;

use App\adms\Controllers\Services\Validation\ValidationTransportRakitService;
use App\adms\Helpers\CSRFHelper;
use App\adms\Models\Repository\TransportsRepository;
use App\adms\Views\Services\LoadViewService;
use App\adms\Helpers\GenerateLog; // Adicionar o use para GenerateLog

class CreateTransport
{
    private array|string|null $data = null;

    public function index(): void
    {
        $this->data['form'] = filter_input_array(INPUT_POST, FILTER_DEFAULT);

        if (isset($this->data['form']['csrf_token']) && CSRFHelper::validateCSRFToken('form_create_transport', $this->data['form']['csrf_token'])) {
            $this->addTransport();
        } else {
            // Verifica se é um POST e o token CSRF falhou ou não veio
            if (!isset($this->data['form']['csrf_token']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
                // Log de tentativa de cadastro com falha de CSRF
                GenerateLog::generateLog("warning", "Tentativa de cadastro de transporte falhou: Token CSRF inválido ou ausente.", []);
                $_SESSION['error'] = "Erro: Requisição inválida! Por favor, tente novamente.";
            }
            $this->viewTransportForm();
        }
    }

    private function viewTransportForm(): void
    {
        $this->data['title_head'] = "Cadastrar Transporte";
        $this->data['menu'] = "list-transports";
        $loadView = new LoadViewService("adms/Views/transports/create", $this->data);
        $loadView->loadView();
    }

    private function addTransport(): void
    {
        $validationTransport = new ValidationTransportRakitService();
        $this->data['errors'] = $validationTransport->validate($this->data['form']);

        if (!empty($this->data['errors'])) {
            GenerateLog::generateLog("info", "Falha na validação ao cadastrar transporte.", ['errors' => $this->data['errors'], 'formData' => $this->data['form']]);
            $this->viewTransportForm();
            return;
        }

        $transportCreate = new TransportsRepository();
        $result = $transportCreate->createTransport($this->data['form']);

        if ($result) {
            $_SESSION['success'] = "Transporte cadastrado com sucesso!";
            GenerateLog::generateLog("info", "Transporte cadastrado com sucesso.", ['id' => $result, 'placa' => $this->data['form']['placa'] ?? 'N/A']);
            header("Location: {$_ENV['URL_ADM']}view-transport/" . $result);
            exit; 
        } else {
            GenerateLog::generateLog("error", "Falha ao cadastrar transporte no controller.", ['formData' => $this->data['form']]);
            $this->data['errors']['form_error_message'] = "Erro: Transporte não cadastrado! Por favor, tente novamente.";
            $this->viewTransportForm();
        }
    }
}