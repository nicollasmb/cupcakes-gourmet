<?php use App\Core\Csrf; ?>
<div class="hero rounded-4 p-4 p-md-5 mb-4">
    <h1 class="display-6 fw-bold">Cupcakes artesanais, feitos no dia</h1>
    <p class="lead mb-0">Escolha seus sabores, finalize o pedido e receba em casa ou retire na loja.</p>
</div>

<?php if (!$cupcakes): ?>
    <div class="alert alert-info">Nenhum cupcake disponível no momento. Volte mais tarde!</div>
<?php endif; ?>

<div class="row g-4">
    <?php foreach ($cupcakes as $c): $esgotado = (int) $c['estoque'] === 0; ?>
        <div class="col-sm-6 col-lg-4">
            <div class="card h-100 shadow-sm card-cupcake">
                <div class="card-img-top emoji-cupcake" aria-hidden="true">🧁</div>
                <div class="card-body d-flex flex-column">
                    <h2 class="h5 card-title"><?= limpaTexto($c['nome']) ?></h2>
                    <p class="card-text text-muted small flex-grow-1"><?= limpaTexto($c['descricao']) ?></p>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fs-5 fw-bold preco"><?= formataMoeda((float) $c['preco']) ?></span>
                        <?php if ($esgotado): ?>
                            <span class="badge bg-secondary">Esgotado</span>
                        <?php elseif ($c['estoque'] <= 5): ?>
                            <span class="badge bg-warning text-dark">Últimas <?= (int) $c['estoque'] ?></span>
                        <?php endif; ?>
                    </div>
                    <form method="post" action="index.php?r=carrinho/adicionar" class="d-flex gap-2">
                        <?= Csrf::campo() ?>
                        <input type="hidden" name="cupcake_id" value="<?= (int) $c['id'] ?>">
                        <label class="visually-hidden" for="qtd-<?= (int) $c['id'] ?>">Quantidade</label>
                        <input type="number" id="qtd-<?= (int) $c['id'] ?>" name="quantidade" value="1" min="1"
                               max="<?= min(50, (int) $c['estoque']) ?>" class="form-control" style="max-width:90px" <?= $esgotado ? 'disabled' : '' ?>>
                        <button class="btn btn-primary flex-grow-1" <?= $esgotado ? 'disabled' : '' ?>>Adicionar</button>
                    </form>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>


