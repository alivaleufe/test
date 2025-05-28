<?php
// Garante que $this->data['transport'] existe e não é nulo antes de tentar usá-lo.
if (!isset($this->data['transport']) || !$this->data['transport']) {
    echo "<div class='alert alert-danger text-center'>Erro: Transporte não encontrado!</div>";
    // Você pode adicionar um link para voltar para a lista ou um redirecionamento aqui se desejar.
    // Ex: echo "<a href='" . $_ENV['URL_ADM'] . "list-transports'>Voltar para a lista</a>";
    return; // Interrompe a renderização se não houver dados do transporte.
}

// Extrai os dados do transporte para variáveis locais (ex: $placa, $modelo, etc.)
extract($this->data['transport']);

use App\adms\Helpers\CSRFHelper; //
$csrf_token = CSRFHelper::generateCSRFToken('form_delete_transport_view'); // Novo token para exclusão a partir desta view

?>
<div class="container-fluid px-4">

    <div class="mb-1 hstack gap-2">
        <h2 class="mt-3">Transportes</h2>

        <ol class="breadcrumb mb-3 mt-3 ms-auto">
            <li class="breadcrumb-item">
                <a href="<?php echo $_ENV['URL_ADM']; ?>dashboard" class="text-decoration-none">Dashboard</a>
            </li>
            <li class="breadcrumb-item">
                <a href="<?php echo $_ENV['URL_ADM']; ?>list-transports" class="text-decoration-none">Transportes</a>
            </li>
            <li class="breadcrumb-item active">Visualizar</li>
        </ol>
    </div>

    <div class="card mb-4 border-light shadow">
        <div class="card-header hstack gap-2">
            <span>Visualizar Detalhes do Transporte</span>
            <span class="ms-auto d-md-flex">
                <a href="<?php echo $_ENV['URL_ADM']; ?>list-transports" class="btn btn-info btn-sm me-1 mb-1"><i class="fa-solid fa-list"></i> Listar</a>
                <a href="<?php echo $_ENV['URL_ADM'] . 'update-transport/' . $id; ?>" class="btn btn-warning btn-sm me-1 mb-1"><i class="fa-solid fa-pen-to-square"></i> Editar</a>

                <form id="formDelete<?php echo $id; ?>" action="<?php echo $_ENV['URL_ADM']; ?>delete-transport" method="POST" class="d-inline">
                    <input type="hidden" name="csrf_token" value="<?php echo CSRFHelper::generateCSRFToken('delete_transport_form'); ?>">
                    <input type="hidden" name="id" value="<?php echo $id; ?>">
                    <button type="button" class="btn btn-danger btn-sm me-1 mb-1" onclick="confirmDeletion(event, <?php echo $id; ?>)">
                        <i class="fa-regular fa-trash-can"></i> Apagar
                    </button>
                </form>
            </span>
        </div>

        <div class="card-body">
            <?php
            // Inclui o arquivo que exibe mensagens de sucesso e erro da sessão
            include './app/adms/Views/partials/alerts.php'; //
            ?>

            <dl class="row">

                <dt class="col-sm-3">ID:</dt>
                <dd class="col-sm-9"><?php echo $id; ?></dd>

                <dt class="col-sm-3">Placa:</dt>
                <dd class="col-sm-9"><?php echo htmlspecialchars($placa); ?></dd>

                <dt class="col-sm-3">Modelo:</dt>
                <dd class="col-sm-9"><?php echo htmlspecialchars($modelo); ?></dd>

                <dt class="col-sm-3">Marca:</dt>
                <dd class="col-sm-9"><?php echo htmlspecialchars($marca); ?></dd>

                <dt class="col-sm-3">Tipo:</dt>
                <dd class="col-sm-9"><?php echo htmlspecialchars($tipo); ?></dd>

                <dt class="col-sm-3">Capacidade:</dt>
                <dd class="col-sm-9"><?php echo htmlspecialchars($capacidade); ?> passageiros</dd>

                <dt class="col-sm-3">Ano de Fabricação:</dt>
                <dd class="col-sm-9"><?php echo htmlspecialchars($ano_fabricacao); ?></dd>

                <dt class="col-sm-3">Nome do Motorista:</dt>
                <dd class="col-sm-9"><?php echo !empty($nome_motorista) ? htmlspecialchars($nome_motorista) : 'N/A'; ?></dd>

                <dt class="col-sm-3">Observações:</dt>
                <dd class="col-sm-9"><?php echo !empty($observacoes) ? nl2br(htmlspecialchars($observacoes)) : 'N/A'; ?></dd>

                <dt class="col-sm-3">Cadastrado em:</dt>
                <dd class="col-sm-9"><?php echo isset($created_at) ? date('d/m/Y H:i:s', strtotime($created_at)) : 'N/A'; ?></dd>

                <dt class="col-sm-3">Atualizado em:</dt>
                <dd class="col-sm-9"><?php echo isset($updated_at) && $updated_at ? date('d/m/Y H:i:s', strtotime($updated_at)) : 'N/A'; ?></dd>

            </dl>
        </div>
    </div>
</div>