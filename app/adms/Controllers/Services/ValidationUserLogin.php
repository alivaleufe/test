<?php

namespace App\adms\Controllers\Services;

use App\adms\Helpers\GenerateLog;
use App\adms\Models\Repository\LoginRepository;

class ValidationUserLogin
{
    public function ValidationUserLogin(array $data): bool
    {

        // Instanciar o Repository para validar o usuário no banco de dados.
        $login = new LoginRepository();
        $result = $login->getUser((string) $data['username']);

         // Verificar se encontrou o registro no banco de dados.
        if (!$result) {

            // Chamar o método para salvar o log.
            GenerateLog::generateLog("error", "Usuário incorreto", ['username' => $data['username']]);

            // Criar a mensagem de error.
            $_SESSION['error'] = "Usuário ou a senha incorreta!";

            return false;
        }

        if(password_verify($data['password'], $result['password'])){

        // Extrair o array para imprimir o elemento do array através do nome.
        extract($result);

         // Salvar os dados do usuário na sessão.
         $_SESSION['user_id'] = $id;
         $_SESSION['user_name'] = $name;
         $_SESSION['user_email'] = $email;

        return true;

        }

        // Chamar o método para salvar o log.
        GenerateLog::generateLog("error", "Senha incorreta.", ['username' => $data['username']]);

        // Criar a mensagem de erro.
        $_SESSION['error'] = "Usuário ou a senha incorreta!";

        return false;
    }
}