<?php

namespace App\adms\Controllers\login;

class Logout
{
    public function index() : void
    {

        // Eliminar os valores da sessão.
        unset($_SESSION['user_id'], $_SESSION['user_name'], $_SESSION['user_email']);

        // Criar a mensagem de sucesso
        $_SESSION['success'] = "Deslogado com sucesso!";

        // Redirecionar o usuário para página listar
        header("Location: {$_ENV['URL_ADM']}login");
    }
}