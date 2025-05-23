<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

$action = $_GET['action'] ?? $_POST['action'] ?? null;
$id = $_GET['id'] ?? $_POST['id'] ?? null;
$quantidade = $_POST['quantidade'] ?? 1;

if (!isset($_SESSION['carrinho'])) {
    $_SESSION['carrinho'] = [];
}
if (!isset($_SESSION['carrinho_meta'])) {
    $_SESSION['carrinho_meta'] = [
        'subtotal' => 0.00,
        'frete' => 0.00,
        'cupom_id' => null,
        'cupom_codigo' => null,
        'desconto_cupom' => 0.00,
        'total' => 0.00
    ];
}


switch ($action) {
    case 'add':
        if ($id) {
            $produto = getProdutoById($pdo, $id);
            if ($produto) {
                if ($produto['estoque_quantidade'] > 0) {
                    if (isset($_SESSION['carrinho'][$id])) {
                        if ($_SESSION['carrinho'][$id]['quantidade'] < $produto['estoque_quantidade']) {
                            $_SESSION['carrinho'][$id]['quantidade']++;
                            $_SESSION['mensagem'] = "Quantidade atualizada no carrinho!";
                            $_SESSION['tipo_mensagem'] = "info";
                        } else {
                            $_SESSION['mensagem'] = "Quantidade máxima em estoque atingida para este produto.";
                            $_SESSION['tipo_mensagem'] = "warning";
                        }
                    } else {
                        $_SESSION['carrinho'][$id] = [
                            'id' => $produto['id'],
                            'nome' => $produto['nome'],
                            'preco' => $produto['preco'],
                            'quantidade' => 1,
                            'variacoes' => $produto['variacoes_descricao']
                        ];
                        $_SESSION['mensagem'] = "Produto adicionado ao carrinho!";
                        $_SESSION['tipo_mensagem'] = "success";
                    }
                } else {
                    $_SESSION['mensagem'] = "Produto fora de estoque.";
                    $_SESSION['tipo_mensagem'] = "danger";
                }
            } else {
                $_SESSION['mensagem'] = "Produto não encontrado.";
                $_SESSION['tipo_mensagem'] = "danger";
            }
        }
        recalcularCarrinho($pdo);
        header('Location: ../carrinho.php');
        exit;

    case 'update_qty':
        if ($id && isset($_SESSION['carrinho'][$id]) && isset($_POST['quantidade'])) {
            $quantidade = intval($_POST['quantidade']);
            $produto = getProdutoById($pdo, $id);

            if ($produto) {
                if ($quantidade > 0 && $quantidade <= $produto['estoque_quantidade']) {
                    $_SESSION['carrinho'][$id]['quantidade'] = $quantidade;
                    $_SESSION['mensagem'] = "Quantidade atualizada.";
                    $_SESSION['tipo_mensagem'] = "success";
                } elseif ($quantidade <= 0) {
                    unset($_SESSION['carrinho'][$id]);
                    $_SESSION['mensagem'] = "Produto removido do carrinho.";
                    $_SESSION['tipo_mensagem'] = "info";
                } else {
                    $_SESSION['mensagem'] = "Quantidade solicitada excede o estoque disponível ({$produto['estoque_quantidade']}).";
                    $_SESSION['tipo_mensagem'] = "warning";
                    $_SESSION['carrinho'][$id]['quantidade'] = $produto['estoque_quantidade'];
                }
            } else {
                 unset($_SESSION['carrinho'][$id]);
                 $_SESSION['mensagem'] = "Produto não encontrado e removido do carrinho.";
                 $_SESSION['tipo_mensagem'] = "warning";
            }
        }
        recalcularCarrinho($pdo);
        header('Location: ../carrinho.php');
        exit;

    case 'remove':
        if ($id && isset($_SESSION['carrinho'][$id])) {
            unset($_SESSION['carrinho'][$id]);
            $_SESSION['mensagem'] = "Produto removido do carrinho.";
            $_SESSION['tipo_mensagem'] = "info";
        }
        recalcularCarrinho($pdo);
        header('Location: ../carrinho.php');
        exit;

    case 'apply_coupon':
        $codigo_cupom = trim($_POST['codigo_cupom'] ?? '');
        if (!empty($codigo_cupom)) {
            $stmt = $pdo->prepare("SELECT * FROM cupons WHERE codigo = ? AND ativo = TRUE AND (data_validade IS NULL OR data_validade >= CURDATE()) AND (usos_maximos IS NULL OR usos_atuais < usos_maximos)");
            $stmt->execute([$codigo_cupom]);
            $cupom = $stmt->fetch();

            if ($cupom) {
                $_SESSION['carrinho_meta']['cupom_id'] = $cupom['id'];
                $_SESSION['carrinho_meta']['cupom_codigo'] = $cupom['codigo'];
                // O desconto será calculado em recalcularCarrinho
                $_SESSION['mensagem'] = "Cupom '".htmlspecialchars($cupom['codigo'])."' aplicado!";
                $_SESSION['tipo_mensagem'] = "success";
            } else {
                $_SESSION['carrinho_meta']['cupom_id'] = null;
                $_SESSION['carrinho_meta']['cupom_codigo'] = null;
                $_SESSION['carrinho_meta']['desconto_cupom'] = 0.00;
                $_SESSION['mensagem'] = "Cupom inválido ou expirado.";
                $_SESSION['tipo_mensagem'] = "danger";
            }
        } else {
            $_SESSION['carrinho_meta']['cupom_id'] = null;
            $_SESSION['carrinho_meta']['cupom_codigo'] = null;
            $_SESSION['carrinho_meta']['desconto_cupom'] = 0.00;
            $_SESSION['mensagem'] = "Cupom removido.";
            $_SESSION['tipo_mensagem'] = "info";
        }
        recalcularCarrinho($pdo);
        header('Location: ../carrinho.php');
        exit;

    case 'clear':
        $_SESSION['carrinho'] = [];
        $_SESSION['carrinho_meta'] = [
            'subtotal' => 0.00,
            'frete' => 0.00,
            'cupom_id' => null,
            'cupom_codigo' => null,
            'desconto_cupom' => 0.00,
            'total' => 0.00
        ];
        $_SESSION['mensagem'] = "Carrinho esvaziado.";
        $_SESSION['tipo_mensagem'] = "info";
        header('Location: ../carrinho.php');
        exit;
}

header('Location: ../carrinho.php');
exit;
?>