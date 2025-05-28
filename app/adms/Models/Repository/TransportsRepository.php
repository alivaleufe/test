<?php

namespace App\adms\Models\Repository;

use App\adms\Helpers\GenerateLog;
use App\adms\Models\Services\DbConnection;
use Exception;
use PDO;

/**
 * Repository responsável em buscar e manipular transportes no banco de dados.
 */
class TransportsRepository extends DbConnection
{
    private string $table = 'adms_transports'; // Nome da sua tabela

    // Método para listar todos os transportes com paginação
    public function getAllTransports(int $page = 1, int $limitResult = 10): array
    {
        $offset = max(0, ($page - 1) * $limitResult);
        // Selecione os campos que você quer listar na tabela principal
        $sql = "SELECT id, placa, modelo, marca, tipo, nome_motorista
                FROM {$this->table}
                ORDER BY id DESC
                LIMIT :limit OFFSET :offset";

        $stmt = $this->getConnection()->prepare($sql);
        $stmt->bindValue(':limit', $limitResult, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Método para contar a quantidade total de transportes
    public function getAmountTransports(): int
    {
        $sql = "SELECT COUNT(id) as amount_records FROM {$this->table}";
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->execute();
        return (int) ($stmt->fetch(PDO::FETCH_ASSOC)['amount_records'] ?? 0);
    }

    // Método para buscar um transporte específico pelo ID
    public function getTransport(int $id): array|bool
    {
        // Selecione todos os campos que você quer ver na página de detalhes/edição
        $sql = "SELECT id, placa, modelo, marca, tipo, capacidade, ano_fabricacao, nome_motorista, observacoes, created_at, updated_at
                FROM {$this->table}
                WHERE id = :id
                LIMIT 1"; // Adicionado LIMIT 1 para garantir um único resultado

        $stmt = $this->getConnection()->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Método para cadastrar um novo transporte
    public function createTransport(array $data): bool|int
    {
        try {
            // Ajuste os campos conforme a sua tabela `adms_transports`
            $sql = "INSERT INTO {$this->table} (placa, modelo, marca, tipo, capacidade, ano_fabricacao, nome_motorista, observacoes, created_at)
                    VALUES (:placa, :modelo, :marca, :tipo, :capacidade, :ano_fabricacao, :nome_motorista, :observacoes, :created_at)";

            $stmt = $this->getConnection()->prepare($sql);

            // Faça o bindValue para cada campo
            $stmt->bindValue(':placa', $data['placa'], PDO::PARAM_STR);
            $stmt->bindValue(':modelo', $data['modelo'], PDO::PARAM_STR);
            $stmt->bindValue(':marca', $data['marca'], PDO::PARAM_STR);
            $stmt->bindValue(':tipo', $data['tipo'], PDO::PARAM_STR);
            $stmt->bindValue(':capacidade', $data['capacidade'], PDO::PARAM_INT);
            $stmt->bindValue(':ano_fabricacao', $data['ano_fabricacao'], PDO::PARAM_INT); // Ou PARAM_STR se estiver tratando como string
            $stmt->bindValue(':nome_motorista', $data['nome_motorista'] ?? null, PDO::PARAM_STR); // Se for opcional
            $stmt->bindValue(':observacoes', $data['observacoes'] ?? null, PDO::PARAM_STR); // Se for opcional
            $stmt->bindValue(':created_at', date("Y-m-d H:i:s"));

            $stmt->execute();
            return $this->getConnection()->lastInsertId();
        } catch (Exception $e) {
            GenerateLog::generateLog("error", "Transporte não cadastrado.", ['placa' => $data['placa'], 'error' => $e->getMessage()]); //
            return false;
        }
    }

    // Método para atualizar os dados de um transporte
    public function updateTransport(array $data): bool
    {
        try {
            // Ajuste os campos
            $sql = "UPDATE {$this->table} SET 
                        placa = :placa, 
                        modelo = :modelo, 
                        marca = :marca, 
                        tipo = :tipo, 
                        capacidade = :capacidade, 
                        ano_fabricacao = :ano_fabricacao, 
                        nome_motorista = :nome_motorista, 
                        observacoes = :observacoes,
                        updated_at = :updated_at 
                    WHERE id = :id";

            $stmt = $this->getConnection()->prepare($sql);

            // Faça o bindValue para cada campo
            $stmt->bindValue(':placa', $data['placa'], PDO::PARAM_STR);
            $stmt->bindValue(':modelo', $data['modelo'], PDO::PARAM_STR);
            $stmt->bindValue(':marca', $data['marca'], PDO::PARAM_STR);
            $stmt->bindValue(':tipo', $data['tipo'], PDO::PARAM_STR);
            $stmt->bindValue(':capacidade', $data['capacidade'], PDO::PARAM_INT);
            $stmt->bindValue(':ano_fabricacao', $data['ano_fabricacao'], PDO::PARAM_INT);
            $stmt->bindValue(':nome_motorista', $data['nome_motorista'] ?? null, PDO::PARAM_STR);
            $stmt->bindValue(':observacoes', $data['observacoes'] ?? null, PDO::PARAM_STR);
            $stmt->bindValue(':updated_at', date("Y-m-d H:i:s"));
            $stmt->bindValue(':id', $data['id'], PDO::PARAM_INT);

            return $stmt->execute();
        } catch (Exception $e) {
            GenerateLog::generateLog("error", "Transporte não editado.", ['id' => $data['id'], 'error' => $e->getMessage()]); //
            return false;
        }
    }

    // Método para deletar um transporte
    public function deleteTransport(int $id): bool
    {
        try {
            $sql = "DELETE FROM {$this->table} WHERE id = :id LIMIT 1";
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $rowCount = $stmt->rowCount();
            if ($rowCount > 0) {
                return true;
            }
            GenerateLog::generateLog("warning", "Tentativa de deletar transporte não afetou linhas (ID pode não existir).", ['id' => $id]);
            return false;

        } catch (Exception $e) {
            GenerateLog::generateLog("error", "Exceção ao tentar deletar transporte no repositório.", [
                'id' => $id,
                'error_message' => $e->getMessage()
            ]);
            return false;
        }
    }
}