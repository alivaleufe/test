<?php

namespace App\adms\Controllers\dashboard;

use App\adms\Views\Services\LoadViewService;

/**
 * Classe Dashboard
 *
 * Esta classe é responsável por controlar a exibição da página de dashboard. 
 * Ela define os dados necessários e carrega a view correspondente.
 *
 * @package App\adms\Controllers\dashboard
 * @author Cesar <cesar@celke.com.br>
 */
class Dashboard
{
    /** 
     * @var array|string|null $data Recebe os dados que devem ser enviados para VIEW.
     */
    private array|string|null $data = null;

    /**
     * Método index
     *
     * Este método é responsável por configurar os dados necessários e carregar a view do dashboard.
     * Ele define o título da página e utiliza o serviço de carregamento de view para renderizar o conteúdo.
     * 
     * @return void
     */
    public function index(): void
    {
        // Definir o título da página
        $this->data['title_head'] = "Dashboard";

        // Ativar o item de menu
        $this->data['menu'] = "dashboard";
        
        // Carregar a VIEW
        $loadView = new LoadViewService("adms/Views/dashboard/dashboard", $this->data);
        $loadView->loadView();
    }
}