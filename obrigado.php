<?php
include 'includes/header.php';

$pedido_id = $_SESSION['ultimo_pedido_id'] ?? null;
unset($_SESSION['ultimo_pedido_id']);

if (!$pedido_id) {
    header('Location: index.php');
    exit;
}
?>

<div class="py-5 text-center">
    <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
    <h2>Obrigado pelo seu pedido!</h2>
    <p class="lead">Seu pedido número <strong>#<?= htmlspecialchars($pedido_id) ?></strong> foi recebido com sucesso.</p>
    <p>Em breve você receberá atualizações sobre o status.</p>
    <hr>
    <p class="mb-0">
        <a href="index.php" class="btn btn-primary">Continuar Comprando</a>
    </p>
</div>


<?php include 'includes/footer.php'; ?>