<?php

namespace App\adms\Helpers;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * Classe SendEmailService
 *
 * Esta classe é responsável por enviar e-mails utilizando a biblioteca PHPMailer.
 * Ela configura o servidor SMTP, define o remetente, destinatário, assunto, corpo do e-mail
 * e envia o e-mail. Em caso de sucesso ou falha, os logs são gerados.
 *
 * @package App\adms\Helpers
 */

class SendEmailService
{

    /**
     * Envia um e-mail com as configurações fornecidas.
     *
     * @param string $email    Endereço de e-mail do destinatário.
     * @param string $name     Nome do destinatário.
     * @param string $subject  Assunto do e-mail.
     * @param string $body     Corpo do e-mail em formato HTML.
     * @param string $altBody  Corpo do e-mail em formato texto simples.
     *
     * @return bool            Retorna true se o e-mail foi enviado com sucesso, false caso contrário.
     */

    public static function sendEmail(string $email, string $name, string $subject, string $body, string $altBody) : bool
    {
        // Cria uma nova instância do PHPMailer.        
        $mail = new PHPMailer(true);

        try {


            //$mail->SMTPDebug = SMTP::DEBUG_SERVER;   
            $mail->CharSet = 'UFT-8';                   
            $mail->isSMTP();                                            
            $mail->Host       = $_ENV['MAIL_HOST'];           
            $mail->SMTPAuth   = true;                                   
            $mail->Username   = $_ENV['MAIL_USERNAME'];                    
            $mail->Password   = $_ENV['MAIL_PASSWORD'];                              
            $mail->SMTPSecure = $_ENV['MAIL_ENCRYPTION'];      
            $mail->Port       = $_ENV['MAIL_PORT'];
            
            $mail->setFrom($_ENV['EMAIL_ADM'], $_ENV['NAME_ADM']);
            $mail->addAddress($email, $name);
            
            $mail->isHTML(true);                               
            $mail->Subject = $subject;
            $mail->Body    = $body;
            $mail->AltBody = $altBody;

            $mail->send();

            // Chamar o método para salvar as logs.
            GenerateLog::generateLog("error", "E-mail não enviado.", ['email' => $email, 'subject' => $subject]);

            return true; // Retorna true se o e-mail foi enviado com sucesso

        } catch (Exception $e) {

            // Chamar o método para salvar as logs.
            GenerateLog::generateLog("error", "E-mail não enviado.", ['email' => $email, 'error' => $e->getMessage()]);

            return false; // Retorna false em caso de erro ao enviar o e-mail
        }
    }
}