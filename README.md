# 🧁 Cupcakes Gourmet

Aplicação web de uma loja de cupcakes gourmet: **vitrine virtual, pedido eletrônico, pagamento e entrega** — os quatro pilares definidos no Product Backlog do Projeto Integrador Transdisciplinar em Engenharia de Software I.

Projeto Integrador Transdisciplinar em Engenharia de Software II — Nicollas Moleta Borges (RGM 44973900).

| Item | Escolha |
|---|---|
| Linguagem do back-end | PHP 8.1+ (sem framework) |
| Padrão de projeto | MVC (Model–View–Controller) com Front Controller |
| Front-end | HTML5, CSS3, JavaScript + Bootstrap 5 |
| Banco de dados | MySQL 8 |
| Testes | PHPUnit 10 (testes automatizados) |
| Modo de codificação | Tradicional |

**Solução em funcionamento:**
**Vídeo de apresentação:**

## Funcionalidades

**Cliente**
- Vitrine com sabores, preço, aviso de "últimas unidades" e "esgotado"
- Carrinho: adicionar, alterar quantidade, remover (limite de 50 por sabor e respeito ao estoque)
- Checkout com dados do cliente, entrega ou retirada (taxa de R$ 8,00 só na entrega) e forma de pagamento (PIX, cartão ou dinheiro na entrega)
- Confirmação do pedido e acompanhamento do status (número do pedido + e-mail)

**Lojista** (`index.php?r=admin/login`)
- Cadastro, edição e exclusão de cupcakes (cupcakes que já têm pedidos são apenas desativados, para preservar o histórico)
- Lista de pedidos com filtro por status
- Atualização do status (Recebido → Em preparo → Saiu para entrega / Pronto para retirada → Entregue, ou Cancelado). Cancelar devolve os itens ao estoque.

## Como rodar localmente (XAMPP / MySQL)

1. Inicie o Apache e o MySQL (ex.: via XAMPP).
2. Crie a base de dados `cupcakes` no phpMyAdmin (ou terminal) e importe o arquivo `sql/schema.mysql.sql`.
3. Se necessário, ajuste as credenciais do banco em `config/config.php` (o padrão é `localhost`, usuário `root` e senha vazia).
4. Inicie o servidor PHP embutido na pasta do projeto:
```bash
php -S localhost:8000
```
5. Acesse http://localhost:8000 no navegador.
   - Senha padrão do lojista: `admin123`.

## Como rodar os testes

1. Crie uma base de dados para testes chamada `cupcakes_test` e importe o `sql/schema.mysql.sql`.
2. Execute o PHPUnit:
```bash
composer install
vendor/bin/phpunit
```

## Estrutura

```
index.php              Front controller (roteamento e tratamento de erros)
assets/                CSS e JavaScript
config/config.php      Configuração (banco MySQL, senha, taxa de entrega)
src/Core/              Infraestrutura: conexão MySQL, controller base, CSRF, validação
src/Model/             Regras de negócio: Cupcake, Carrinho, Pedido
src/Controller/        Vitrine, Carrinho, Pedido, Admin
src/View/              Telas (HTML + PHP)
sql/                   Script do banco MySQL (schema.mysql.sql)
tests/                 Testes PHPUnit
docs/                  Documentação do projeto (situações 1, 2 e 3)
```

## Documentação

- [Documentação do projeto (escopo, UML, banco de dados, IHC)](docs/1-documentacao-do-projeto.md)
- [Manual de uso](docs/2-manual-de-uso.md)
- [Plano e resultados de testes](docs/3-testes/plano-de-testes.md)
- [Laudo de qualidade](docs/3-testes/laudo-de-qualidade.md)
