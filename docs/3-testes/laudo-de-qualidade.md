# Laudo de qualidade — Cupcakes Gourmet

Relatório elaborado após a realização dos ciclos de testes (unitários, sistema e aceitação com usuários).

## 1. Identificação

| Item | Valor |
|---|---|
| Sistema | Cupcakes Gourmet |
| Endereço testado | `http://localhost:8000` |
| Período dos testes | 09/11/2026 a 11/11/2026 |
| Testadores | 5 usuários convidados (perfil clientes e lojista) |

## 2. Resumo dos resultados

| Tipo de teste | Executados | Aprovados | Reprovados |
|---|---|---|---|
| Unitários (PHPUnit) | 6 | 6 | 0 |
| Sistema (roteiro S01–S18) | 18 | 18 | 0 |
| Aceitação (usuários) | 5 testadores | 2 sem ressalvas | 3 com erros/sugestões |

## 3. Ocorrências encontradas pelos usuários

| ID | Relatado por | Descrição do problema | Severidade | Evidência | Situação |
|---|---|---|---|---|---|
| BUG-01 | Usuário 2 | Ao selecionar a forma de pagamento "Dinheiro na entrega", o sistema permite informar um valor de troco menor que o valor total do pedido (ex: pedido de R$ 42,00 com troco para R$ 20,00). | Média | Tela de checkout / confirmação | Identificado para ajuste |
| BUG-02 | Usuário 4 | Na tela de gerenciamento de pedidos (área do lojista), o seletor permite alterar um pedido já marcado como "Entregue" ou "Cancelado" de volta para "Em preparo", gerando inconsistência no controle de estoque já estornado. | Alta | Detalhe do pedido no painel do lojista | Identificado para ajuste |
| MEL-01 | Usuário 1 | Sugestão de adicionar fotos reais dos cupcakes na vitrine em vez de usar apenas emojis. | Baixa | Vitrine principal | Registrado no backlog futuro |

## 4. Funcionalidades não testadas

| Funcionalidade | Motivo |
|---|---|
| Pagamento com cartão online / gateway direto | Fora do escopo da entrega atual (pagamento na entrega/retirada). |
| Envio automático de e-mail ao cliente | Não configurado servidor SMTP em ambiente local. |

## 5. Conclusão

Os testes unitários e de fluxo de sistema foram aprovados com sucesso. Na etapa de testes de aceitação com usuários convidados, foram levantadas 2 ocorrências de inconsistência de regras de negócio (BUG-01 e BUG-02), as quais foram documentadas para correção e evolução do sistema, além de 1 sugestão de melhoria visual (MEL-01) encaminhada para o backlog futuro do projeto.
