<?php
use App\Model\Pedido;
?>
<?php if ($confirmacao): ?>
    <div class="alert alert-success">
        <h1 class="h4 mb-1">Pedido #<?= (int) $pedido['id'] ?> recebido! 🎉</h1>
        Guarde o número do pedido. Para acompanhar, use-o junto com o e-mail <strong><?= limpaTexto($pedido['cliente_email']) ?></strong>.
    </div>
<?php else: ?>
    <h1 class="h3 mb-3">Pedido #<?= (int) $pedido['id'] ?></h1>
<?php endif; ?>

<?= $this->renderParcial('pedido/_resumo', ['pedido' => $pedido]) ?>

<?php if ($pedido['forma_pagamento'] === 'pix' && $pedido['status'] !== 'cancelado'): ?>
    <div class="alert alert-info mt-3">
        <strong>Pagamento via PIX:</strong> chave <code>pix@cupcakesgourmet.com.br</code> — valor <?= formataMoeda((float) $pedido['total']) ?>.
        Envie o comprovante pelo WhatsApp informando o número do pedido.
    </div>
<?php endif; ?>

<a href="index.php" class="btn btn-primary mt-3">Voltar para a vitrine</a>


