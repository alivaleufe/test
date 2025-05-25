<?php

namespace App\adms\Models\Repository;

use App\adms\Helpers\GenerateLog;
use App\adms\Models\Services\DbConnection;
use Exception;
use PDO;

/**
 * Repositório para operações de recuperação de senha.
 *
 * Esta classe é responsável por interagir com a base de dados para recuperar e 
 * atualizar informações relacionadas ao processo de recuperação de senha dos 
 * usuários. Ela lida com a obtenção de dados do usuário e a atualização de 
 * registros com as informações de recuperação de senha.
 *
 * @package App\adms\Models\Repository
 */
class ResetPasswordRepository extends DbConnection
{
    /**
     * Obtém o ID do usuário baseado em seu e-mail.
     *
     * Este método executa uma consulta na base de dados para recuperar o ID do 
     * usuário associado ao e-mail fornecido. Ele retorna um array com o ID do 
     * usuário caso o e-mail seja encontrado ou `false` se o usuário não existir.
     *
     * @param string $email E-mail do usuário.
     * @return array|bool Retorna o array com o ID do usuário ou `false` se não encontrado.
     */
    public function getUser(string $email): array|bool
    {
        // QUERY para recuperar o registro do banco de dados
        $sql = "SELECT id, name, email, recover_password, validate_recover_password FROM adms_users WHERE email = :email";

        // Preparar a QUERY
        $stmt = $this->getConnection()->prepare($sql);

        // Substituir os parâmetros da QUERY pelos valores
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);

        // Executar a QUERY
        $stmt->execute();

        // Ler o registro e retornar
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Atualiza as informações de recuperação de senha do usuário.
     *
     * Este método atualiza os dados de recuperação de senha de um usuário, 
     * incluindo a chave de recuperação, a data de validade e o horário de 
     * atualização. Em caso de falha, um log de erro é gerado.
     *
     * @param array $data Dados para atualização, incluindo e-mail e chave de recuperação.
     * @return bool Retorna `true` se a atualização for bem-sucedida ou `false` em caso de falha.
     */
    public function updateFogotPassword(array $data): bool
    {
        try {
            // QUERY para atualizar usuário
            $sql = 'UPDATE adms_users SET recover_password = :recover_password, validate_recover_password = :validate_recover_password, updated_at = :updated_at';

            // Condição para indicar qual registro editar
            $sql .= ' WHERE email = :email LIMIT 1';

            // Preparar a QUERY
            $stmt = $this->getConnection()->prepare($sql);

            // Substituir os parâmetros da QUERY pelos valores
            $stmt->bindValue(':recover_password', $data['recover_password'], PDO::PARAM_STR);
            $stmt->bindValue(':validate_recover_password', $data['validate_recover_password']);
            $stmt->bindValue(':updated_at', date("Y-m-d H:i:s"));
            $stmt->bindValue(':email', $data['form']['email'], PDO::PARAM_STR);

            // Executar a QUERY
            return $stmt->execute();
        } catch (Exception $e) {
            // Gerar log de erro
            GenerateLog::generateLog("error", "Recuperar senha não salvo no banco de dados.", ['email' => $data['email'], 'error' => $e->getMessage()]);

            return false;
        }
    }

    /**
     * Atualiza a senha do usuário.
     *
     * Este método atualiza a senha do usuário na base de dados. Além disso, ele 
     * limpa os campos relacionados à recuperação de senha. Em caso de erro, um 
     * log é gerado.
     *
     * @param array $data Dados para atualização, incluindo a nova senha e e-mail.
     * @return bool Retorna `true` se a senha for atualizada com sucesso, ou `false` em caso de falha.
     */
    public function updatePassword(array $data): bool
    {
        try {
            // QUERY para atualizar a senha do usuário
            $sql = 'UPDATE adms_users SET password = :password, recover_password = NULL, validate_recover_password = NULL, updated_at = :updated_at WHERE email = :email';

            // Preparar a QUERY
            $stmt = $this->getConnection()->prepare($sql);

            // Substituir os parâmetros da QUERY pelos valores
            $stmt->bindValue(':password', password_hash($data['password'], PASSWORD_DEFAULT));
            $stmt->bindValue(':updated_at', date("Y-m-d H:i:s"));
            $stmt->bindValue(':email', $data['email'], PDO::PARAM_STR);

            // Executar a QUERY
            return $stmt->execute();
        } catch (Exception $e) {
            // Gerar log de erro
            GenerateLog::generateLog("error", "Senha não editada.", ['email' => (string) $data['email'], 'error' => $e->getMessage()]);

            return false;
        }
    }
}
