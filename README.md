## Requisitos

* PHP 8.3 ou superior;
* Apache;
* rewrite_module ativo no Apache;
* MySQL 8.0 ou superior;
* Composer;

## Como rodar o projeto baixado

Duplicar o arquivo ".env.example" e renomear para ".env".<br>
Alterar no arquivo .env as credenciais do banco de dados.<br>
Alterar no arquivo .env o endereço da aplicação na variável de ambiente URL_ADM.<br>

Instalar as dependências.
```
composer install
```

Executar as migrations.
```
vendor/bin/phinx migrate -c database/phinx.php
```

Executar as seed.
```
vendor/bin/phinx seed:run -c database/phinx.php
```

## Sequencia para criar o projeto
Criar o arquivo composer.json com a instrução básica.
```
composer init
```

Instalar a dependencias Monolog, biblioteca PHP que permite criar arquivo de log.
```
composer require monolog/monolog
```

Instalar a biblioteca gerenciar variáveis de ambiente.
```
composer require vlucas/phpdotenv
```

Instalar a biblioteca para criar/executar migration e seed.
```
composer require robmorgan/phinx
```

Criar o arquivo "phinx.php" com as configurações e alterar as mesmas.
```
vendor/bin/phinx init -f php
```

Testar as configurações.
```
vendor/bin/phinx test
```

Criar o diretório database.
```
mkdir database/
```

Criar o diretório para migrations.
```
mkdir database/migrations/
```

Criar a migrations.
```
vendor/bin/phinx create AdmsUsers -c database/phinx.php
```

Executar as migrations.
```
vendor/bin/phinx migrate -c database/phinx.php
```

Executar o rollback na última migration - reverter as alterações realizadas.
```
vendor\bin\phinx rollback -c database/phinx.php
```

Criar o diretório para seed.
```
mkdir database/seeds/
```

Criar seed.
```
vendor/bin/phinx seed:create AddAdmsUsers -c database/phinx.php
```

Executar as seed.
```
vendor/bin/phinx seed:run -c database/phinx.php
```

Instalar a biblioteca para validar o formulário.
```
composer require rakit/validation
```
Instalar a biblioteca para enviar e-mails.
```
composer require phpmailer/phpmailer
```


## Como usar o GitHub
Baixar os arquivos do Git.
```
git clone --branch <branch_name> <repository_url> .
```

Definir as configurações do usuário.
```
git config --local user.name Cesar
```
```
git config --local user.email cesar@meuemail.com.br
```

Verificar a branch.
```
git branch 
```

Baixar as atualizações.
```
git pull
```

Adicionar todos os arquivos modificados no staging area - área de preparação.
```
git add .
```

commit representa um conjunto de alterações em um ponto específico da história do seu projeto, registra apenas as alterações adicionadas ao índice de preparação.
O comando -m permite que insira a mensagem de commit diretamente na linha de comando.
```
git commit -m "Descrição do commit"
```

Enviar os commits locais, para um repositório remoto.
```
git push <remote> <branch>
git push origin dev-master
```

## Lista de erros
001 - DbConnection.php - Erro de conexão com o banco de dados
002 - LoadPageAdm.php - Não encontrou a página
003 - LoadPageAdm.php - Não encontrou a controller
004 - LoadPageAdm.php - Não encontrou o método
005 - LoadViewService.php - Não encontrou a view

