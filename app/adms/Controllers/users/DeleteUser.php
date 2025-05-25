<?php

namespace App\adms\Controllers\users;

use App\adms\Helpers\CSRFHelper;
use App\adms\Helpers\GenerateLog;
use App\adms\Models\Repository\UsersRepository;

/**
 * Controller apagar usuário
 *
 */
class DeleteUser
{

    /** @var array|string|null $dados Recebe os dados que devem ser enviados para VIEW */
    private array|string|null $data = null;

    /**
     * Recuperar os detalhes do usuário
     * 
     * @return void
     */
    public function index(): void
    {
        // Receber os dados do formulário
        $this->data['form'] = filter_input_array(INPUT_POST, FILTER_DEFAULT);

        // Acessar o IF se existir o CSRF e for valido o CSRF
        if (!isset($this->data['form']['csrf_token']) or !CSRFHelper::validateCSRFToken('form_delete_user', $this->data['form']['csrf_token']) or !isset($this->data['form']['id'])) {

        // Chamar o método para salvar o log
        GenerateLog::generateLog("error", "Usuário não encontrado", []);

        // Criar a mensagem de erro
        $_SESSION['error'] = "Usuário não encontrado!";

        // Redirecionar o usuário para página listar
        header("Location: {$_ENV['URL_ADM']}list-users");
        return;
        }

        // Instanciar o Repository para recuperar o regitro do banco de dados
        $deleteUser = new UsersRepository();
        $this->data['user'] = $deleteUser->getUser((int) $this->data['form']['id']);     
        
        // Verificar se encontrou o registro no banco de dados
        if (!$this->data['user']) {

        // Chamar o método para salvar o log
        GenerateLog::generateLog("error", "Usuário não encontrado", ['id' => (int) $this->data['form']['id']]);

        // Criar a mensagem de erro
        $_SESSION['error'] = "Usuário não encontrado!";

        // Redirecionar o usuário para página listar
        header("Location: {$_ENV['URL_ADM']}list-users");
        return;
        }

        // Instanciar o Repository para apagar o registro do banco de dados
        $result = $deleteUser->deleteUser($this->data['form']['id']);

        // Acessa o IF se o repository retornou TRUE
        if($result){

            // Criar a mensagem de sucesso
            $_SESSION['success'] = "Usuário apagado com sucesso!";
        }else{
            
            // Criar a mensagem de error
            $_SESSION['error'] = "Usuário não apagado!";
        }

        // Redirecionar o usuário para página listar
        header("Location: {$_ENV['URL_ADM']}list-users");
        return;
    }
}
