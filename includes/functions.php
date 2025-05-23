<?php
    function calcularFrete($subtotal) {
        if ($subtotal >= 52.00 && $subtotal <= 166.59) {
            return 15.00;
        } elseif ($subtotal > 200.00) {
            return 0.00;
        } else {
            return 20.00;
        }
    }

    function formatarMoeda($valor) {
        return "R$ " . number_format($valor, 2, ',', '.');
    }

    function getProdutoById($pdo, $id) {
        $stmt = $pdo->prepare("SELECT p.*, e.quantidade as estoque_quantidade FROM produtos p JOIN estoque e ON p.id = e.produto_id WHERE p.id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    function exibirMensagem() {
        if (isset($_SESSION['mensagem'])) {
            $tipo = isset($_SESSION['tipo_mensagem']) ? $_SESSION['tipo_mensagem'] : 'info';
            echo '<div class="alert alert-' . htmlspecialchars($tipo) . ' alert-dismissible fade show" role="alert">';
            echo htmlspecialchars($_SESSION['mensagem']);
            echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
            echo '</div>';
            unset($_SESSION['mensagem']);
            unset($_SESSION['tipo_mensagem']);
        }
    }

    function recalcularCarrinho($pdo) {
        $subtotal = 0;
        if (!empty($_SESSION['carrinho'])) {
            foreach ($_SESSION['carrinho'] as $item_id => $item) {
                $produtoDB = getProdutoById($pdo, $item_id);
                if ($produtoDB) {
                    $_SESSION['carrinho'][$item_id]['preco'] = $produtoDB['preco'];
                    $subtotal += $produtoDB['preco'] * $item['quantidade'];
                } else {
                    unset($_SESSION['carrinho'][$item_id]);
                }
            }
        }

        $_SESSION['carrinho_meta']['subtotal'] = $subtotal;
        $_SESSION['carrinho_meta']['frete'] = calcularFrete($subtotal);

        if ($_SESSION['carrinho_meta']['cupom_id']) {
            $stmt_cupom = $pdo->prepare("SELECT * FROM cupons WHERE id = ? AND ativo = TRUE AND (data_validade IS NULL OR data_validade >= CURDATE()) AND (usos_maximos IS NULL OR usos_atuais < usos_maximos)");
            $stmt_cupom->execute([$_SESSION['carrinho_meta']['cupom_id']]);
            $cupom = $stmt_cupom->fetch();

            if ($cupom) {
                if ($cupom['tipo_desconto'] == 'percentual') {
                    $_SESSION['carrinho_meta']['desconto_cupom'] = ($subtotal * $cupom['valor_desconto']) / 100;
                } else {
                    $_SESSION['carrinho_meta']['desconto_cupom'] = $cupom['valor_desconto'];
                }
                if ($_SESSION['carrinho_meta']['desconto_cupom'] > $subtotal) {
                    $_SESSION['carrinho_meta']['desconto_cupom'] = $subtotal;
                }
            } else {
                $_SESSION['carrinho_meta']['cupom_id'] = null;
                $_SESSION['carrinho_meta']['cupom_codigo'] = null;
                $_SESSION['carrinho_meta']['desconto_cupom'] = 0.00;
                $_SESSION['mensagem'] = "Cupom anteriormente aplicado tornou-se inválido.";
                $_SESSION['tipo_mensagem'] = "warning";
            }
        }


        $total = $subtotal + $_SESSION['carrinho_meta']['frete'] - $_SESSION['carrinho_meta']['desconto_cupom'];
        $_SESSION['carrinho_meta']['total'] = ($total > 0) ? $total : 0.00;
    }
?>