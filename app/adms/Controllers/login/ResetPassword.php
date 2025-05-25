<?php

namespace App\adms\Controllers\login;

use App\adms\Controllers\Services\Validation\ValidationUserPasswordService;
use App\adms\Helpers\CSRFHelper;
use App\adms\Helpers\GenerateLog;
use App\adms\Models\Repository\ResetPasswordRepository;
use App\adms\Views\Services\LoadViewService;

/**
 * Controller para redefinir a senha do usuário.
 *
 * Esta classe lida com o processo de redefinição de senha para usuários que 
 * esqueceram a senha. Ela verifica a validade do código de recuperação, valida 
 * a nova senha e atualiza as informações do usuário no banco de dados.
 *
 * @package App\adms\Controllers\login
 * @author Cesar <cesar@celke.com.br>
 */
class ResetPassword
{
    /** 
     * @var array|string|null $data Dados que serão enviados para a VIEW 
     */
    private array|string|null $data = null;

    /**
     * Método principal que controla o fluxo de redefinição de senha.
     *
     * Este método recebe os dados do formulário, verifica a validade do token CSRF, 
     * e decide se a senha será redefinida ou se a página de redefinição será recarregada.
     *
     * @param string|null $recoverPassword Código de recuperação de senha.
     * @return void
     */
    public function index(string|null $recoverPassword): void
    {
        // Receber os dados do formulário
        $this->data['form'] = filter_input_array(INPUT_POST, FILTER_DEFAULT);

        // Receber o código recuperar a senha
        $this->data['form']['recover_password'] = (string) $recoverPassword;

        // Verificar se o token CSRF é válido
        if (isset($this->data['form']['csrf_token']) && CSRFHelper::validateCSRFToken('form_reset_password', $this->data['form']['csrf_token'])) {
            // Chamar o método esqueceu a senha
            $this->resetPassword();
        } else {
            // Chamar o método para carregar a view atualizar a senha
            $this->viewResetPassword();
        }
    }

    /**
     * Carrega a visualização para redefinição de senha.
     *
     * Este método define o título da página e carrega a visualização que 
     * permite ao usuário inserir uma nova senha.
     *
     * @return void
     */
    private function viewResetPassword(): void
    {
        // Definir o título da página
        $this->data['title_head'] = "Nova Senha";

        // Carregar a VIEW
        $loadView = new LoadViewService("adms/Views/login/resetPassword", $this->data);
        $loadView->loadViewLogin();
    }

    /**
     * Processa a redefinição de senha do usuário.
     *
     * Este método valida a nova senha, verifica a validade do código de recuperação 
     * e, se tudo estiver correto, atualiza a senha do usuário no banco de dados.
     *
     * @return void
     */
    private function resetPassword(): void
    {
        // Instanciar a classe de validação dos dados do formulário
        $validationUser = new ValidationUserPasswordService();
        $this->data['errors'] = $validationUser->validate($this->data['form']);

        // Se houver erros, recarregar a view com erros
        if (!empty($this->data['errors'])) {
            $this->viewResetPassword();
            return;
        }

        // Instanciar o Repository para recuperar o registro do banco de dados
        $viewUser = new ResetPasswordRepository();
        $this->data['user'] = $viewUser->getUser((string) $this->data['form']['email']);

        // Verificar se encontrou o registro no banco de dados
        if (!$this->data['user']) {

            // Chamar o método para salvar o log
            GenerateLog::generateLog("error", "Usuário não encontrado.", ['email' => (string) $this->data['form']['email']]);

            // Criar a mensagem de erro
            $_SESSION['error'] = "Usuário não encontrado!";

            $this->viewResetPassword();
            return;
        }

        // Verificar se o código recuperar a senha é válido
        if (($this->data['form']['recover_password'] ?? false) and ($this->data['user']['recover_password'] ?? false) and (!password_verify($this->data['form']['recover_password'], $this->data['user']['recover_password']))) {

            // Chamar o método para salvar o log
            GenerateLog::generateLog("error", "Código recuperar senha inválido.", ['email' => (string) $this->data['form']['email']]);

            // Criar a mensagem de erro
            $_SESSION['error'] = "Código recuperar senha inválido!";

            $this->viewResetPassword();
            return;
        }

        // Verificar se a data de validade da chave recuperar senha é menor que a data atual
        if ($this->data['user']['validate_recover_password'] < date('Y-m-d H:i:s')) {

            // Chamar o método para salvar o log
            GenerateLog::generateLog("error", "Código recuperar senha inválido.", ['email' => (string) $this->data['form']['email']]);

            // Criar a mensagem de erro
            $_SESSION['error'] = "Código recuperar senha inválido!";

            $this->viewResetPassword();
            return;
        }

        // Atualizar o usuário
        $userUpdate = new ResetPasswordRepository();
        $result = $userUpdate->updatePassword($this->data['form']);

        // Acessa o IF se o repository retornou TRUE
        if ($result) {

            // Criar a mensagem de sucesso
            $_SESSION['success'] = "Senha editada com sucesso!";

            // Redirecionar o usuário para página de login
            header("Location: {$_ENV['URL_ADM']}login");
            return;
        } else {

            // Criar a mensagem de error
            $this->data['errors'][] = "Senha não editada!";

            // Chamar o método carregar a view
            $this->viewResetPassword();
        }
    }
}
