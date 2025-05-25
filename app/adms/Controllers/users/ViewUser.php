<?php

namespace App\adms\Controllers\users;

use App\adms\Helpers\GenerateLog;
use App\adms\Models\Repository\UsersRepository;
use App\adms\Views\Services\LoadViewService;

/**
 * Controller visualizar usuário
 *
 * @author Cesar <cesar@celke.com.br>
 */
class ViewUser
{

    /** @var array|string|null $dados Recebe os dados que devem ser enviados para VIEW */
    private array|string|null $data = null;

    /**
     * Recuperar os detalhes do usuário
     * 
     * @param int|string $id id do usuário
     * @return void
     */
    public function index(int|string $id): void
    {
        // Acessa o IF se o id for valor do tipo inteiro
        if (!(int) $id) {

            // Chamar o método para salvar o log
            GenerateLog::generateLog("error", "Usuário não encontrado", ['id' => (int) $id]);

            // Criar a mensagem de erro
            $_SESSION['error'] = "Usuário não encontrado!";

            // Redirecionar o usuário para página listar
            header("Location: {$_ENV['URL_ADM']}list-users");
            return;
        }

        // Instanciar o Repository para recuperar o regitro do banco de dados
        $viewUser = new UsersRepository();
        $this->data['user'] = $viewUser->getUser((int) $id);

        // Verificar se encontrou o registro no banco de dados
        if (!$this->data['user']) {

            // Chamar o método para salvar o log
            GenerateLog::generateLog("error", "Usuário não encontrado", ['id' => (int) $id]);

            // Criar a mensagem de erro
            $_SESSION['error'] = "Usuário não encontrado!";

            // Redirecionar o usuário para página listar
            header("Location: {$_ENV['URL_ADM']}list-users");
            return;
        }

        // Chamar o método para salvar o log
        GenerateLog::generateLog("error", "Visualizado o usuário.", ['id' => (int) $id]);

        // Criar o título da página
        $this->data['title_head'] = "Visualizar Usuário";

        // Ativar o item de menu.
        $this->data['menu'] = "list-users";

        // Carregar a VIEW
        $loadView = new LoadViewService("adms/Views/users/view", $this->data);
        $loadView->loadView();

    }
}
