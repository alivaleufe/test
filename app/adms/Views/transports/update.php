<?php

use App\adms\Helpers\CSRFHelper; //

// Recupera os dados do formulário que podem ter sido enviados de volta pelo controller
// (seja do banco na primeira carga, ou do POST em caso de erro de validação)
$formData = $this->data['form'] ?? [];
$errors = $this->data['errors'] ?? [];
$transport_id = $formData['id'] ?? null; // Pega o ID para o action do formulário e para o CSRF token

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
            <li class="breadcrumb-item active">Editar</li>
        </ol>
    </div>

    <div class="card mb-4 border-light shadow">
        <div class="card-header hstack gap-2">
            <span>Editar Transporte</span>
            <span class="ms-auto d-sm-flex flex-row">
                <a href="<?php echo $_ENV['URL_ADM']; ?>list-transports" class="btn btn-info btn-sm me-1 mb-1"><i class="fa-solid fa-list"></i> Listar</a>
                <?php if ($transport_id): ?>
                    <a href="<?php echo $_ENV['URL_ADM'] . 'view-transport/' . $transport_id; ?>" class="btn btn-primary btn-sm me-1 mb-1"><i class="fa-regular fa-eye"></i> Visualizar</a>
                <?php endif; ?>
            </span>
        </div>

        <div class="card-body">
            <?php
            // Exibe mensagens de erro gerais do formulário ou de sucesso da sessão
            include './app/adms/Views/partials/alerts.php';
            if (isset($errors['form_error_message'])) { 
                echo "<div class='alert alert-danger' role='alert'>{$errors['form_error_message']}</div>";
            }
            ?>

            <form action="<?php echo $_ENV['URL_ADM'] . 'update-transport/' . $transport_id; ?>" method="POST" class="row g-3">
                <input type="hidden" name="csrf_token" value="<?php echo CSRFHelper::generateCSRFToken('form_update_transport'); ?>">
                <input type="hidden" name="id" value="<?php echo $transport_id; // Adiciona o ID como campo oculto se necessário, mas o controller já o tem via URL ?>">


                <div class="col-md-6">
                    <label for="placa" class="form-label">Placa <span class="text-danger">*</span></label>
                    <input type="text" name="placa" class="form-control <?php echo isset($errors['placa']) ? 'is-invalid' : ''; ?>" id="placa" placeholder="Ex: ABC-1234" value="<?php echo htmlspecialchars($formData['placa'] ?? ''); ?>">
                    <?php if (isset($errors['placa'])): ?>
                        <div class="invalid-feedback"><?php echo $errors['placa']; ?></div>
                    <?php endif; ?>
                </div>

                <div class="col-md-6">
                    <label for="modelo" class="form-label">Modelo <span class="text-danger">*</span></label>
                    <input type="text" name="modelo" class="form-control <?php echo isset($errors['modelo']) ? 'is-invalid' : ''; ?>" id="modelo" placeholder="Ex: Sprinter" value="<?php echo htmlspecialchars($formData['modelo'] ?? ''); ?>">
                    <?php if (isset($errors['modelo'])): ?>
                        <div class="invalid-feedback"><?php echo $errors['modelo']; ?></div>
                    <?php endif; ?>
                </div>

                <div class="col-md-6">
                    <label for="marca" class="form-label">Marca <span class="text-danger">*</span></label>
                    <input type="text" name="marca" class="form-control <?php echo isset($errors['marca']) ? 'is-invalid' : ''; ?>" id="marca" placeholder="Ex: Mercedes-Benz" value="<?php echo htmlspecialchars($formData['marca'] ?? ''); ?>">
                    <?php if (isset($errors['marca'])): ?>
                        <div class="invalid-feedback"><?php echo $errors['marca']; ?></div>
                    <?php endif; ?>
                </div>

                <div class="col-md-6">
                    <label for="tipo" class="form-label">Tipo <span class="text-danger">*</span></label>
                    <input type="text" name="tipo" class="form-control <?php echo isset($errors['tipo']) ? 'is-invalid' : ''; ?>" id="tipo" placeholder="Ex: Van, Ônibus" value="<?php echo htmlspecialchars($formData['tipo'] ?? ''); ?>">
                    <?php if (isset($errors['tipo'])): ?>
                        <div class="invalid-feedback"><?php echo $errors['tipo']; ?></div>
                    <?php endif; ?>
                </div>

                <div class="col-md-6">
                    <label for="capacidade" class="form-label">Capacidade (Passageiros) <span class="text-danger">*</span></label>
                    <input type="number" name="capacidade" class="form-control <?php echo isset($errors['capacidade']) ? 'is-invalid' : ''; ?>" id="capacidade" placeholder="Ex: 15" value="<?php echo htmlspecialchars($formData['capacidade'] ?? ''); ?>">
                    <?php if (isset($errors['capacidade'])): ?>
                        <div class="invalid-feedback"><?php echo $errors['capacidade']; ?></div>
                    <?php endif; ?>
                </div>

                <div class="col-md-6">
                    <label for="ano_fabricacao" class="form-label">Ano de Fabricação <span class="text-danger">*</span></label>
                    <input type="number" name="ano_fabricacao" class="form-control <?php echo isset($errors['ano_fabricacao']) ? 'is-invalid' : ''; ?>" id="ano_fabricacao" placeholder="Ex: 2020" value="<?php echo htmlspecialchars($formData['ano_fabricacao'] ?? ''); ?>" maxlength="4">
                    <?php if (isset($errors['ano_fabricacao'])): ?>
                        <div class="invalid-feedback"><?php echo $errors['ano_fabricacao']; ?></div>
                    <?php endif; ?>
                </div>

                <div class="col-md-12">
                    <label for="nome_motorista" class="form-label">Nome do Motorista</label>
                    <input type="text" name="nome_motorista" class="form-control <?php echo isset($errors['nome_motorista']) ? 'is-invalid' : ''; ?>" id="nome_motorista" placeholder="Nome completo do motorista" value="<?php echo htmlspecialchars($formData['nome_motorista'] ?? ''); ?>">
                    <?php if (isset($errors['nome_motorista'])): ?>
                        <div class="invalid-feedback"><?php echo $errors['nome_motorista']; ?></div>
                    <?php endif; ?>
                </div>

                <div class="col-12">
                    <label for="observacoes" class="form-label">Observações</label>
                    <textarea name="observacoes" class="form-control <?php echo isset($errors['observacoes']) ? 'is-invalid' : ''; ?>" id="observacoes" rows="3" placeholder="Detalhes adicionais sobre o transporte"><?php echo htmlspecialchars($formData['observacoes'] ?? ''); ?></textarea>
                    <?php if (isset($errors['observacoes'])): ?>
                        <div class="invalid-feedback"><?php echo $errors['observacoes']; ?></div>
                    <?php endif; ?>
                </div>

                <div class="col-12">
                    <button type="submit" class="btn btn-warning btn-sm">Salvar Alterações</button>
                </div>
            </form>
        </div>
    </div>
</div>