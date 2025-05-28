<?php

// O error_log DEVE VIR DEPOIS da declaração de namespace ou declare.
// Para fins de depuração SE o arquivo está sendo incluído, coloque o log APÓS o namespace,
// ou use outros métodos de depuração se precisar antes do namespace.
// Por enquanto, vamos remover o log que estava antes do namespace.

declare(strict_types=1); // Se você usar strict_types, ele vem primeiro.

namespace App\adms\Controllers\Services\Validation;

// Agora os logs e o resto do código
// error_log("DEBUG: UniqueRule.php file was included/parsed at " . date("Y-m-d H:i:s")); // Movido ou removido

use App\adms\Models\Repository\UniqueValueRepository;
use Rakit\Validation\Rule;
use App\adms\Helpers\GenerateLog;
use Exception;

class UniqueRule extends Rule
{
    protected $message = "O :attribute :value já está cadastrado.";
    protected $fillableParams = ['table', 'column', 'except', 'idColumn'];

    public function check($value): bool
    {
        // Log de entrada no método (este está no lugar correto)
        error_log("DEBUG: UniqueRule::check() ENTERED for value: " . $value . " at " . date("Y-m-d H:i:s"));
        try {
            $this->requireParameters(['table', 'column']);
            $table = $this->parameter('table');
            $column = $this->parameter('column');
            
            $exceptValue = $this->parameter('except');
            $idColumnName = $this->parameter('idColumn');

            if ($idColumnName === null) {
                $idColumnName = 'id'; // Padrão para 'id' se não especificado
            }
            if (is_string($exceptValue) && strtoupper($exceptValue) === 'NULL') {
                $exceptValue = null; // Converte a string "NULL" para o valor null real
            }

            error_log("DEBUG: UniqueRule params: table={$table}, column={$column}, exceptValue=" . print_r($exceptValue, true) . " (Type: " . gettype($exceptValue) . "), idColumnName={$idColumnName}");

            $validateUniqueValue = new UniqueValueRepository();
            $result = $validateUniqueValue->getRecord($table, $column, $value, $exceptValue, $idColumnName);
            
            error_log("DEBUG: Result from getRecord in UniqueRule: " . ($result ? 'true (is unique)' : 'false (not unique)'));
            error_log("DEBUG: UniqueRule::check() END");
            return $result;

        } catch (Exception $e) {
            error_log("EXCEPTION in UniqueRule: " . $e->getMessage() . " for value: " . $value);
            GenerateLog::generateLog("error", "Erro ao verificar valor único na UniqueRule.", [
                'error' => $e->getMessage(),
                'value' => $value,
                'table' => $this->parameter('table'), // Melhor usar a variável $table se já definida
                'column' => $this->parameter('column'), // Melhor usar a variável $column se já definida
                'params_passed' => $this->getParameters() 
            ]);
            return false;
        }
    }
}