<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AdmsUsers extends AbstractMigration
{
    /**
     * Cria a tabela AdmsUsers
     * 
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     * 
     */
    public function up(): void
    {

        // Acessa o IF quando não existe a tabela no banco de dados
        if (!$this->hasTable('adms_users')) {

            // Definir o nome da tabela
            $table = $this->table('adms_users');

            // Definir as colunas da tabela
            $table->addColumn('name', 'string', ['null' => false])
                ->addColumn('email', 'string', ['null' => false])
                ->addColumn('username', 'string', ['null' => false])
                ->addColumn('password', 'string', ['null' => false])
                ->addColumn('created_at', 'timestamp')
                ->addColumn('updated_at', 'timestamp')
                ->create();
        }
    }

    // Método down() para reverter a migração (caso necessário)
    public function down(): void
    {
        // Apagar a tabela adms_users
        $this->table('adms_users')->drop()->save();
    }
}
