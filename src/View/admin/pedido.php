<?php use App\Core\Csrf; use App\Model\Pedido; ?>
<a href="index.php?r=admin/pedidos" class="small">&larr; Voltar aos pedidos</a>
<h1 class="h3 my-3">Pedido #<?= (int) $pedido['id'] ?></h1>

<div class="row g-4">
    <div class="col-lg-8">
        <?= $this->renderParcial('pedido/_resumo', ['pedido' => $pedido]) ?>
    </div>
    <div class="col-lg-4">
        <div class="card shadow-sm mb-4"><div class="card-body">
            <h2 class="h6">Cliente</h2>
            <p class="mb-0"><?= limpaTexto($pedido['cliente_nome']) ?><br>
                <a href="mailto:<?= limpaTexto($pedido['cliente_email']) ?>"><?= limpaTexto($pedido['cliente_email']) ?></a><br>
                <?= limpaTexto($pedido['cliente_telefone']) ?></p>
        </div></div>

        <div class="card shadow-sm"><div class="card-body">
            <h2 class="h6">Atualizar status</h2>
            <?php if (in_array($pedido['status'], Pedido::STATUS_FINAIS, true)): ?>
                <p class="text-muted small mb-0">Pedido finalizado — o status não pode mais ser alterado.</p>
            <?php else: ?>
                <form method="post" action="index.php?r=admin/pedidoStatus" class="d-flex gap-2">
                    <?= Csrf::campo() ?>
                    <input type="hidden" name="id" value="<?= (int) $pedido['id'] ?>">
                    <select name="status" class="form-select" aria-label="Novo status">
                        <?php foreach (Pedido::statusPermitidos($pedido) as $valor => $rotulo): ?>
                            <option value="<?= $valor ?>" <?= $pedido['status'] === $valor ? 'selected' : '' ?>><?= limpaTexto($rotulo) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button class="btn btn-primary">Salvar</button>
                </form>
                <p class="small text-muted mt-2 mb-0">Cancelar devolve os itens ao estoque.</p>
            <?php endif; ?>
        </div></div>
    </div>
</div>


