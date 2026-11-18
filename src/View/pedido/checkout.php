<?php
use App\Core\Csrf;
use App\Model\Pedido;

$campo = fn(string $n) => limpaTexto($dados[$n] ?? '');
$invalido = fn(string $n) => isset($erros[$n]) ? ' is-invalid' : '';
$msg = fn(string $n) => isset($erros[$n]) ? '<div class="invalid-feedback d-block">' . limpaTexto($erros[$n]) . '</div>' : '';
?>
<h1 class="h3 mb-4">Finalizar pedido</h1>

<?php if (isset($erros['carrinho'])): ?>
    <div class="alert alert-danger"><?= limpaTexto($erros['carrinho']) ?> <a href="index.php?r=carrinho/index">Revisar carrinho</a></div>
<?php elseif ($erros): ?>
    <div class="alert alert-danger">Corrija os campos destacados abaixo.</div>
<?php endif; ?>

<form method="post" action="index.php?r=pedido/checkout" class="row g-4" novalidate>
    <?= Csrf::campo() ?>
    <div class="col-lg-6">
        <div class="card shadow-sm"><div class="card-body">
            <h2 class="h5 mb-3">Seus dados</h2>
            <div class="mb-3">
                <label for="nome" class="form-label">Nome completo *</label>
                <input type="text" id="nome" name="nome" maxlength="100" required class="form-control<?= $invalido('nome') ?>" value="<?= $campo('nome') ?>">
                <?= $msg('nome') ?>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">E-mail *</label>
                <input type="email" id="email" name="email" maxlength="150" required class="form-control<?= $invalido('email') ?>" value="<?= $campo('email') ?>">
                <div class="form-text">Você vai usar este e-mail para acompanhar o pedido.</div>
                <?= $msg('email') ?>
            </div>
            <div class="mb-3">
                <label for="telefone" class="form-label">Telefone com DDD *</label>
                <input type="tel" id="telefone" name="telefone" maxlength="15" required placeholder="(11) 91234-5678"
                       class="form-control<?= $invalido('telefone') ?>" value="<?= $campo('telefone') ?>">
                <?= $msg('telefone') ?>
            </div>
        </div></div>
    </div>

    <div class="col-lg-6">
        <div class="card shadow-sm mb-4"><div class="card-body">
            <h2 class="h5 mb-3">Entrega</h2>
            <?php foreach (Pedido::ENTREGAS as $valor => $rotulo): ?>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="tipo_entrega" id="entrega-<?= $valor ?>" value="<?= $valor ?>"
                        <?= ($dados['tipo_entrega'] ?? '') === $valor ? 'checked' : '' ?>>
                    <label class="form-check-label" for="entrega-<?= $valor ?>">
                        <?= limpaTexto($rotulo) ?><?= $valor === 'entrega' ? ' (+ ' . formataMoeda((float) $config['taxa_entrega']) . ')' : ' (grátis)' ?>
                    </label>
                </div>
            <?php endforeach; ?>
            <?= $msg('tipo_entrega') ?>
            <div class="mt-3" id="bloco-endereco">
                <label for="endereco" class="form-label">Endereço de entrega *</label>
                <input type="text" id="endereco" name="endereco" maxlength="255" placeholder="Rua, número, bairro, complemento"
                       class="form-control<?= $invalido('endereco') ?>" value="<?= $campo('endereco') ?>">
                <?= $msg('endereco') ?>
            </div>
        </div></div>

        <div class="card shadow-sm"><div class="card-body">
            <h2 class="h5 mb-3">Pagamento</h2>
            <?php foreach (Pedido::PAGAMENTOS as $valor => $rotulo): ?>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="forma_pagamento" id="pag-<?= $valor ?>" value="<?= $valor ?>"
                        <?= ($dados['forma_pagamento'] ?? '') === $valor ? 'checked' : '' ?>>
                    <label class="form-check-label" for="pag-<?= $valor ?>"><?= limpaTexto($rotulo) ?></label>
                </div>
            <?php endforeach; ?>
            <div class="mt-2" id="bloco-troco" style="<?= ($dados['forma_pagamento'] ?? '') === 'dinheiro' ? '' : 'display: none;' ?>">
                <label for="troco_para" class="form-label small">Precisa de troco para quanto? (opcional)</label>
                <input type="text" id="troco_para" name="troco_para" placeholder="Ex: 50,00" class="form-control form-control-sm<?= $invalido('troco_para') ?>" style="max-width: 160px;" value="<?= $campo('troco_para') ?>">
                <?= $msg('troco_para') ?>
            </div>
            <?= $msg('forma_pagamento') ?>
            <p class="small text-muted mt-2 mb-0">O pagamento é feito na entrega/retirada. A chave PIX aparece após a confirmação.</p>
        </div></div>
    </div>

    <div class="col-12 d-flex justify-content-between">
        <a href="index.php?r=carrinho/index" class="btn btn-outline-secondary">Voltar ao carrinho</a>
        <button class="btn btn-primary btn-lg" id="btn-confirmar">Confirmar pedido</button>
    </div>
</form>


