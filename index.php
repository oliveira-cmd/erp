<?php include 'includes/header.php'; ?>

<div class="row mb-3">
    <div class="col">
        <h2>Gerenciar Produtos</h2>
    </div>
    <div class="col text-end">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">
            <i class="bi bi-plus-circle"></i> Adicionar Produto
        </button>
    </div>
</div>

<!-- Tabela de Produtos -->
<table class="table table-striped table-hover">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nome</th>
            <th>Preço</th>
            <th>Variações (Desc.)</th>
            <th>Estoque</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $stmt = $pdo->query("SELECT p.id, p.nome, p.preco, p.variacoes_descricao, e.quantidade as estoque_quantidade
                             FROM produtos p
                             LEFT JOIN estoque e ON p.id = e.produto_id
                             ORDER BY p.id DESC");
        while ($produto = $stmt->fetch()) :
        ?>
            <tr>
                <td><?= htmlspecialchars($produto['id']) ?></td>
                <td><?= htmlspecialchars($produto['nome']) ?></td>
                <td><?= formatarMoeda($produto['preco']) ?></td>
                <td><?= htmlspecialchars($produto['variacoes_descricao'] ?: 'N/A') ?></td>
                <td><?= htmlspecialchars($produto['estoque_quantidade']) ?></td>
                <td>
                    <button type="button" class="btn btn-sm btn-warning"
                            data-bs-toggle="modal" data-bs-target="#editProductModal"
                            data-bs-id="<?= $produto['id'] ?>"
                            data-bs-nome="<?= htmlspecialchars($produto['nome']) ?>"
                            data-bs-preco="<?= $produto['preco'] ?>"
                            data-bs-variacoes="<?= htmlspecialchars($produto['variacoes_descricao']) ?>"
                            data-bs-estoque="<?= $produto['estoque_quantidade'] ?>">
                        <i class="bi bi-pencil-square"></i> Editar
                    </button>
                    <a href="actions/carrinho_action.php?action=add&id=<?= $produto['id'] ?>" class="btn btn-sm btn-success">
                        <i class="bi bi-cart-plus"></i> Comprar
                    </a>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="actions/produto_action.php" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="addProductModalLabel">Adicionar Novo Produto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="add">
                    <div class="mb-3">
                        <label for="nome" class="form-label">Nome do Produto</label>
                        <input type="text" class="form-control" id="nome" name="nome" required>
                    </div>
                    <div class="mb-3">
                        <label for="preco" class="form-label">Preço (R$)</label>
                        <input type="number" step="0.01" class="form-control" id="preco" name="preco" required>
                    </div>
                    <div class="mb-3">
                        <label for="variacoes_descricao" class="form-label">Variações (Descrição)</label>
                        <textarea class="form-control" id="variacoes_descricao" name="variacoes_descricao" rows="2" placeholder="Ex: Cor: Azul/Vermelho, Tamanho: P/M/G"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="estoque_quantidade" class="form-label">Estoque Inicial</label>
                        <input type="number" class="form-control" id="estoque_quantidade" name="estoque_quantidade" required min="0">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                    <button type="submit" class="btn btn-primary">Salvar Produto</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editProductModal" tabindex="-1" aria-labelledby="editProductModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="actions/produto_action.php" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="editProductModalLabel">Editar Produto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="produto_id" id="edit_produto_id">
                    <div class="mb-3">
                        <label for="edit_nome" class="form-label">Nome do Produto</label>
                        <input type="text" class="form-control" id="edit_nome" name="nome" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_preco" class="form-label">Preço (R$)</label>
                        <input type="number" step="0.01" class="form-control" id="edit_preco" name="preco" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_variacoes_descricao" class="form-label">Variações (Descrição)</label>
                        <textarea class="form-control" id="edit_variacoes_descricao" name="variacoes_descricao" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="edit_estoque_quantidade" class="form-label">Estoque</label>
                        <input type="number" class="form-control" id="edit_estoque_quantidade" name="estoque_quantidade" required min="0">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                    <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>