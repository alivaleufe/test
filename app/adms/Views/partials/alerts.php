<?php

// Exibe mensagens de sucesso e erro armazenadas na sessão.
if (isset($_SESSION['success'])) {
    echo "<div class='alert alert-success' role='alert'>{$_SESSION['success']}</div>";
    unset($_SESSION['success']);
}

if (isset($_SESSION['error'])) {
    echo "<div class='alert alert-danger' role='alert'>{$_SESSION['error']}</div>";
    unset($_SESSION['error']);
}

// Verifica se há um erro geral de formulário em $this->data['errors']
// Este erro NÃO deve ser um array de erros de campo, mas uma string única.
// Usaremos a chave 'form_error_message' como convenção para um erro geral do formulário.
if (isset($this->data['errors']['form_error_message']) && is_string($this->data['errors']['form_error_message'])) {
    echo "<div class='alert alert-danger' role='alert'>{$this->data['errors']['form_error_message']}</div>";
}
// Para compatibilidade com o ValidationEmptyFieldService ou outros erros gerais que usam a chave 'msg'
else if (isset($this->data['errors']['msg']) && is_string($this->data['errors']['msg'])) {
     echo "<div class='alert alert-danger' role='alert'>{$this->data['errors']['msg']}</div>";
}
// Se $this->data['errors'] for uma string simples (não um array de erros de validação de campo)
// e não for uma das chaves já tratadas acima.
else if (isset($this->data['errors']) && is_string($this->data['errors'])) {
    echo "<div class='alert alert-danger' role='alert'>{$this->data['errors']}</div>";
}
// Se $this->data['errors'] for um array, mas você quer mostrar apenas o primeiro erro como geral
// (Menos ideal se você já mostra erros por campo, mas é uma opção)
/*
else if (isset($this->data['errors']) && is_array($this->data['errors']) && !empty($this->data['errors'])) {
    // Pega a primeira mensagem de erro do array como um erro geral, se não for um erro de campo já exibido
    $firstError = reset($this->data['errors']);
    // Verifica se não é um erro já sendo tratado (pode ser complexo de generalizar)
    // Esta parte é opcional e pode ser removida se você controlar os erros gerais de outra forma
    if (is_string($firstError)) {
         echo "<div class='alert alert-danger' role='alert'>$firstError</div>";
    }
}
*/

?>