<?php

// Verifica se há uma página final disponível e se não é a primeira página
if (($this->data['pagination']['last_page'] ?? false) && ($this->data['pagination']['last_page'] != 1)) {

?>
    <nav aria-label="Page navigation example">
        <ul class="pagination justify-content-center pagination-sm">

            <?php
            // Se a página atual for maior que 1, exibe links para a primeira página e a página anterior
            if ($this->data['pagination']['current_page'] > 1) {
                // Gera um link para a primeira página
                echo "<li class='page-item'><a href='" . $_ENV['URL_ADM'] . ($this->data['pagination']['url_controller'] ?? '') . "/1' class='page-link'>Primeira</a></li>";

                // Calcula o número da página anterior
                $beforePage = $this->data['pagination']['current_page'] - 1;

                // Gera um link para a página anterior
                echo "<li class='page-item'><a href='" . $_ENV['URL_ADM'] . ($this->data['pagination']['url_controller'] ?? '') . "/" . $beforePage . "' class='page-link'>$beforePage</a></li>";
            }

            // Exibe a página atual.
            $currentPage = ($this->data['pagination']['current_page'] ?? '');
            echo "<li class='page-item active' aria-current='page'><span class='page-link'>$currentPage</span></li>";

            // Se a página atual for menor que a página final, exibe links para a próxima página e a última página
            if ($this->data['pagination']['current_page'] < $this->data['pagination']['last_page']) {
                // Calcula o número da próxima página
                $afterPage = $this->data['pagination']['current_page'] + 1;

                // Gera um link para a próxima página
                echo "<li class='page-item'><a href='" . $_ENV['URL_ADM'] . ($this->data['pagination']['url_controller'] ?? '') . "/" . $afterPage . "' class='page-link'>$afterPage</a></li>";

                // Gera um link para a última página
                echo "<li class='page-item'><a href='" . $_ENV['URL_ADM'] . ($this->data['pagination']['url_controller'] ?? '') . "/" . ($this->data['pagination']['last_page'] ?? '') . "' class='page-link'>Última</a></li>";
            }
            ?>

        </ul>
    </nav>

<?php } ?>