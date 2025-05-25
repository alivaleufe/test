<?php

namespace App\adms\Controllers\Services;

class GenerateKeyService
{

    public static function generatekey() : array
    {
        // Definindo os caracteres possíveis com letras e números.
        $chars = 'abcdefghijklmnopqrstuvwxyz0123456789';

        // Embaralhando todos os caracteres.
        $shuffle = str_shuffle($chars);

        // Extraindo a chabe de 12 caracteres.
        $key = substr($shuffle, 0, 12);

        // Criptografar a chave.
        $encryptedKey = password_hash($key, PASSWORD_DEFAULT);

        // Retornar a chave em texto claro e criptografado.
        return ['key' => $key, 'encryptedKey' => $encryptedKey]; 
    }
}