<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateAdmsTransportesTable extends AbstractMigration // Certifique-se de que o nome da classe aqui é o mesmo do nome do arquivo da migration
{
    public function change(): void
    {
        $tableName = 'adms_transports';

        // Verifica se a tabela já NÃO existe para evitar erros
        if (!$this->hasTable($tableName)) {
            $table = $this->table($tableName); // Removido o comment daqui também

            $table->addColumn('placa', 'string', [
                    'limit' => 10,
                    'null' => false,
                ])
                ->addColumn('modelo', 'string', [
                    'limit' => 100,
                    'null' => false,
                ])
                ->addColumn('marca', 'string', [
                    'limit' => 50,
                    'null' => false,
                ])
                ->addColumn('tipo', 'string', [
                    'limit' => 50,
                    'null' => false,
                ])
                ->addColumn('capacidade', 'integer', [
                    'null' => false,
                    'signed' => false,
                ])
                ->addColumn('ano_fabricacao', 'year', [
                    'null' => false,
                ])
                ->addColumn('nome_motorista', 'string', [ // Coluna para o nome do motorista
                    'limit' => 255,
                    'null' => true, // Pode ser nulo se o transporte puder ser cadastrado sem motorista
                ])
                ->addColumn('observacoes', 'text', [
                    'null' => true,
                ])
                ->addColumn('created_at', 'timestamp', [
                    'default' => 'CURRENT_TIMESTAMP',
                    'null' => false,
                ])
                ->addColumn('updated_at', 'timestamp', [
                    'null' => true,
                    'default' => null,
                    'update' => 'CURRENT_TIMESTAMP',
                ])
                ->addIndex(['placa'], [
                    'unique' => true,
                    'name' => 'uidx_transporte_placa' // É uma boa prática nomear seus índices
                ])
                ->create();
        }
    }
}