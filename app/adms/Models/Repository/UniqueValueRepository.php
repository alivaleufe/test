<?php

namespace App\adms\Models\Repository;

use App\adms\Models\Services\DbConnection;
use PDO;
use App\adms\Helpers\GenerateLog;
use Exception;

class UniqueValueRepository extends DbConnection
{
    public function getRecord($table, $column, $value, $exceptValue = null, $exceptIdColumnName = 'id')
    {
        error_log("DEBUG: UniqueValueRepository::getRecord CALLED.");
        error_log("DEBUG: Params: table={$table}, column={$column}, value={$value}, exceptValue=" . print_r($exceptValue, true) . " (Type: " . gettype($exceptValue) . "), exceptIdColumnName={$exceptIdColumnName}");

        try {
            // Garante que os nomes da tabela e colunas não contenham caracteres maliciosos (simplificado)
            // Idealmente, validar contra uma lista de permissões se fossem muito dinâmicos.
            $safeTable = preg_replace('/[^a-zA-Z0-9_]/', '', $table);
            $safeColumn = preg_replace('/[^a-zA-Z0-9_]/', '', $column);
            $safeExceptIdColumnName = preg_replace('/[^a-zA-Z0-9_]/', '', $exceptIdColumnName);

            if ($safeTable !== $table || $safeColumn !== $column || $safeExceptIdColumnName !== $exceptIdColumnName) {
                error_log("DEBUG: Potential unsafe characters in table/column names. Original: t=$table, c=$column, eC=$exceptIdColumnName");
                // Decide como lidar: lançar exceção, retornar false, etc.
                // Por segurança, se houver alteração, pode ser um problema.
            }

            $sql = "SELECT COUNT(`{$safeExceptIdColumnName}`) as count FROM `{$safeTable}` WHERE `{$safeColumn}` = :value";

            $addExceptCondition = false;
            if ($exceptValue !== null && (string)$exceptValue !== '' && strtoupper((string)$exceptValue) !== 'NULL') {
                $addExceptCondition = true;
                $sql .= " AND `{$safeExceptIdColumnName}` != :exceptValue";
            }

            error_log("DEBUG: SQL Query for Unique Check: " . $sql);

            $stmt = $this->getConnection()->prepare($sql);
            $stmt->bindParam(':value', $value, PDO::PARAM_STR);

            if ($addExceptCondition) {
                // Tenta converter para int se a coluna de exceção for tipicamente um ID
                $idToExclude = ($safeExceptIdColumnName === 'id' || str_ends_with(strtolower($safeExceptIdColumnName), 'id')) ? (int)$exceptValue : $exceptValue;
                error_log("DEBUG: Binding :exceptValue = " . $idToExclude . " (Type: " . gettype($idToExclude) . ")");
                $stmt->bindParam(':exceptValue', $idToExclude); // Deixe o PDO tentar o tipo, ou especifique se souber
            }

            $stmt->execute();
            $countResult = $stmt->fetchColumn();
            
            // fetchColumn retorna false em caso de erro, ou o valor da coluna.
            if ($countResult === false) {
                error_log("DEBUG: fetchColumn() returned false. Possible SQL error or no rows matched (for COUNT, means 0).");
                // Para COUNT, se não há erro SQL, fetchColumn deve retornar 0 se não houver linhas, ou o valor.
                // Se a query falhou (ex: sintaxe), uma PDOException deveria ter sido lançada.
                // Se retorna false aqui, pode ser que a query não encontrou NADA, o que para COUNT é 0.
                // Vamos assumir que se é false aqui, o count é 0, mas é estranho.
                // Uma exceção deveria ter sido lançada se a query falhasse.
                $count = 0; 
            } else {
                $count = (int)$countResult; // Converte para inteiro (ex: "0" -> 0, "1" -> 1)
            }

            error_log("DEBUG: Count from DB: " . $count . " (Original from fetchColumn: " . print_r($countResult, true) . ")");

            $isUnique = ($count === 0);
            error_log("DEBUG: Is Unique (count is 0?): " . ($isUnique ? 'YES' : 'NO'));
            error_log("DEBUG: UniqueValueRepository::getRecord END.");
            return $isUnique;

        } catch (Exception $e) {
            error_log("EXCEPTION in UniqueValueRepository: " . $e->getMessage());
            GenerateLog::generateLog("error", "Erro no UniqueValueRepository.", [
                'error' => $e->getMessage(),
                'table' => $table, 'column' => $column, 'value' => $value,
                'exceptValue' => $exceptValue, 'exceptIdColumnName' => $exceptIdColumnName
            ]);
            return false; // Falha a validação em caso de exceção
        }
    }
}