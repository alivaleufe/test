<?php

namespace App\adms\Controllers\Services;

use App\adms\Helpers\SendEmailService;
use App\adms\Models\Repository\ResetPasswordRepository;

/**
 * Classe RecoverPassword
 *
 * Esta classe é responsável por gerenciar o processo de recuperação de senha dos usuários. 
 * Ela gera uma chave de recuperação, formata a data de validade, atualiza as informações 
 * do usuário no repositório de senhas e envia um e-mail com as instruções para recuperação.
 * 
 * @package App\adms\Controllers\Services
 */
class RecoverPassword
{
    /**
     * Método para recuperar senha
     *
     * Este método inicia o processo de recuperação de senha para um usuário. Ele gera uma 
     * chave de recuperação, configura o e-mail com as instruções de recuperação e envia 
     * o e-mail para o usuário. O método retorna um valor booleano indicando o sucesso 
     * ou falha do envio do e-mail.
     *
     * @param array $data Array contendo os dados do usuário e informações adicionais necessárias para a recuperação de senha.
     * @return bool Retorna true se o e-mail for enviado com sucesso, ou false em caso de falha.
     */
    public function recoverPassword(array $data): bool
    {
        // Instanciar o serviço para gerar a chave
        $valueGenerateKey = GenerateKeyService::generateKey();
        $data['key'] = $valueGenerateKey['key'];
        $data['recover_password'] = $valueGenerateKey['encryptedKey'];
        $data['validate_recover_password'] = date("Y-m-d H:i:s", strtotime('+1 hour'));

        // Formata a data e a hora separadamente
        $formattedTime = date("H:i:s", strtotime($data['validate_recover_password']));
        $formattedDate = date("d/m/Y", strtotime($data['validate_recover_password']));

        // Atualizar o usuário
        $userUpdate = new ResetPasswordRepository();
        $result = $userUpdate->updateFogotPassword($data);

        // Acessa o IF se o repository retornou TRUE
        if (!$result) {
            return false;
        }

        $name = explode(" ", $data['user']['name']);
        $firstName = $name[0];

        $subject = "Recuperar senha";
        $url = "{$_ENV['URL_ADM']}reset-password/{$data['key']}";

        // Corpo do e-mail em HTML
        $body = "<p>Olá, $firstName!</p>";
        $body .= "<p>Recebemos seu pedido para trocar a senha.</p>";
        $body .= "<p>Para fazer isso, clique no link abaixo ou copie e cole no seu navegador:</p>";
        $body .= "<p><a href='$url'>$url</a></p>";
        $body .= "<p>Este link é válido até as $formattedTime do dia $formattedDate.</p>";
        $body .= "<p>Se passar desse horário, será preciso pedir outro link.</p>";
        $body .= "<p>Se você não pediu essa troca, pode ignorar este e-mail. Sua senha continua a mesma.</p>";

        // Corpo alternativo do e-mail em texto plano
        $altBody = "Olá, $firstName!\n\n";
        $altBody .= "Recebemos seu pedido para trocar a senha.\n\n";
        $altBody .= "Para fazer isso, clique no link abaixo ou copie e cole no seu navegador:\n\n";
        $altBody .= "$url\n\n";
        $altBody .= "Este link é válido até as $formattedTime do dia $formattedDate.\n";
        $altBody .= "Se passar desse horário, será preciso pedir outro link.\n\n";
        $altBody .= "Se você não pediu essa troca, pode ignorar este e-mail. Sua senha continua a mesma.\n";


        // Envia o e-mail de recuperação de senha
        $sendEmail = new SendEmailService();
        $resultSendEmail = $sendEmail->sendEmail($data['user']['email'], $data['user']['name'], $subject, $body, $altBody);

        return $resultSendEmail;
    }
}
