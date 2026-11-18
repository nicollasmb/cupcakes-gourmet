// Comportamentos simples da interface.
document.addEventListener('DOMContentLoaded', () => {
  // Confirmação antes de ações destrutivas (ex.: excluir cupcake).
  document.querySelectorAll('form[data-confirmar]').forEach((form) => {
    form.addEventListener('submit', (e) => {
      if (!window.confirm(form.dataset.confirmar)) e.preventDefault();
    });
  });

  // Checkout: mostra o endereço só quando "entrega" estiver marcado.
  const bloco = document.getElementById('bloco-endereco');
  if (bloco) {
    const atualizar = () => {
      const entrega = document.getElementById('entrega-entrega');
      bloco.style.display = entrega && entrega.checked ? '' : 'none';
    };
    document.querySelectorAll('input[name="tipo_entrega"]').forEach((r) => r.addEventListener('change', atualizar));
    atualizar();
  }

  // Checkout: mostra campo de troco se a forma de pagamento for dinheiro.
  const blocoTroco = document.getElementById('bloco-troco');
  if (blocoTroco) {
    const atualizarTroco = () => {
      const dinheiro = document.getElementById('pag-dinheiro');
      blocoTroco.style.display = dinheiro && dinheiro.checked ? '' : 'none';
    };
    document.querySelectorAll('input[name="forma_pagamento"]').forEach((r) => r.addEventListener('change', atualizarTroco));
    atualizarTroco();
  }

  // Evita clique duplo no "Confirmar pedido" (pedido duplicado).
  const btn = document.getElementById('btn-confirmar');
  if (btn) {
    btn.form.addEventListener('submit', () => {
      btn.disabled = true;
      btn.textContent = 'Enviando...';
    });
  }
});
