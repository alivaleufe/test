<?php

namespace App\adms\Controllers\users;

use App\adms\Controllers\Services\Validation\ValidationUserRakitService;
use App\adms\Helpers\CSRFHelper;
use App\adms\Helpers\GenerateLog;
use App\adms\Models\Repository\UsersRepository;
use App\adms\Views\Services\LoadViewService;

/**
 * Controller para editar usuário
 *
 * Esta classe é responsável por gerenciar a edição de informações de um usuário existente. Inclui a validação dos dados
 * do formulário, a atualização das informações do usuário no repositório e a renderização da visualização apropriada.
 * Caso haja algum problema, como um usuário não encontrado ou dados inválidos, mensagens de erro são exibidas e registradas.
 *
 * @package App\adms\Controllers\users
 * @author Cesar <cesar@celke.com.br>
 */
class UpdateUser
{
    /** @var array|string|null $data Dados que devem ser enviados para a VIEW */
    private array|string|null $data = null;

    /**
     * Editar o usuário.
     *
     * Este método gerencia o processo de edição de um usuário. Recebe os dados do formulário, valida o CSRF token e
     * a existência do usuário, e chama o método adequado para editar o usuário ou carregar a visualização de edição.
     *
     * @param int|string $id ID do usuário a ser editado.
     * 
     * @return void
     */
    public function index(int|string $id): void
    {
        // Receber os dados do formulário
        $this->data['form'] = filter_input_array(INPUT_POST, FILTER_DEFAULT);

        // Validar o CSRF token e a existência do ID do usuário
        if (isset($this->data['form']['csrf_token']) && 
            CSRFHelper::validateCSRFToken('form_update_user', $this->data['form']['csrf_token'])) 
        {
            // Editar o usuário
            $this->editUser();
        } else {
            // Recuperar o registro do usuário
            $viewUser = new UsersRepository();
            $this->data['form'] = $viewUser->getUser((int) $id);

            // Verificar se o usuário foi encontrado
            if (!$this->data['form']) {
                // Registrar o erro e redirecionar
                GenerateLog::generateLog("error", "Usuário não encontrado", ['id' => (int) $id]);
                $_SESSION['error'] = "Usuário não encontrado!";
                header("Location: {$_ENV['URL_ADM']}list-users");
                return;
            }

            // Carregar a visualização para edição do usuário
            $this->viewUser();
        }
    }

    /**
     * Carregar a visualização para edição do usuário.
     *
     * Este método define o título da página e carrega a visualização de edição do usuário com os dados necessários.
     * 
     * @return void
     */
    private function viewUser(): void
    {
        // Definir o título da página
        $this->data['title_head'] = "Editar Usuário";

        // Ativar o item de menu.
        $this->data['menu'] = "list-users";

        // Carregar a VIEW
        $loadView = new LoadViewService("adms/Views/users/update", $this->data);
        $loadView->loadView();
    }

    /**
     * Editar o usuário.
     *
     * Este método valida os dados do formulário, atualiza as informações do usuário no repositório e lida com o resultado
     * da operação. Se a atualização for bem-sucedida, o usuário é redirecionado para a página de visualização do usuário.
     * Caso contrário, uma mensagem de erro é exibida e a visualização de edição é recarregada.
     * 
     * @return void
     */
    private function editUser(): void
    {
        // Validar os dados do formulário
        $validationUser = new ValidationUserRakitService();
        $this->data['errors'] = $validationUser->validate($this->data['form']);

        // Se houver erros de validação, recarregar a visualização
        if (!empty($this->data['errors'])) {
            $this->viewUser();
            return;
        }

        // Atualizar o usuário
        $userUpdate = new UsersRepository();
        $result = $userUpdate->updateUser($this->data['form']);

        // Verificar o resultado da atualização
        if ($result) {
            $_SESSION['success'] = "Usuário editado com sucesso!";
            header("Location: {$_ENV['URL_ADM']}view-user/{$this->data['form']['id']}");
        } else {
            $this->data['errors'][] = "Usuário não editado!";
            $this->viewUser();
        }
    }
}
