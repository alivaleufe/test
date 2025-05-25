<?php

namespace App\adms\Controllers\login;

use App\adms\Controllers\Services\GenerateKeyService;
use App\adms\Controllers\Services\RecoverPassword;
use App\adms\Controllers\Services\Validation\ValidationEmailService;
use App\adms\Helpers\CSRFHelper;
use App\adms\Helpers\GenerateLog;
use App\adms\Helpers\SendEmailService;
use App\adms\Models\Repository\LoginRepository;
use App\adms\Models\Repository\ResetPasswordRepository;
use App\adms\Views\Services\LoadViewService;

class ForgotPassword
{

    /** @var array|string|null $dados Recebe os dados que devem ser enviados para VIEW */
    private array|string|null $data = null;    

    public function index():  void
    {

        //$sendEmail = new SendEmailService();
        //$sendEmail->sendEmail('teste@teste.com.br', 'Atendimento', 'Recuperar senha', 'Conteúdo com HTML', 'Conteúdo sem HTML');
        
        // Receber os dados do formulário.
        $this->data['form'] = filter_input_array(INPUT_POST, FILTER_DEFAULT);

        // Acessar o IF se existir o CSRF e for valido o CSRF.
        if (isset($this->data['form']['csrf_token']) and CSRFHelper::validateCSRFToken('form_forgot_password', $this->data['form']['csrf_token'])) {

            // Chamar o método esqueceu a senha.
            $this->forgotPassword();
        } else {
            // Chamar o método carregar a view.
            $this->viewForgotPassword();
        }
    }        

    private function viewForgotPassword(): void
    {

        // Criar o título da página.
        $this->data['title_head'] = "Recuperar Senha";

        // Carregar a VIEW.
        $loadView = new LoadViewService("adms/Views/login/forgotPassword", $this->data);
        $loadView->loadViewLogin();
    }

    private function forgotPassword(): void
    {

        // Instanciar a classe validar os dados do formulário.
        $validationUser = new ValidationEmailService();
        $this->data['errors'] = $validationUser->validate($this->data['form']);

        // Acessa o IF quando existir campo com dados incorretos.
        if (!empty($this->data['errors'])) {

            // Chamar o método carregar a view.
            $this->viewForgotPassword();
            return;
        }
        
        // Instanciar o Repository para recuperar o regitro do banco de dados.
        $viewUser = new ResetPasswordRepository();
        $this->data['user'] = $viewUser->getUser((string) $this->data['form']['email']);

        // Verificar se encontrou o registro no banco de dados.
        if (!$this->data['user']) {

            // Chamar o método para salvar o log.
            GenerateLog::generateLog("error", "Usuário não encontrado", ['email' => (string) $this->data['form']['email']]);

            // Criar a mensagem de erro.
            $_SESSION['error'] = "E-mail não encontrado!";

            $this->viewForgotPassword();            
            return;
        }  

        // Instanciar o serviço para recuperar a senha.
        $recoverPassword = new RecoverPassword();
        $resultRecoverPassword = $recoverPassword->recoverPassword($this->data);

        // Verificar se enviou o e-mail com sucesso.
        if(!$resultRecoverPassword){

            // Chamar o método para salvar o log.
            GenerateLog::generateLog("error", "E-mail não enviado!", ['email' => (string) $this->data ['form']['email']]);

            // Criar uma mensagem de erro.
            $_SESSION['error'] = "E-mail não enviado, tente novamente {$_ENV['EMAIL_ADM']}";

            // Chamar o método carregar a view.
            $this->viewForgotPassword();

            return;
        }

        // Criar a mensagem de sucesso
        $_SESSION['success'] = "Enviando e-mail para recuperar a senha";

        // Redirecionar o usuário para a página de login.
        header("Location: {$_ENV['URL_ADM']}login");
    }


    }

