<?php

namespace App\adms\Models\Repository;

use App\adms\Helpers\GenerateLog;
use App\adms\Models\Services\DbConnection;
use Exception;
use PDO;

/**
 * Repository responsável em buscar e manipular usuários no banco de dados.
 *
 * Esta classe fornece métodos para recuperar, criar, atualizar e deletar usuários no banco de dados.
 * Ela estende a classe `DbConnection` para gerenciar conexões com o banco de dados e utiliza o `GenerateLog`
 * para registrar erros que ocorrem durante as operações.
 *
 * @package App\adms\Models\Repository
 * @author Cesar <cesar@celke.com.br>
 */
class UsersRepository extends DbConnection
{

    /**
     * Recuperar todos os usuários com paginação.
     *
     * Este método retorna uma lista de usuários da tabela `adms_users`, com suporte à paginação.
     *
     * @param int $page Número da página para recuperação de usuários (começa do 1).
     * @param int $limitResult Número máximo de resultados por página.
     * @return array Lista de usuários recuperados do banco de dados.
     */
    public function getAllUsers(int $page = 1, int $limitResult = 10): array
    {
        // Calcular o registro inicial, função max para garantir valor mínimo 0
        $offset = max(0, ($page - 1) * $limitResult);

        // QUERY para recuperar os registros do banco de dados
        $sql = 'SELECT id, name, email 
                FROM adms_users                
                ORDER BY id DESC
                LIMIT :limit OFFSET :offset';

        // Preparar a QUERY
        $stmt = $this->getConnection()->prepare($sql);

        // Substituir os parâmetros da QUERY pelos valores
        $stmt->bindValue(':limit', $limitResult, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

        // Executar a QUERY
        $stmt->execute();

        // Ler os registros e retornar
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Recuperar a quantidade total de usuários para paginação.
     *
     * Este método retorna a quantidade total de usuários na tabela `adms_users`, útil para a paginação.
     *
     * @return int Quantidade total de usuários encontrados no banco de dados.
     */
    public function getAmountUsers(): int
    {
        // QUERY para recuperar a quantidade de registros
        $sql = 'SELECT COUNT(id) as amount_records
                FROM adms_users';

        $stmt = $this->getConnection()->prepare($sql);
        $stmt->execute();

        return (int) ($stmt->fetch(PDO::FETCH_ASSOC)['amount_records'] ?? 0);
    }

    /**
     * Recuperar um usuário específico pelo ID.
     *
     * Este método retorna os detalhes de um usuário específico identificado pelo ID.
     *
     * @param int $id ID do usuário a ser recuperado.
     * @return array|bool Detalhes do usuário recuperado ou `false` se não encontrado.
     */
    public function getUser(int $id): array|bool
    {
        // QUERY para recuperar o registro do banco de dados
        $sql = 'SELECT id, name, email, username, created_at, updated_at
                FROM adms_users
                WHERE id = :id
                ORDER BY id DESC';

        // Preparar a QUERY
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        // Executar a QUERY
        $stmt->execute();

        // Ler o registro e retornar
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Cadastrar um novo usuário.
     *
     * Este método insere um novo usuário na tabela `adms_users`. Em caso de erro, um log é gerado.
     *
     * @param array $data Dados do usuário a ser cadastrado, incluindo `name`, `email`, `username`, `password`.
     * @return bool|int `true` se o usuário foi criado com sucesso ou `false` em caso de erro.
     */
    public function createUser(array $data): bool|int
    {
        try {
            // QUERY para cadastrar usuários
            $sql = 'INSERT INTO adms_users (name, email, username, password, created_at) VALUES (:name, :email, :username, :password, :created_at)';

            // Preparar a QUERY
            $stmt = $this->getConnection()->prepare($sql);

            // Substituir os parâmetros da QUERY pelos valores
            $stmt->bindValue(':name', $data['name'], PDO::PARAM_STR);
            $stmt->bindValue(':email', $data['email'], PDO::PARAM_STR);
            $stmt->bindValue(':username', $data['email'], PDO::PARAM_STR);
            $stmt->bindValue(':password', password_hash($data['password'], PASSWORD_DEFAULT));
            $stmt->bindValue(':created_at', date("Y-m-d H:i:s"));

            // Executar a QUERY
            $stmt->execute();

            // Retornar o ID do usuário recém-cadastrado
            return $this->getConnection()->lastInsertId();
        } catch (Exception $e) {
            // Gerar log de erro
            GenerateLog::generateLog("error", "Usuário não cadastrado.", ['email' => $data['email'], 'error' => $e->getMessage()]);

            return false;
        }
    }

    /**
     * Atualizar os dados de um usuário existente.
     *
     * Este método atualiza as informações de um usuário existente. Se a senha for fornecida, ela também será atualizada.
     * Em caso de erro, um log é gerado.
     *
     * @param array $data Dados atualizados do usuário, incluindo `id`, `name`, `email`, `username`, e opcionalmente `password`.
     * @return bool `true` se a atualização foi bem-sucedida ou `false` em caso de erro.
     */
    public function updateUser(array $data): bool
    {
        try {
            // QUERY para atualizar usuário
            $sql = 'UPDATE adms_users SET name = :name, email = :email, username = :username, updated_at = :updated_at';

            // Verificar se a senha está incluída nos dados e, se sim, adicionar ao SQL
            if (!empty($data['password'])) {
                $sql .= ', password = :password';
            }

            // Condição para indicar qual registro editar
            $sql .= ' WHERE id = :id';

            // Preparar a QUERY
            $stmt = $this->getConnection()->prepare($sql);

            // Substituir os parâmetros da QUERY pelos valores
            $stmt->bindValue(':name', $data['name'], PDO::PARAM_STR);
            $stmt->bindValue(':email', $data['email'], PDO::PARAM_STR);
            $stmt->bindValue(':username', $data['username'], PDO::PARAM_STR);
            $stmt->bindValue(':updated_at', date("Y-m-d H:i:s"));
            $stmt->bindValue(':id', $data['id'], PDO::PARAM_INT);

            // Substituir o parâmetro da senha se a mesma estiver presente
            if (!empty($data['password'])) {
                $stmt->bindValue(':password', password_hash($data['password'], PASSWORD_DEFAULT));
            }

            // Executar a QUERY
            return $stmt->execute();
        } catch (Exception $e) {
            // Gerar log de erro
            GenerateLog::generateLog("error", "Usuário não editado.", ['id' => $data['id'], 'error' => $e->getMessage()]);

            return false;
        }
    }

    /**
     * Atualizar a senha de um usuário.
     *
     * Este método atualiza a senha de um usuário específico. Em caso de erro, um log é gerado.
     *
     * @param array $data Dados atualizados do usuário, incluindo `id` e `password`.
     * @return bool `true` se a atualização foi bem-sucedida ou `false` em caso de erro.
     */
    public function updatePasswordUser(array $data): bool
    {
        try {
            // QUERY para atualizar a senha do usuário
            $sql = 'UPDATE adms_users SET password = :password, updated_at = :updated_at WHERE id = :id';

            // Preparar a QUERY
            $stmt = $this->getConnection()->prepare($sql);

            // Substituir os parâmetros da QUERY pelos valores
            $stmt->bindValue(':password', password_hash($data['password'], PASSWORD_DEFAULT));
            $stmt->bindValue(':updated_at', date("Y-m-d H:i:s"));
            $stmt->bindValue(':id', $data['id'], PDO::PARAM_INT);

            // Executar a QUERY
            return $stmt->execute();
        } catch (Exception $e) {
            // Gerar log de erro
            GenerateLog::generateLog("error", "Senha não editada.", ['id' => $data['id'], 'error' => $e->getMessage()]);

            return false;
        }
    }

    /**
     * Deletar um usuário pelo ID.
     *
     * Este método remove um usuário específico da tabela `adms_users`. Em caso de erro, um log é gerado.
     *
     * @param int $id ID do usuário a ser deletado.
     * @return bool `true` se o usuário foi deletado com sucesso ou `false` em caso de erro.
     */
    public function deleteUser(int $id): bool
    {
        try {
            // QUERY para deletar usuário
            $sql = 'DELETE FROM adms_users WHERE id = :id LIMIT 1';

            // Preparar a QUERY
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);

            // Executar a QUERY
            $stmt->execute();

            // Verificar o número de linhas afetadas
            $affectedRows = $stmt->rowCount();

            if ($affectedRows > 0) {
                return true;
            } else {
                // Gerar log de erro
                GenerateLog::generateLog("error", "Usuário não apagado.", ['id' => $id]);

                return false;
            }
        } catch (Exception $e) {
            // Gerar log de erro
            GenerateLog::generateLog("error", "Usuário não apagado.", ['id' => $id, 'error' => $e->getMessage()]);

            return false;
        }
    }
}
