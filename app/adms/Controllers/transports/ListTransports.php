<?php

namespace App\adms\Controllers\transports;

use App\adms\Controllers\Services\PaginationService; 
use App\adms\Models\Repository\TransportsRepository; 
use App\adms\Views\Services\LoadViewService; 

/**
 * Controller para listar transportes
 */
class ListTransports
{
    /** @var array|string|null $data Dados que devem ser enviados para a VIEW */
    private array|string|null $data = null;

    /** @var int $limitResult Limite de registros por página */
    private int $limitResult = 10; // Ajuste conforme necessário

    /**
     * Recuperar e listar transportes com paginação.
     */
    public function index(string|int $page = 1): void
    {
        // Instanciar o Repository para recuperar os registros do banco de dados.
        $listTransportsRepo = new TransportsRepository();

        // Recuperar os transportes para a página atual.
        // Ajuste o nome do método no repositório se necessário (ex: getAllTransports)
        $this->data['transports'] = $listTransportsRepo->getAllTransports((int) $page, (int) $this->limitResult);

        // Gerar dados de paginação.
        // Ajuste o nome do método no repositório se necessário (ex: getAmountTransports)
        $this->data['pagination'] = PaginationService::generatePagination(
            (int) $listTransportsRepo->getAmountTransports(),
            (int) $this->limitResult,
            (int) $page,
            'list-transports' // URL base para os links da paginação
        );

        // Definir o título da página.
        $this->data['title_head'] = "Listar Transportes";

        // Ativar o item de menu (você precisará definir um novo item de menu para transportes).
        $this->data['menu'] = "list-transports"; // Exemplo de nome para o item de menu

        // Carregar a VIEW com os dados.
        // O caminho da view será algo como "adms/Views/transports/list"
        $loadView = new LoadViewService("adms/Views/transports/list", $this->data);
        $loadView->loadView();
    }
}