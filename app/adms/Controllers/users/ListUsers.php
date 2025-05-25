<?php

namespace App\adms\Controllers\users;

use App\adms\Controllers\Services\PaginationService;
use App\adms\Models\Repository\UsersRepository;
use App\adms\Views\Services\LoadViewService;

/**
 * Controller para listar usuários
 *
 * Esta classe é responsável por recuperar e exibir uma lista de usuários no sistema. Utiliza um repositório
 * para obter dados dos usuários e um serviço de paginação para gerenciar a navegação entre páginas de resultados.
 * Em seguida, carrega a visualização correspondente com os dados recuperados.
 * 
 * @package App\adms\Controllers\users
 * @author Cesar <cesar@celke.com.br>
 */
class ListUsers
{
    /** @var array|string|null $data Dados que devem ser enviados para a VIEW */
    private array|string|null $data = null;

    /** @var int $limitResult Limite de registros por página */
    private int $limitResult = 10; // Ajuste conforme necessário

    /**
     * Recuperar e listar usuários com paginação.
     * 
     * Este método recupera os usuários a partir do repositório de usuários com base na página atual e no limite
     * de registros por página. Gera os dados de paginação e carrega a visualização para exibir a lista de usuários.
     * 
     * @param string|int $page Página atual para a exibição dos resultados. O padrão é 1.
     * 
     * @return void
     */
    public function index(string|int $page = 1): void
    {
        // Instanciar o Repository para recuperar os registros do banco de dados.
        $listUsers = new UsersRepository();

        // Recuperar os usuários para a página atual.
        $this->data['users'] = $listUsers->getAllUsers((int) $page, (int) $this->limitResult);

        // Gerar dados de paginação.
        $this->data['pagination'] = PaginationService::generatePagination(
            (int) $listUsers->getAmountUsers(), 
            (int) $this->limitResult, 
            (int) $page, 
            'list-users'
        );

        // Definir o título da página.
        $this->data['title_head'] = "Listar Estudantes";

        // Ativar o item de menu.
        $this->data['menu'] = "list-users";

        // Carregar a VIEW com os dados.
        $loadView = new LoadViewService("adms/Views/users/list", $this->data);
        $loadView->loadView();
    }
}
