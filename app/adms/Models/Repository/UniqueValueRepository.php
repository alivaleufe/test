<?php

namespace App\adms\Models\Repository;

use App\adms\Models\Services\DbConnection;
use PDO;

/**
 * Repository responsável em verificar se existe um registro com dados fornecidos
 *
 * @author Celke
 */
class UniqueValueRepository extends DbConnection
{

    /**
     * Recuperar o registro com dado fornecido
     * @return bool Retornar falso se o valor fornecido já estiver cadastrado, verdadeiro caso contrário
     */
    public function getRecord($table, $column, $value, $except = null)
    {

        // QUERY para recuperar o registro do banco de dados
        $sql = "SELECT COUNT(id) as count FROM `{$table}` WHERE `{$column}` = :value";

        // Se houver um ID de exceção, adicionar condição à consulta
        if($except !== null){
            $sql .= " AND `id` != :except";
        }

        // Preparar a QUERY
        $stmt = $this->getConnection()->prepare($sql);

        // Substituir os links da QUERY pelo valor
        $stmt->bindParam(':value', $value, PDO::PARAM_STR);
        if($except !== null){
            $stmt->bindParam(':except', $except, PDO::PARAM_INT);
        }

        // Executar a QUERY
        $stmt->execute();

        // Retornar falso se o valor fornecido já estiver cadastrado, verdadeiro caso contrário
        return $stmt->fetchColumn() === 0;

    }

}