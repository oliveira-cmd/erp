# Mini ERP para Controle de Pedidos, Produtos, Cupons e Estoque (PHP Puro)

Este é um sistema simples de Mini ERP desenvolvido em PHP puro com Bootstrap e MySQL para o banco de dados. Ele permite gerenciar produtos, estoque, cupons e processar pedidos básicos.

## Pré-requisitos

Antes de começar, você precisará ter o seguinte software instalado em sua máquina local:

1.  **Servidor Web com PHP:**
    *   **XAMPP:** (Windows, Linux, macOS) - Uma das opções mais populares, inclui Apache, MariaDB (compatível com MySQL) e PHP. [Download XAMPP](https://www.apachefriends.org/index.html)
    *   **WAMP:** (Windows) - Similar ao XAMPP, específico para Windows. [Download WampServer](https://www.wampserver.com/en/)
    *   **MAMP:** (macOS, Windows) - Outra alternativa popular. [Download MAMP](https://www.mamp.info/en/downloads/)
    *   Ou qualquer outra configuração de servidor que inclua Apache/Nginx, PHP (versão 7.4 ou superior recomendada) e MySQL/MariaDB.

2.  **Sistema de Gerenciamento de Banco de Dados (SGBD):**
    *   **MySQL** ou **MariaDB:** Geralmente vêm incluídos com XAMPP, WAMP ou MAMP.
    *   **phpMyAdmin** (ou uma ferramenta similar como DBeaver, MySQL Workbench) para gerenciar o banco de dados. phpMyAdmin também vem com XAMPP/WAMP/MAMP.

3.  **Navegador Web:**
    *   Google Chrome, Firefox, Edge, Safari, etc.

## Passo a Passo para Instalação e Execução

### 1. Baixar ou Clonar o Projeto

*   **Se você recebeu os arquivos como um ZIP:**
    *   Extraia o conteúdo do arquivo ZIP para uma pasta em seu computador.
*   **Se o projeto estiver em um repositório Git (ex: GitHub):**
    *   Abra seu terminal ou Git Bash.
    *   Navegue até o diretório onde deseja salvar o projeto.
    *   Clone o repositório:
        ```bash
        git clone https://github.com/oliveira-cmd/erp
        cd erp
### 2. Configurar o Servidor Web

1.  **Mova a pasta do projeto para o diretório raiz do seu servidor web.**
    *   **XAMPP:** Mova a pasta do projeto (ex: `mini_erp`) para `C:\xampp\htdocs\` (Windows) ou `/Applications/XAMPP/htdocs/` (macOS).
    *   **WAMP:** Mova para `C:\wamp64\www\` (ou similar).
    *   **MAMP:** Mova para `/Applications/MAMP/htdocs/`.

2.  **Inicie os serviços do seu servidor web.**
    *   Abra o painel de controle do XAMPP/WAMP/MAMP.
    *   Inicie os módulos **Apache** e **MySQL**.

### 3. Configurar o Banco de Dados

1.  **Acesse o phpMyAdmin** (ou sua ferramenta de SGBD preferida).
    *   Normalmente, você pode acessá-lo via `http://localhost/phpmyadmin` no seu navegador.

2.  **Crie um novo banco de dados:**
    *   Clique em "Novo" (ou "New") na barra lateral ou na aba "Bancos de dados" (ou "Databases").
    *   Nome do banco de dados: `mini_erp_db`
    *   Agrupamento (Collation): `utf8mb4_unicode_ci` (ou deixe o padrão, se compatível).
    *   Clique em "Criar" (ou "Create").

3.  **Importe o Schema SQL:**
    *   Selecione o banco de dados `mini_erp_db` que você acabou de criar.
    *   Clique na aba "Importar" (ou "Import").
    *   Clique em "Escolher arquivo" (ou "Browse") e navegue até a pasta do projeto.
    *   Selecione o arquivo `sql/schema.sql`.
    *   Deixe as outras opções como padrão e clique em "Executar" (ou "Go" ou "Import").
    *   Isso criará as tabelas necessárias (`produtos`, `estoque`, `cupons`, `pedidos`, `pedido_itens`).

4.  **Verifique as Tabelas:**
    *   Após a importação, você deve ver as 5 tabelas listadas sob o banco de dados `mini_erp_db`.

### 4. Configurar a Conexão com o Banco de Dados no PHP

1.  **Abra o arquivo de configuração do banco de dados:**
    *   Navegue até a pasta do projeto e abra o arquivo `includes/db.php` em um editor de texto/código (VS Code, Sublime Text, Notepad++, etc.).

2.  **Ajuste as credenciais do banco de dados:**
    ```php
        <?php
            $host = 'localhost'; // ou 127.0.0.1
            $db   = 'mini_erp_db'; // Nome do banco de dados criado
            $user = 'root';     // Seu usuário do MySQL (padrão do XAMPP/WAMP/MAMP é 'root')
            $pass = '';         // Sua senha do MySQL (padrão do XAMPP/WAMP/MAMP é vazia)
            $charset = 'utf8mb4';
        ?>
    ```
    *   Certifique-se de que `$user` e `$pass` correspondem ao usuário e senha do seu MySQL local. Se você não configurou uma senha para o usuário `root` no XAMPP/WAMP/MAMP, deixar `$pass = '';` geralmente funciona.

3.  **Salve o arquivo `db.php`**.

### 5. Acessar o Mini ERP no Navegador

1.  Abra seu navegador web.
2.  Digite o seguinte URL na barra de endereços:
    *   `http://localhost/nome_da_pasta_do_projeto/`
    *   Por exemplo, se você nomeou a pasta como `mini_erp`, acesse: `http://localhost/mini_erp/`

    Você deverá ver a tela principal do Mini ERP, listando os produtos (inicialmente vazia) e a opção de adicionar novos produtos.

## Funcionalidades Principais para Testar

*   **Cadastro de Produtos:**
    *   Clique em "Adicionar Produto".
    *   Preencha Nome, Preço, Variações (opcional) e Estoque.
    *   Clique em "Salvar Produto".
*   **Edição de Produtos:**
    *   Clique no botão "Editar" (ícone de lápis) ao lado de um produto.
    *   Modifique os dados e salve.
*   **Adicionar ao Carrinho:**
    *   Clique no botão "Comprar" (ícone de carrinho) ao lado de um produto.
*   **Visualizar Carrinho:**
    *   Clique no link "Carrinho" na barra de navegação.
    *   Ajuste quantidades, remova itens.
*   **Aplicar Cupom:**
    *   No carrinho, digite um código de cupom válido (ex: `DESC10`, `VALOR5` - definidos em `sql/schema.sql`) e clique em "Aplicar Cupom".
*   **Verificar CEP:**
    *   No carrinho, digite um CEP válido no campo apropriado. O endereço deve ser preenchido automaticamente usando a API ViaCEP.
*   **Finalizar Pedido:**
    *   Com itens no carrinho e CEP informado, clique em "Finalizar Pedido".
    *   Você será redirecionado para uma página de agradecimento.
    *   O estoque dos produtos comprados será deduzido, e o uso do cupom (se aplicado) será incrementado.

## Solução de Problemas Comuns

*   **Erro de Conexão com o Banco de Dados:**
    *   Verifique se as credenciais em `includes/db.php` (`$host`, `$db`, `$user`, `$pass`) estão corretas.
    *   Confirme se o serviço MySQL está em execução.
    *   Verifique se o banco de dados `mini_erp_db` e as tabelas foram criados corretamente.
*   **"Cannot redeclare function..."**:
    *   Indica que uma função foi definida mais de uma vez. Verifique se você não copiou a mesma função para múltiplos arquivos ou a incluiu duas vezes.
*   **Problemas com JavaScript (ex: CEP não funcionando):**
    *   Abra o console do desenvolvedor do navegador (geralmente F12) e verifique a aba "Console" por erros de JavaScript.

---

Aproveite o Mini ERP! Se encontrar problemas, tente revisar os passos e verificar as mensagens de erro.