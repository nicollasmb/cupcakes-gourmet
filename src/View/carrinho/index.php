<?php use App\Core\Csrf; ?>
<h1 class="h3 mb-4">Seu carrinho</h1>

<?php if (!$linhas): ?>
    <div class="text-center py-5">
        <div style="font-size:3rem">🛒</div>
        <p class="text-muted">Seu carrinho está vazio.</p>
        <a href="index.php" class="btn btn-primary">Ver cupcakes</a>
    </div>
<?php else: ?>
    <div class="table-responsive">
        <table class="table align-middle">
            <thead><tr><th>Cupcake</th><th>Preço</th><th style="width:200px">Quantidade</th><th class="text-end">Total</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($linhas as $l): ?>
                <tr>
                    <td><?= limpaTexto($l['nome']) ?></td>
                    <td><?= formataMoeda($l['preco']) ?></td>
                    <td>
                        <form method="post" action="index.php?r=carrinho/atualizar" class="d-flex gap-2">
                            <?= Csrf::campo() ?>
                            <input type="hidden" name="cupcake_id" value="<?= (int) $l['id'] ?>">
                            <input type="number" name="quantidade" value="<?= (int) $l['quantidade'] ?>" min="0"
                                   max="<?= min(50, $l['estoque']) ?>" class="form-control form-control-sm" aria-label="Quantidade de <?= limpaTexto($l['nome']) ?>">
                            <button class="btn btn-sm btn-outline-secondary">Atualizar</button>
                        </form>
                    </td>
                    <td class="text-end"><?= formataMoeda($l['total']) ?></td>
                    <td class="text-end">
                        <form method="post" action="index.php?r=carrinho/remover">
                            <?= Csrf::campo() ?>
                            <input type="hidden" name="cupcake_id" value="<?= (int) $l['id'] ?>">
                            <button class="btn btn-sm btn-link text-danger">Remover</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr><th colspan="3" class="text-end">Subtotal</th><th class="text-end"><?= formataMoeda($subtotal) ?></th><th></th></tr>
            </tfoot>
        </table>
    </div>
    <p class="small text-muted text-end">A taxa de entrega (<?= formataMoeda((float) $config['taxa_entrega']) ?>) é somada no próximo passo, se você escolher entrega.</p>
    <div class="d-flex justify-content-between">
        <a href="index.php" class="btn btn-outline-secondary">Continuar comprando</a>
        <a href="index.php?r=pedido/checkout" class="btn btn-primary btn-lg">Finalizar pedido</a>
    </div>
<?php endif; ?>


