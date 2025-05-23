<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'finalizar_pedido') {
    if (empty($_SESSION['carrinho'])) {
        $_SESSION['mensagem'] = "Seu carrinho está vazio.";
        $_SESSION['tipo_mensagem'] = "warning";
        header('Location: ../carrinho.php');
        exit;
    }

    recalcularCarrinho($pdo);

    $carrinho = $_SESSION['carrinho'];
    $meta = $_SESSION['carrinho_meta'];

    $cep_final = trim($_POST['cep_final'] ?? '');
    $endereco_final = trim($_POST['endereco_final'] ?? '');

    if (empty($cep_final) || empty($endereco_final)) {
        $_SESSION['mensagem'] = "Por favor, informe o CEP e o endereço para continuar."; // Mensagem mais clara
        $_SESSION['tipo_mensagem'] = "warning";
        // Salvar o que foi digitado para não perder
        $_SESSION['pedido_cep'] = $_POST['cep_final'] ?? ''; // Usar o que veio do POST
        $_SESSION['pedido_endereco'] = $_POST['endereco_final'] ?? '';
        header('Location: ../carrinho.php');
        exit;
    }
    unset($_SESSION['pedido_cep']);
    unset($_SESSION['pedido_endereco']);


    try {
        $pdo->beginTransaction();

        foreach ($carrinho as $produto_id_carrinho => $item_carrinho) {
            $stmt_check_estoque = $pdo->prepare("SELECT quantidade FROM estoque WHERE produto_id = ?");
            $stmt_check_estoque->execute([$produto_id_carrinho]);
            $estoque_atual = $stmt_check_estoque->fetchColumn();

            if ($estoque_atual === false || $estoque_atual < $item_carrinho['quantidade']) {
                $pdo->rollBack();
                $_SESSION['mensagem'] = "Desculpe, o produto '".htmlspecialchars($item_carrinho['nome'])."' não tem estoque suficiente (Disponível: $estoque_atual). Seu carrinho foi atualizado.";
                $_SESSION['tipo_mensagem'] = "danger";
                if ($estoque_atual > 0) {
                     $_SESSION['carrinho'][$produto_id_carrinho]['quantidade'] = $estoque_atual;
                } else {
                    unset($_SESSION['carrinho'][$produto_id_carrinho]);
                }
                recalcularCarrinho($pdo);
                header('Location: ../carrinho.php');
                exit;
            }
        }


        $stmt_pedido = $pdo->prepare(
            "INSERT INTO pedidos (subtotal, valor_frete, cupom_id, valor_desconto_cupom, valor_total, cep, endereco_entrega, status_pedido)
             VALUES (?, ?, ?, ?, ?, ?, ?, 'Pendente')"
        );
        $stmt_pedido->execute([
            $meta['subtotal'],
            $meta['frete'],
            $meta['cupom_id'],
            $meta['desconto_cupom'],
            $meta['total'],
            $cep_final,
            $endereco_final
        ]);
        $pedido_id = $pdo->lastInsertId();

        foreach ($carrinho as $produto_id_item => $item) {
            $stmt_item = $pdo->prepare(
                "INSERT INTO pedido_itens (pedido_id, produto_id, quantidade, preco_unitario)
                 VALUES (?, ?, ?, ?)"
            );
            $stmt_item->execute([$pedido_id, $item['id'], $item['quantidade'], $item['preco']]);

            $stmt_update_estoque = $pdo->prepare(
                "UPDATE estoque SET quantidade = quantidade - ? WHERE produto_id = ?"
            );
            $stmt_update_estoque->execute([$item['quantidade'], $item['id']]);
        }

        if ($meta['cupom_id']) {
            $stmt_cupom_uso = $pdo->prepare("UPDATE cupons SET usos_atuais = usos_atuais + 1 WHERE id = ?");
            $stmt_cupom_uso->execute([$meta['cupom_id']]);
        }

        $pdo->commit();

        $_SESSION['carrinho'] = [];
        $_SESSION['carrinho_meta'] = [
            'subtotal' => 0.00,
            'frete' => 0.00,
            'cupom_id' => null,
            'cupom_codigo' => null,
            'desconto_cupom' => 0.00,
            'total' => 0.00
        ];
        $_SESSION['ultimo_pedido_id'] = $pedido_id;

        header('Location: ../obrigado.php');
        exit;

    } catch (PDOException $e) {
        $pdo->rollBack();
        error_log("Erro ao finalizar pedido: " . $e->getMessage());
        $_SESSION['mensagem'] = "Erro ao processar seu pedido. Tente novamente. Detalhe: " . $e->getMessage();
        $_SESSION['tipo_mensagem'] = "danger";
        header('Location: ../carrinho.php');
        exit;
    }

} else {
    $_SESSION['mensagem'] = "Requisição inválida.";
    $_SESSION['tipo_mensagem'] = "danger";
    header('Location: ../carrinho.php');
    exit;
}
?>