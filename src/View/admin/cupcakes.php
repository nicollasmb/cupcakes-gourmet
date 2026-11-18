<?php use App\Core\Csrf; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Cupcakes</h1>
    <a href="index.php?r=admin/cupcakeForm" class="btn btn-primary">+ Novo cupcake</a>
</div>
<div class="table-responsive">
    <table class="table align-middle">
        <thead><tr><th>Nome</th><th>Preço</th><th>Estoque</th><th>Situação</th><th class="text-end">Ações</th></tr></thead>
        <tbody>
        <?php foreach ($cupcakes as $c): ?>
            <tr class="<?= $c['ativo'] ? '' : 'text-muted' ?>">
                <td><?= limpaTexto($c['nome']) ?></td>
                <td><?= formataMoeda((float) $c['preco']) ?></td>
                <td><?= (int) $c['estoque'] ?></td>
                <td><?= $c['ativo'] ? '<span class="badge bg-success">Na vitrine</span>' : '<span class="badge bg-secondary">Inativo</span>' ?></td>
                <td class="text-end text-nowrap">
                    <a href="index.php?r=admin/cupcakeForm&id=<?= (int) $c['id'] ?>" class="btn btn-sm btn-outline-primary">Editar</a>
                    <form method="post" action="index.php?r=admin/cupcakeExcluir" class="d-inline"
                          data-confirmar="Excluir o cupcake <?= limpaTexto($c['nome']) ?>?">
                        <?= Csrf::campo() ?>
                        <input type="hidden" name="id" value="<?= (int) $c['id'] ?>">
                        <button class="btn btn-sm btn-outline-danger">Excluir</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>


