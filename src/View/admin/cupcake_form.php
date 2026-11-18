<?php
use App\Core\Csrf;

$invalido = fn(string $n) => isset($erros[$n]) ? ' is-invalid' : '';
$msg = fn(string $n) => isset($erros[$n]) ? '<div class="invalid-feedback">' . limpaTexto($erros[$n]) . '</div>' : '';
?>
<h1 class="h3 mb-3"><?= $id ? 'Editar cupcake' : 'Novo cupcake' ?></h1>
<?php if ($erros): ?><div class="alert alert-danger">Corrija os campos destacados.</div><?php endif; ?>

<form method="post" action="index.php?r=admin/cupcakeSalvar" class="card card-body shadow-sm" style="max-width:640px" novalidate>
    <?= Csrf::campo() ?>
    <?php if ($id): ?><input type="hidden" name="id" value="<?= (int) $id ?>"><?php endif; ?>
    <div class="mb-3">
        <label for="nome" class="form-label">Nome *</label>
        <input type="text" id="nome" name="nome" maxlength="100" required class="form-control<?= $invalido('nome') ?>" value="<?= limpaTexto($cupcake['nome']) ?>">
        <?= $msg('nome') ?>
    </div>
    <div class="mb-3">
        <label for="descricao" class="form-label">Descrição</label>
        <textarea id="descricao" name="descricao" maxlength="500" rows="3" class="form-control<?= $invalido('descricao') ?>"><?= limpaTexto($cupcake['descricao']) ?></textarea>
        <?= $msg('descricao') ?>
    </div>
    <div class="row">
        <div class="col-sm-6 mb-3">
            <label for="preco" class="form-label">Preço (R$) *</label>
            <input type="text" id="preco" name="preco" inputmode="decimal" required placeholder="9,50" class="form-control<?= $invalido('preco') ?>" value="<?= limpaTexto($cupcake['preco']) ?>">
            <?= $msg('preco') ?>
        </div>
        <div class="col-sm-6 mb-3">
            <label for="estoque" class="form-label">Estoque *</label>
            <input type="number" id="estoque" name="estoque" min="0" max="10000" required class="form-control<?= $invalido('estoque') ?>" value="<?= limpaTexto($cupcake['estoque']) ?>">
            <?= $msg('estoque') ?>
        </div>
    </div>
    <div class="form-check mb-3">
        <input type="checkbox" class="form-check-input" id="ativo" name="ativo" value="1" <?= !empty($cupcake['ativo']) ? 'checked' : '' ?>>
        <label for="ativo" class="form-check-label">Exibir na vitrine</label>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-primary">Salvar</button>
        <a href="index.php?r=admin/cupcakes" class="btn btn-outline-secondary">Cancelar</a>
    </div>
</form>


