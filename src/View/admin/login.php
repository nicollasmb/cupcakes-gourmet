<?php use App\Core\Csrf; ?>
<div class="row justify-content-center">
    <div class="col-md-5 col-lg-4">
        <h1 class="h3 mb-3">Área do lojista</h1>
        <?php if ($erro): ?><div class="alert alert-danger"><?= limpaTexto($erro) ?></div><?php endif; ?>
        <form method="post" action="index.php?r=admin/login" class="card card-body shadow-sm">
            <?= Csrf::campo() ?>
            <label for="senha" class="form-label">Senha</label>
            <input type="password" id="senha" name="senha" required autofocus class="form-control mb-3">
            <button class="btn btn-primary">Entrar</button>
        </form>
    </div>
</div>


