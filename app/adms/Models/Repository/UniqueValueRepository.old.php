<?php

namespace App\adms\Models\Repository;

use App\adms\Models\Services\DbConnection;
use PDO;
use App\adms\Helpers\GenerateLog;
use Exception;

class UniqueValueRepository extends DbConnection
{
    // Modificado para aceitar $exceptIdColumnName
    public function getRecord($table, $column, $value, $exceptValue = null, $exceptIdColumnName = 'id')
    {
        try {
            $sql = "SELECT COUNT({$exceptIdColumnName}) as count FROM `{$table}` WHERE `{$column}` = :value";

            // Se houver um valor de exceção e uma coluna de ID para exceção, adicionar condição à consulta
            if ($exceptValue !== null && $exceptValue !== '') { // Adicionado verificação para $exceptValue não ser string vazia
                // Garante que $exceptIdColumnName é um nome de coluna seguro.
                // Idealmente, validar contra uma lista de colunas permitidas se viesse de fonte não confiável.
                // Mas como aqui é 'id' ou definido por nós, é relativamente seguro.
                $sql .= " AND `{$exceptIdColumnName}` != :exceptValue";
            }

            $stmt = $this->getConnection()->prepare($sql);
            $stmt->bindParam(':value', $value, PDO::PARAM_STR);

            if ($exceptValue !== null && $exceptValue !== '') {
                // Se $exceptValue puder ser string "NULL" vinda da regra, tratar.
                // Aqui, assumimos que $exceptValue é o ID real.
                $stmt->bindParam(':exceptValue', $exceptValue); // PDO tentará inferir o tipo, ou use PDO::PARAM_INT se for sempre ID numérico
            }

            $stmt->execute();
            return $stmt->fetchColumn() === 0;

        } catch (Exception $e) {
            GenerateLog::generateLog("error", "Erro no UniqueValueRepository ao buscar registro.", [
                'error' => $e->getMessage(),
                'table' => $table,
                'column' => $column,
                'value' => $value,
                'exceptValue' => $exceptValue,
                'exceptIdColumnName' => $exceptIdColumnName
            ]);
            // Em caso de erro na query (ex: tabela não existe), a validação deve falhar.
            // Retornar false fará a regra de unicidade falhar.
            return false;
        }
    }
}