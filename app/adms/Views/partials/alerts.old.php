<?php

// Exibe mensagens de sucesso e erro armazenadas na sessão.
// O operador ternário verifica se a chave 'success' existe em $_SESSION. Se existir, exibe a mensagem com estilo de texto verde (#086).
// Caso contrário, não exibe nada.
echo isset($_SESSION['success']) ? "<div class='alert alert-success' role='alert'>{$_SESSION['success']}</div>" : "";

// O mesmo operador ternário é usado para a chave 'error'. Se a chave existir, exibe a mensagem com estilo de texto vermelho (#f00).
// Caso contrário, não exibe nada.
echo isset($_SESSION['error']) ? "<div class='alert alert-danger' role='alert'>{$_SESSION['error']}</div>" : "";

// Remove as mensagens de sucesso e erro da sessão após exibi-las para evitar que sejam exibidas novamente em carregamentos subsequentes.
unset($_SESSION['success'], $_SESSION['error']);

// Verifica se há erros armazenados em $this->data['errors'].
// Se a chave 'errors' estiver presente no array, itera sobre cada erro e o exibe com estilo de texto vermelho (#f00).
if (isset($this->data['errors'])) {
    foreach ($this->data['errors'] as $error) {
        echo "<div class='alert alert-danger' role='alert'>$error</div>";
    }
}
