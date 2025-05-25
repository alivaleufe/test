<?php

namespace App\adms\Controllers\login;

use App\adms\Controllers\Services\Validation\ValidationLoginService;
use App\adms\Controllers\Services\ValidationUserLogin;
use App\adms\Helpers\CSRFHelper;
use App\adms\Views\Services\LoadViewService;

/**
 * Controller login
 *
 */
class Login
{    

    /** @var array|string|null $dados Recebe os dados que devem ser enviados para VIEW. */
    private array|string|null $data = null;

    /**
     * Página login
     * 
     * @return void
     */
    public function index(): void
    {
        
        // Receber os dados do formulário
        $this->data['form'] = filter_input_array(INPUT_POST, FILTER_DEFAULT);

        // Acessar o IF se existir o CSRF e for valido o CSRF
        if (isset($this->data['form']['csrf_token']) and CSRFHelper::validateCSRFToken('form_login', $this->data['form']['csrf_token'])) {

            // Chamar o método login.
            $this->login();
        } else {
            // Chamar o método carregar a view login.
            $this->viewlogin();
        }        

    }
     /**
     * Carregar a visualização logim.
     * 
     * Este método configura os dados necessários e carrega a VIEW para login.
     * 
     */
    private function viewLogin(): void
    {

        // Criar o título da página.
        $this->data['title_head'] = "Login";

        // Carregar a VIEW.
        $loadView = new LoadViewService("adms/Views/login/login", $this->data);
        $loadView->loadViewLogin();        
    }

    private function login():void
    {
        // Instanciar a classe validar os dados do formulário.
        $validationLogin = new ValidationLoginService();
        $this->data['errors'] = $validationLogin->validate($this->data['form']);

        // Acessa o IF quando existir campo com dados incorretos
        if (!empty($this->data['errors'])) {

            // Chamar o método carregar a view
            $this->viewLogin();

            return;
        }

        // Instanciar a calsse validar o usuário e senha.
        $validationUserLogin = new ValidationUserLogin();
        $result = $validationUserLogin->validationUserLogin($this->data['form']);

        if($result){

            // Redirecionar o usuário para página listar
            header("Location: {$_ENV['URL_ADM']}dashboard");

        }else{

            // Chamar o método para carregar a view login.
            $this->viewLogin();

            return;
        
        }
    
    }
}
