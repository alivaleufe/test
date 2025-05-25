<?php

namespace App\adms\Controllers\errors;

class Error403
{
    public function index()
    {
        echo "<h1>403 - Acesso restrito</h1>";
        echo "<p>Você não tem permissão para acessar esta página.</p>";
    }
}
