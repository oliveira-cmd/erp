<?php
include 'includes/header.php';

if (function_exists('recalcularCarrinho')) { 
    recalcularCarrinho($pdo);
} else {
    $subtotal = 0;
    if (!empty($_SESSION['carrinho'])) {
        foreach ($_SESSION['carrinho'] as $item_id => $item) {
            $subtotal += $item['preco'] * $item['quantidade'];
        }
    }
    $_SESSION['carrinho_meta']['subtotal'] = $subtotal;
    $_SESSION['carrinho_meta']['frete'] = calcularFrete($subtotal);
    if(!isset($_SESSION['carrinho_meta']['desconto_cupom'])) $_SESSION['carrinho_meta']['desconto_cupom'] = 0;
    
    $total = $subtotal + $_SESSION['carrinho_meta']['frete'] - $_SESSION['carrinho_meta']['desconto_cupom'];
    $_SESSION['carrinho_meta']['total'] = ($total > 0) ? $total : 0.00;
}


$carrinho = $_SESSION['carrinho'] ?? [];
$meta = $_SESSION['carrinho_meta'] ?? [
    'subtotal' => 0.00,
    'frete' => 0.00,
    'cupom_id' => null,
    'cupom_codigo' => null,
    'desconto_cupom' => 0.00,
    'total' => 0.00
];
?>

<h2>Meu Carrinho</h2>

<?php if (empty($carrinho)) : ?>
    <div class="alert alert-info">Seu carrinho está vazio. <a href="index.php">Continue comprando!</a></div>
<?php else : ?>
    <table class="table">
        <thead>
            <tr>
                <th>Produto</th>
                <th>Preço Unit.</th>
                <th>Quantidade</th>
                <th>Subtotal</th>
                <th>Ação</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($carrinho as $id => $item) : ?>
                <tr>
                    <td><?= htmlspecialchars($item['nome']) ?>
                        <?php if (!empty($item['variacoes'])): ?>
                            <small class="d-block text-muted"><?= htmlspecialchars($item['variacoes']) ?></small>
                        <?php endif; ?>
                    </td>
                    <td><?= formatarMoeda($item['preco']) ?></td>
                    <td>
                        <form action="actions/carrinho_action.php" method="POST" class="d-inline-flex align-items-center">
                            <input type="hidden" name="action" value="update_qty">
                            <input type="hidden" name="id" value="<?= $id ?>">
                            <input type="number" name="quantidade" value="<?= $item['quantidade'] ?>" min="1" class="form-control form-control-sm" style="width: 70px;">
                            <button type="submit" class="btn btn-sm btn-outline-primary ms-2"><i class="bi bi-arrow-repeat"></i></button>
                        </form>
                    </td>
                    <td><?= formatarMoeda($item['preco'] * $item['quantidade']) ?></td>
                    <td>
                        <a href="actions/carrinho_action.php?action=remove&id=<?= $id ?>" class="btn btn-sm btn-danger" title="Remover item">
                            <i class="bi bi-trash"></i>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="row mt-4">
        <div class="col-md-6">
            <h4>Calcular Frete e Cupom</h4>
            <form action="actions/carrinho_action.php" method="POST" class="mb-3">
                <input type="hidden" name="action" value="apply_coupon">
                <div class="input-group">
                    <input type="text" name="codigo_cupom" class="form-control" placeholder="Código do Cupom" value="<?= htmlspecialchars($meta['cupom_codigo'] ?? '') ?>">
                    <button class="btn btn-outline-secondary" type="submit">Aplicar Cupom</button>
                </div>
            </form>

            <div class="mb-3">
                <label for="cep" class="form-label">CEP para Entrega:</label>
                <div class="input-group">
                    <input type="text" class="form-control" id="cep" name="cep" placeholder="00000-000" value="<?= htmlspecialchars($_SESSION['pedido_cep'] ?? '') ?>">
                    <span class="input-group-text" id="basic-addon1"><i class="bi bi-geo-alt"></i></span>
                </div>
                <small class="form-text text-muted">Apenas para cálculo do frete e informação no pedido.</small>
            </div>
             <div class="mb-3">
                <label for="endereco_completo" class="form-label">Endereço (auto-preenchido via CEP):</label>
                <input type="text" class="form-control" id="endereco_completo" name="endereco_completo" readonly value="<?= htmlspecialchars($_SESSION['pedido_endereco'] ?? '') ?>">
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Resumo do Pedido</h5>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Subtotal
                            <span><?= formatarMoeda($meta['subtotal']) ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Frete
                            <span><?= formatarMoeda($meta['frete']) ?></span>
                        </li>
                        <?php if ($meta['desconto_cupom'] > 0) : ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center text-success">
                            Desconto Cupom (<?= htmlspecialchars($meta['cupom_codigo'] ?? '') ?>)
                            <span>- <?= formatarMoeda($meta['desconto_cupom']) ?></span>
                        </li>
                        <?php endif; ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center fw-bold">
                            Total
                            <h3><?= formatarMoeda($meta['total']) ?></h3>
                        </li>
                    </ul>
                    <form action="actions/pedido_action.php" method="POST" class="mt-3">
                        <input type="hidden" name="action" value="finalizar_pedido">
                        <input type="hidden" name="cep_final" id="cep_final_hidden">
                        <input type="hidden" name="endereco_final" id="endereco_final_hidden">
                        <button type="submit" class="btn btn-primary w-100" onclick="document.getElementById('cep_final_hidden').value = document.getElementById('cep').value; document.getElementById('endereco_final_hidden').value = document.getElementById('endereco_completo').value;">
                           <i class="bi bi-check-circle"></i> Finalizar Pedido
                        </button>
                    </form>
                </div>
            </div>
            <div class="mt-3">
                 <a href="actions/carrinho_action.php?action=clear" class="btn btn-sm btn-outline-danger">
                    <i class="bi bi-cart-x"></i> Esvaziar Carrinho
                </a>
            </div>
        </div>
    </div>

<?php endif; ?>

<?php include 'includes/footer.php'; ?>