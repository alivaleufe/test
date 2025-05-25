<?php

declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

class AddAdmsUsers extends AbstractSeed
{
    /**
     * Cadastrar usuário no banco de dados
     */
    public function run(): void
    {

        // Variável para receber os dados.
        $data = [];

        // Verifica se o registro já existe no banco de dados
        $existingRecord = $this->query('SELECT id FROM adms_users WHERE email=:email', ['email' => 'cesar@celke.com.br'])->fetch();

        // Se o registro não existir, insere os dados na variável $data para em seguida cadastrar na tabela
        if (!$existingRecord) {

            // Criar o array com dados do usuário
            $data[] = [
                'name' => 'Cesar',
                'email' => 'cesar@celke.com.br',
                'username' => 'cesar@celke.com.br',
                'password' => password_hash('123456a', PASSWORD_DEFAULT),
                'created_at' => date("Y-m-d H:i:s"),
            ];
        }

        // Verifica se o registro já existe no banco de dados
        $existingRecord = $this->query('SELECT id FROM adms_users WHERE email=:email', ['email' => 'kelly@celke.com.br'])->fetch();

        // Se o registro não existir, insere os dados na variável $data para em seguida cadastrar na tabela
        if (!$existingRecord) {

            // Criar o array com dados do usuário
            $data[] = [
                'name' => 'Kelly',
                'email' => 'kelly@celke.com.br',
                'username' => 'kelly@celke.com.br',
                'password' => password_hash('123456a', PASSWORD_DEFAULT),
                'created_at' => date("Y-m-d H:i:s"),
            ];
        }

        // Verifica se o registro já existe no banco de dados
        $existingRecord = $this->query('SELECT id FROM adms_users WHERE email=:email', ['email' => 'jessica@celke.com.br'])->fetch();

        // Se o registro não existir, insere os dados na variável $data para em seguida cadastrar na tabela
        if (!$existingRecord) {

            // Criar o array com dados do usuário
            $data[] = [
                'name' => 'Jessica',
                'email' => 'jessica@celke.com.br',
                'username' => 'jessica@celke.com.br',
                'password' => password_hash('123456a', PASSWORD_DEFAULT),
                'created_at' => date("Y-m-d H:i:s"),
            ];
        }

        // Verifica se o registro já existe no banco de dados
        $existingRecord = $this->query('SELECT id FROM adms_users WHERE email=:email', ['email' => 'gabrielly@celke.com.br'])->fetch();

        // Se o registro não existir, insere os dados na variável $data para em seguida cadastrar na tabela
        if (!$existingRecord) {

            // Criar o array com dados do usuário
            $data[] = [
                'name' => 'Gabrielly',
                'email' => 'gabrielly@celke.com.br',
                'username' => 'gabrielly@celke.com.br',
                'password' => password_hash('123456a', PASSWORD_DEFAULT),
                'created_at' => date("Y-m-d H:i:s"),
            ];
        }

        // Indicar em qual tabela deve salvar
        $adms_users = $this->table('adms_users');

        // Inserir os registros na tabela
        $adms_users->insert($data)->save();
    }
}
