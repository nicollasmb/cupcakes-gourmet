<?php
use App\Model\Pedido;

$cores = ['recebido' => 'primary', 'em_preparo' => 'warning', 'saiu_para_entrega' => 'info',
          'pronto_para_retirada' => 'info', 'entregue' => 'success', 'cancelado' => 'secondary'];
?>
<div class="card shadow-sm">
    <div class="card-body">
        <div class="d-flex flex-wrap justify-content-between gap-2 mb-3">
            <div>
                <span class="badge bg-<?= $cores[$pedido['status']] ?? 'dark' ?> fs-6"><?= limpaTexto(Pedido::STATUS[$pedido['status']] ?? $pedido['status']) ?></span>
                <span class="text-muted small ms-2">Feito em <?= formataData($pedido['criado_em']) ?></span>
            </div>
            <div class="small text-muted">
                <?= limpaTexto(Pedido::ENTREGAS[$pedido['tipo_entrega']]) ?> · <?= limpaTexto(Pedido::PAGAMENTOS[$pedido['forma_pagamento']]) ?>
                <?php if ($pedido['forma_pagamento'] === 'dinheiro' && !empty($pedido['troco_para'])): ?>
                    · Troco para <?= formataMoeda((float) $pedido['troco_para']) ?>
                <?php endif; ?>
            </div>
        </div>
        <?php if ($pedido['endereco_entrega']): ?>
            <p class="mb-3"><strong>Endereço:</strong> <?= limpaTexto($pedido['endereco_entrega']) ?></p>
        <?php endif; ?>
        <table class="table table-sm mb-0">
            <thead><tr><th>Cupcake</th><th class="text-center">Qtd</th><th class="text-end">Unitário</th><th class="text-end">Total</th></tr></thead>
            <tbody>
            <?php foreach ($pedido['itens'] as $i): ?>
                <tr>
                    <td><?= limpaTexto($i['nome']) ?></td>
                    <td class="text-center"><?= (int) $i['quantidade'] ?></td>
                    <td class="text-end"><?= formataMoeda((float) $i['preco_unitario']) ?></td>
                    <td class="text-end"><?= formataMoeda($i['quantidade'] * (float) $i['preco_unitario']) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr><td colspan="3" class="text-end">Subtotal</td><td class="text-end"><?= formataMoeda((float) $pedido['subtotal']) ?></td></tr>
                <tr><td colspan="3" class="text-end">Taxa de entrega</td><td class="text-end"><?= formataMoeda((float) $pedido['taxa_entrega']) ?></td></tr>
                <tr><th colspan="3" class="text-end">Total</th><th class="text-end"><?= formataMoeda((float) $pedido['total']) ?></th></tr>
            </tfoot>
        </table>
    </div>
</div>


