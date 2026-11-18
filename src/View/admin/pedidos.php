<?php use App\Model\Pedido; ?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <h1 class="h3 mb-0">Pedidos</h1>
    <form method="get" class="d-flex gap-2">
        <input type="hidden" name="r" value="admin/pedidos">
        <select name="status" class="form-select" aria-label="Filtrar por status" onchange="this.form.submit()">
            <option value="">Todos os status</option>
            <?php foreach (Pedido::STATUS as $valor => $rotulo): ?>
                <option value="<?= $valor ?>" <?= $filtro === $valor ? 'selected' : '' ?>><?= limpaTexto($rotulo) ?></option>
            <?php endforeach; ?>
        </select>
    </form>
</div>
<?php if (!$pedidos): ?>
    <div class="alert alert-info">Nenhum pedido encontrado.</div>
<?php else: ?>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead><tr><th>#</th><th>Data</th><th>Cliente</th><th>Entrega</th><th>Total</th><th>Status</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($pedidos as $p): ?>
                <tr>
                    <td><?= (int) $p['id'] ?></td>
                    <td><?= formataData($p['criado_em']) ?></td>
                    <td><?= limpaTexto($p['cliente_nome']) ?></td>
                    <td><?= limpaTexto(Pedido::ENTREGAS[$p['tipo_entrega']]) ?></td>
                    <td><?= formataMoeda((float) $p['total']) ?></td>
                    <td><?= limpaTexto(Pedido::STATUS[$p['status']]) ?></td>
                    <td class="text-end"><a href="index.php?r=admin/pedido&id=<?= (int) $p['id'] ?>" class="btn btn-sm btn-outline-primary">Abrir</a></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>


