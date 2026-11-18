<?php use App\Core\Csrf; ?>
<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <h1 class="h3 mb-3">Acompanhar pedido</h1>
        <?php if ($erro): ?><div class="alert alert-danger"><?= limpaTexto($erro) ?></div><?php endif; ?>
        <form method="post" action="index.php?r=pedido/acompanhar" class="card card-body shadow-sm">
            <?= Csrf::campo() ?>
            <div class="mb-3">
                <label for="numero" class="form-label">Número do pedido</label>
                <input type="number" min="1" id="numero" name="numero" required class="form-control" value="<?= limpaTexto($dados['numero']) ?>">
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">E-mail usado na compra</label>
                <input type="email" id="email" name="email" required class="form-control" value="<?= limpaTexto($dados['email']) ?>">
            </div>
            <button class="btn btn-primary">Consultar</button>
        </form>
    </div>
</div>


