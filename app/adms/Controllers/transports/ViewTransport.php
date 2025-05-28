<?php

namespace App\adms\Controllers\transports;

use App\adms\Models\Repository\TransportsRepository;
use App\adms\Views\Services\LoadViewService; //

/**
 * Controller para visualizar os detalhes de um transporte.
 */
class ViewTransport
{
    /** @var array|string|null $data Recebe os dados que devem ser enviados para VIEW */
    private array|string|null $data = null;

    /** @var string|int|null $id Recebe o ID do transporte */
    private string|int|null $id;

    public function index(string|int|null $id = null): void
    {
        if (empty($id)) {
            $_SESSION['error'] = "Erro: Transporte não encontrado!";
            header("Location: {$_ENV['URL_ADM']}list-transports");
            return;
        }

        $this->id = (int) $id;

        $transportRepo = new TransportsRepository();
        // Método no repositório para buscar um único transporte pelo ID
        $this->data['transport'] = $transportRepo->getTransport($this->id);

        if ($this->data['transport']) {
            $this->data['title_head'] = "Detalhes do Transporte";
            // Mantém o menu da lista ativo ou pode criar um específico
            $this->data['menu'] = "list-transports";

            $loadView = new LoadViewService("adms/Views/transports/view", $this->data);
            $loadView->loadView();
        } else {
            $_SESSION['error'] = "Erro: Transporte não encontrado!";
            header("Location: {$_ENV['URL_ADM']}list-transports");
        }
    }
}