<?php

namespace App\adms\Controllers\users;

use App\adms\Controllers\Services\Validation\ValidationUserPasswordService;
use App\adms\Helpers\CSRFHelper;
use App\adms\Helpers\GenerateLog;
use App\adms\Models\Repository\UsersRepository;
use App\adms\Views\Services\LoadViewService;

/**
 * Controller para editar a senha do usuário
 *
 * Esta classe é responsável por gerenciar a edição da senha de um usuário. Inclui a validação dos dados de entrada,
 * a atualização da senha no repositório de usuários e a renderização da visualização apropriada. Caso haja
 * algum problema, como um usuário não encontrado ou dados inválidos, as mensagens de erro são geradas e registradas.
 *
 * @package App\adms\Controllers\users
 * @author Cesar <cesar@celke.com.br>
 */
class UpdatePasswordUser
{
    /** @var array|string|null $data Dados que devem ser enviados para a VIEW */
    private array|string|null $data = null;

    /**
     * Editar a senha do usuário.
     *
     * Este método gerencia o processo de edição da senha do usuário. Se o CSRF token for válido e os dados do formulário
     * forem corretos, a senha do usuário é atualizada. Caso contrário, a visualização de edição é carregada com
     * as informações necessárias.
     *
     * @param int|string $id ID do usuário cuja senha deve ser editada.
     * 
     * @return void
     */
    public function index(int|string $id): void
    {
        // Receber os dados do formulário
        $this->data['form'] = filter_input_array(INPUT_POST, FILTER_DEFAULT);

        // Validar o CSRF token e a existência do ID do usuário
        if (isset($this->data['form']['csrf_token']) && 
            CSRFHelper::validateCSRFToken('form_update_password_user', $this->data['form']['csrf_token'])) 
        {
            // Editar a senha do usuário
            $this->editPasswordUser();
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

            // Carregar a visualização para editar a senha
            $this->viewUser();
        }
    }

    /**
     * Carregar a visualização para edição da senha do usuário.
     *
     * Este método define o título da página e carrega a visualização de edição de senha com os dados necessários.
     * 
     * @return void
     */
    private function viewUser(): void
    {
        // Definir o título da página
        $this->data['title_head'] = "Editar Senha do Usuário";

        // Ativar o item de menu.
        $this->data['menu'] = "list-users";

        // Carregar a VIEW
        $loadView = new LoadViewService("adms/Views/users/updatePassword", $this->data);
        $loadView->loadView();
    }

    /**
     * Editar a senha do usuário.
     *
     * Este método valida os dados do formulário, atualiza a senha do usuário no repositório e lida com o resultado
     * da operação. Se a atualização for bem-sucedida, o usuário é redirecionado para a página de visualização do usuário.
     * Caso contrário, uma mensagem de erro é exibida e a visualização de edição é recarregada.
     * 
     * @return void
     */
    private function editPasswordUser(): void
    {
        // Validar os dados do formulário
        $validationUser = new ValidationUserPasswordService();
        $this->data['errors'] = $validationUser->validate($this->data['form']);

        // Se houver erros de validação, recarregar a visualização
        if (!empty($this->data['errors'])) {
            $this->viewUser();
            return;
        }

        // Atualizar a senha do usuário
        $userUpdate = new UsersRepository();
        $result = $userUpdate->updatePasswordUser($this->data['form']);

        // Verificar o resultado da atualização
        if ($result) {
            $_SESSION['success'] = "Senha editada com sucesso!";
            header("Location: {$_ENV['URL_ADM']}view-user/{$this->data['form']['id']}");
        } else {
            $this->data['errors'][] = "Senha não editada!";
            $this->viewUser();
        }
    }
}
