<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddUniqueContraintToAdmsUsers extends AbstractMigration
{
    /**
     * Alterar as colunas email e username para serem únicas
     */
    public function up(): void
    {
        // Acessa o IF quando a tabela existe no banco de dados
        if($this->hasTable('adms_users')){

            // Alterar a tabela para adicionar índices únicos
            $table = $this->table('adms_users');

            // Adicionar índices únicos às colunas email e username
            // 'name' => 'idx_unique_email' - nomear o índice único
            $table->addIndex(['email'], ['unique' => true, 'name' => 'idx_unique_email'])
                ->addIndex(['username'], ['unique' => true, 'name' => 'idx_unique_username'])
                ->update();
        }

    }

    // Método down() para reverter a migração (caso necessário)
    public function down(): void
    {
        // Acessa o IF quando a tabela existe no banco de dados
        if($this->hasTable('adms_users')){

            // Indicar a tabela para remover os índices únicos das colunas email e username
            $table = $this->table('adms_users');

            // Remover os índices únicos
            $table->removeIndexByName('idx_unique_email')
            ->removeIndexByName('idx_unique_username')
            ->update();
        }
    }
}
