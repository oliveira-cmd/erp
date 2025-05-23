// mini_erp/actions/produto_action.php
<?php
require_once '../includes/db.php'; // Acessa db.php uma pasta acima

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? null;
    $nome = trim($_POST['nome'] ?? '');
    $preco = filter_var($_POST['preco'] ?? 0, FILTER_VALIDATE_FLOAT);
    $variacoes_descricao = trim($_POST['variacoes_descricao'] ?? '');
    $estoque_quantidade = filter_var($_POST['estoque_quantidade'] ?? 0, FILTER_VALIDATE_INT);
    $produto_id = filter_var($_POST['produto_id'] ?? null, FILTER_VALIDATE_INT);

    // Validações básicas
    if (empty($nome) || $preco === false || $preco <= 0 || $estoque_quantidade === false || $estoque_quantidade < 0) {
        $_SESSION['mensagem'] = "Dados inválidos. Verifique nome, preço (maior que zero) e estoque (maior ou igual a zero).";
        $_SESSION['tipo_mensagem'] = "danger";
        header('Location: ../index.php');
        exit;
    }


    if ($action === 'add') {
        try {
            $pdo->beginTransaction();

            // Inserir produto
            $stmt = $pdo->prepare("INSERT INTO produtos (nome, preco, variacoes_descricao) VALUES (?, ?, ?)");
            $stmt->execute([$nome, $preco, $variacoes_descricao]);
            $new_produto_id = $pdo->lastInsertId();

            // Inserir estoque
            $stmt_estoque = $pdo->prepare("INSERT INTO estoque (produto_id, quantidade) VALUES (?, ?)");
            $stmt_estoque->execute([$new_produto_id, $estoque_quantidade]);

            $pdo->commit();
            $_SESSION['mensagem'] = "Produto adicionado com sucesso!";
            $_SESSION['tipo_mensagem'] = "success";

        } catch (PDOException $e) {
            $pdo->rollBack();
            $_SESSION['mensagem'] = "Erro ao adicionar produto: " . $e->getMessage();
            $_SESSION['tipo_mensagem'] = "danger";
        }

    } elseif ($action === 'edit' && $produto_id) {
         try {
            $pdo->beginTransaction();

            // Atualizar produto
            $stmt = $pdo->prepare("UPDATE produtos SET nome = ?, preco = ?, variacoes_descricao = ? WHERE id = ?");
            $stmt->execute([$nome, $preco, $variacoes_descricao, $produto_id]);

            // Atualizar estoque (ou inserir se não existir, embora deva existir)
            $stmt_estoque_check = $pdo->prepare("SELECT id FROM estoque WHERE produto_id = ?");
            $stmt_estoque_check->execute([$produto_id]);
            if ($stmt_estoque_check->fetch()) {
                $stmt_estoque = $pdo->prepare("UPDATE estoque SET quantidade = ? WHERE produto_id = ?");
                $stmt_estoque->execute([$estoque_quantidade, $produto_id]);
            } else {
                $stmt_estoque = $pdo->prepare("INSERT INTO estoque (produto_id, quantidade) VALUES (?, ?)");
                $stmt_estoque->execute([$produto_id, $estoque_quantidade]);
            }


            $pdo->commit();
            $_SESSION['mensagem'] = "Produto atualizado com sucesso!";
            $_SESSION['tipo_mensagem'] = "success";

        } catch (PDOException $e) {
            $pdo->rollBack();
            $_SESSION['mensagem'] = "Erro ao atualizar produto: " . $e->getMessage();
            $_SESSION['tipo_mensagem'] = "danger";
        }
    } else {
        $_SESSION['mensagem'] = "Ação inválida.";
        $_SESSION['tipo_mensagem'] = "warning";
    }
} else {
    $_SESSION['mensagem'] = "Método de requisição inválido.";
    $_SESSION['tipo_mensagem'] = "danger";
}

header('Location: ../index.php');
exit;
?>