<?php use App\Core\Csrf; ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= limpaTexto($titulo ?? '') ?> | <?= limpaTexto($config['app_nome']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-md navbar-dark bg-cupcake mb-4">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">🧁 <?= limpaTexto($config['app_nome']) ?></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menu" aria-label="Abrir menu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="menu">
            <ul class="navbar-nav ms-auto align-items-md-center">
                <li class="nav-item"><a class="nav-link" href="index.php">Vitrine</a></li>
                <li class="nav-item"><a class="nav-link" href="index.php?r=pedido/acompanhar">Acompanhar pedido</a></li>
                <li class="nav-item">
                    <a class="nav-link" href="index.php?r=carrinho/index">
                        Carrinho <span class="badge bg-light text-dark"><?= (int) $qtdCarrinho ?></span>
                    </a>
                </li>
                <?php if (!empty($_SESSION['admin'])): ?>
                    <li class="nav-item"><a class="nav-link" href="index.php?r=admin/pedidos">Pedidos</a></li>
                    <li class="nav-item"><a class="nav-link" href="index.php?r=admin/cupcakes">Cupcakes</a></li>
                    <li class="nav-item">
                        <form method="post" action="index.php?r=admin/logout" class="d-inline">
                            <?= Csrf::campo() ?>
                            <button class="btn btn-sm btn-outline-light ms-md-2">Sair</button>
                        </form>
                    </li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link small opacity-75" href="index.php?r=admin/login">Lojista</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<main class="container mb-5">
    <?php if ($flash): ?>
        <div class="alert alert-<?= limpaTexto($flash['tipo']) ?> alert-dismissible fade show" role="alert">
            <?= limpaTexto($flash['mensagem']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
        </div>
    <?php endif; ?>
    <?= $conteudo ?>
</main>

<footer class="text-center text-muted small py-4 border-top">
    <?= limpaTexto($config['app_nome']) ?> — Projeto Integrador Transdisciplinar em Engenharia de Software II
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/app.js"></script>
</body>
</html>


