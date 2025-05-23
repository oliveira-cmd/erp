<?php
    require_once __DIR__ . '/db.php';
    require_once __DIR__ . '/functions.php';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mini ERP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body { padding-top: 70px; }
        .container { max-width: 1140px; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">Mini ERP</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Produtos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="carrinho.php">
                            Carrinho
                            <?php
                                $totalItensCarrinho = 0;
                                if (isset($_SESSION['carrinho']) && !empty($_SESSION['carrinho'])) {
                                    foreach ($_SESSION['carrinho'] as $item) {
                                        $totalItensCarrinho += $item['quantidade'];
                                    }
                                }
                                if ($totalItensCarrinho > 0) {
                                    echo '<span class="badge bg-danger ms-1">' . $totalItensCarrinho . '</span>';
                                }
                            ?>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <?php exibirMensagem();?>