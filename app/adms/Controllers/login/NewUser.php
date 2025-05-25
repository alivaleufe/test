<?php

namespace App\adms\Controllers\login;

use App\adms\Controllers\Services\Validation\ValidationUserRakitService;
use App\adms\Helpers\CSRFHelper;
use App\adms\Models\Repository\UsersRepository;
use App\adms\Views\Services\LoadViewService;

class NewUser
{

    /** @var array|string|null $dados Recebe os dados que devem ser enviados para VIEW. */
    private array|string|null $data = null;

    public function index() : void
    {

        // Receber os dados do formulário.
        $this->data['form'] = filter_input_array(INPUT_POST, FILTER_DEFAULT);

        // Acessar o IF se existir o CSRF e for valido o CSRF.
        if (isset($this->data['form']['csrf_token']) and CSRFHelper::validateCSRFToken('form_new_user', $this->data['form']['csrf_token'])) {

            // Chamar o método cadastrar.
            $this->addUser();
        } else {
            // Chamar o método carregar a View.
            $this->viewUser();
        }        
    }

    private function viewUser(): void
    {

        // Criar o título da página.
        $this->data['title_head'] = "Novo Usuário";

        // Carregar a VIEW.
        $loadView = new LoadViewService("adms/Views/login/newUser", $this->data);
        $loadView->loadViewLogin();
    }

    private function addUser(): void
    {

        // Instanciar a classe validar os dados do formulário.
        $validationUser = new ValidationUserRakitService();
        $this->data['errors'] = $validationUser->validate($this->data['form']);

        // Acessa o IF quando existir campo com dados incorretos.
        if (!empty($this->data['errors'])) {

            // Chamar o método carregar a View.
            $this->viewUser();

            return;
        }

        // Instanciar o Repository para criar o usuário.
        $userCreate = new UsersRepository();
        $result = $userCreate->createUser($this->data['form']);

        // Acessa o IF se o repository retornou TRUE.
        if ($result) {

            // Criar a mensagem de sucesso.
            $_SESSION['success'] = "Usuário cadastrado com sucesso!";

            // Redirecionar o usuário para página listar.
            header("Location: {$_ENV['URL_ADM']}login");
            return;
        } else {
            // Mensagem de error.
            $this->data['errors'][] = "Usuário não cadastrado!";

            // Chamar o método carregar a View.
            $this->viewUser();
        }
    }    
}