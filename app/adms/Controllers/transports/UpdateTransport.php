<?php

namespace App\adms\Controllers\transports;

use App\adms\Controllers\Services\Validation\ValidationTransportRakitService;
use App\adms\Helpers\CSRFHelper;
use App\adms\Models\Repository\TransportsRepository;
use App\adms\Views\Services\LoadViewService;
use App\adms\Helpers\GenerateLog;

class UpdateTransport
{
    private array|string|null $data = null;
    private string|int|null $id;

    public function index(string|int|null $id = null): void
    {
        if (empty($id)) {
            $_SESSION['error'] = "Erro: Transporte não encontrado!";
            GenerateLog::generateLog("warning", "Tentativa de acesso a UpdateTransport sem ID.", []);
            header("Location: {$_ENV['URL_ADM']}list-transports");
            exit;
        }
        $this->id = (int) $id;
        $this->data['form'] = filter_input_array(INPUT_POST, FILTER_DEFAULT);

        if (isset($this->data['form']['csrf_token']) && CSRFHelper::validateCSRFToken('form_update_transport', $this->data['form']['csrf_token'])) {
            $this->editTransport();
        } else {
            if (!isset($this->data['form']['csrf_token']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
                GenerateLog::generateLog("warning", "Tentativa de atualização de transporte falhou: Token CSRF inválido ou ausente.", ['id' => $this->id]);
                $_SESSION['error'] = "Erro: Requisição inválida! Por favor, tente novamente.";
            }
            $this->viewTransportForm();
        }
    }

    private function viewTransportForm(): void
    {
        $transportRepo = new TransportsRepository();
        $transportData = $transportRepo->getTransport($this->id);

        if ($transportData) {
            if (empty($this->data['form'])) { // Só preenche do banco se não for um POST de volta com erros
                $this->data['form'] = $transportData;
            }
            $this->data['form']['id'] = $this->id; // Garante que o ID está no form para a view e validação
            $this->data['title_head'] = "Editar Transporte";
            $this->data['menu'] = "list-transports";
            $loadView = new LoadViewService("adms/Views/transports/update", $this->data);
            $loadView->loadView();
        } else {
            $_SESSION['error'] = "Erro: Transporte não encontrado para edição!";
            GenerateLog::generateLog("error", "Transporte não encontrado para edição ao carregar formulário.", ['id' => $this->id]);
            header("Location: {$_ENV['URL_ADM']}list-transports");
            exit;
        }
    }

    private function editTransport(): void
    {
        $this->data['form']['id'] = $this->id; // Garante que o ID está nos dados do formulário para validação/update
        $validationTransport = new ValidationTransportRakitService();
        $this->data['errors'] = $validationTransport->validate($this->data['form']);

        if (!empty($this->data['errors'])) {
            GenerateLog::generateLog("info", "Falha na validação ao editar transporte.", ['id' => $this->id, 'errors' => $this->data['errors'], 'formData' => $this->data['form']]);
            $this->viewTransportForm();
            return;
        }

        $transportUpdate = new TransportsRepository();
        // Passa apenas os dados do formulário, pois o ID já está nele e o repositório o usará
        $result = $transportUpdate->updateTransport($this->data['form']);

        if ($result) {
            $_SESSION['success'] = "Transporte atualizado com sucesso!";
            GenerateLog::generateLog("info", "Transporte atualizado com sucesso.", ['id' => $this->id, 'placa' => $this->data['form']['placa'] ?? 'N/A']);
            header("Location: {$_ENV['URL_ADM']}view-transport/" . $this->id);
            exit;
        } else {
            // O erro específico do repositório já deve ter sido logado lá
            GenerateLog::generateLog("error", "Falha ao atualizar transporte no controller (após tentativa no repositório).", ['id' => $this->id, 'formData' => $this->data['form']]);
            $this->data['errors']['form_error_message'] = "Erro: Transporte não atualizado! Por favor, tente novamente.";
            $this->viewTransportForm();
        }
    }
}