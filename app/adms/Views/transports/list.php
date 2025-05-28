<?php

use App\adms\Helpers\CSRFHelper;

// Gera o token CSRF para proteger o formulário de deleção
// Você pode usar um identificador diferente se quiser, ex: 'form_delete_transport'
$csrf_token = CSRFHelper::generateCSRFToken('form_delete_transport');

?>

<div class="container-fluid px-4">

    <div class="mb-1 hstack gap-2">
        <h2 class="mt-3">Transportes</h2>

        <ol class="breadcrumb mb-3 mt-3 ms-auto">
            <li class="breadcrumb-item">
                <a href="<?php echo $_ENV['URL_ADM']; ?>dashboard" class="text-decoration-none">Dashboard</a>
            </li>
            <li class="breadcrumb-item">Transportes</li>
        </ol>
    </div>

    <div class="card mb-4 border-light shadow">

        <div class="card-header hstack gap-2">
            <span>Gerenciar Transportes</span>

            <span class="ms-auto">
                <a href="<?php echo $_ENV['URL_ADM']; ?>create-transport" class="btn btn-success btn-sm">
                    <i class="fa-regular fa-square-plus"></i> Cadastrar Novo Transporte
                </a>
            </span>
        </div>

        <div class="card-body">

            <?php // Inclui o arquivo que exibe mensagens de sucesso e erro
            include './app/adms/Views/partials/alerts.php'; //

            // Verifica se há transportes no array (vindo do controller ListTransports)
            if ($this->data['transports'] ?? false) {
            ?>

                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th scope="col">ID</th>
                            <th scope="col">Placa</th>
                            <th scope="col">Modelo</th>
                            <th scope="col" class="d-none d-md-table-cell">Marca</th>
                            <th scope="col" class="d-none d-md-table-cell">Tipo</th>
                            <th scope="col" class="d-none d-sm-table-cell">Motorista</th>
                            <th scope="col" class="text-center">Ações</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php
                        // Percorre o array de transportes
                        foreach ($this->data['transports'] as $transport) {
                            extract($transport); // Extrai as variáveis como $id, $placa, $modelo, etc.
                        ?>
                            <tr>
                                <td><?php echo $id; ?></td>
                                <td><?php echo $placa; ?></td>
                                <td><?php echo $modelo; ?></td>
                                <td class="d-none d-md-table-cell"><?php echo $marca; ?></td>
                                <td class="d-none d-md-table-cell"><?php echo $tipo; ?></td>
                                <td class="d-none d-sm-table-cell"><?php echo $nome_motorista ?? 'N/A'; ?></td>
                                <td class="d-md-flex flex-row justify-content-center">
                                    <a href='<?php echo $_ENV['URL_ADM'] . "view-transport/" . $id; ?>' class="btn btn-primary btn-sm me-1 mb-1">
                                        <i class="fa-regular fa-eye"></i> Visualizar
                                    </a>
                                    <a href='<?php echo $_ENV['URL_ADM'] . "update-transport/" . $id; ?>' class="btn btn-warning btn-sm me-1 mb-1">
                                        <i class="fa-solid fa-pen-to-square"></i> Editar
                                    </a>
                                    <form id="formDelete<?php echo $id; ?>" action="<?php echo $_ENV['URL_ADM']; ?>delete-transport" method="POST" class="d-inline">
                                        <input type="hidden" name="csrf_token" value="<?php echo CSRFHelper::generateCSRFToken('delete_transport_form'); ?>">
                                        <input type="hidden" name="id" value="<?php echo $id; ?>">
                                        <button type="button" class="btn btn-danger btn-sm me-1 mb-1" onclick="confirmDeletion(event, <?php echo $id; ?>)">
                                            <i class="fa-regular fa-trash-can"></i> Apagar
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>

            <?php
                // Inclui o arquivo de paginação
                include_once './app/adms/Views/partials/pagination.php'; //
            } else { // Exibe mensagem se nenhum transporte for encontrado
                echo "<div class='alert alert-info' role='alert'>Nenhum transporte encontrado!</div>";
            } ?>
        </div>
    </div>
</div>