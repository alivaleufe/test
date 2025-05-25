<?php

namespace App\adms\Models\Repository;

use App\adms\Models\Services\DbConnection;
use PDO;

/**
 * Repository responsável em buscar os usuários no banco de dados.
 */
class LoginRepository extends DbConnection
{

    public function getUser(string $username): array|bool
    {

        // QUERY para recuperar o registro do banco de dados
        $sql = 'SELECT id, name, email, username, password
                FROM adms_users
                WHERE email = :email
                OR username = :username
                LIMIT 1';

        // Preparar a QUERY
        $stmt = $this->getConnection()->prepare($sql);

        // Substituir o link da QUERY pelo valor
        $stmt->bindValue(':email', $username, PDO::PARAM_STR);
        $stmt->bindValue(':username', $username, PDO::PARAM_STR);
        // Executar a QUERY
        $stmt->execute();

        // Ler o registro e retornar 
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}    