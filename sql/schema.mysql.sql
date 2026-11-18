-- Cupcakes Gourmet - Projeto físico (MySQL 8 / MariaDB 10.4+)
-- Execute no phpMyAdmin da hospedagem ou: mysql -u root -p cupcakes < schema.mysql.sql

SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS cupcakes (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome        VARCHAR(100)  NOT NULL,
    descricao   VARCHAR(500)  NOT NULL DEFAULT '',
    preco       DECIMAL(8,2)  NOT NULL,
    estoque     INT UNSIGNED  NOT NULL DEFAULT 0,
    ativo       TINYINT(1)    NOT NULL DEFAULT 1,
    criado_em   DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT ck_cupcakes_preco CHECK (preco > 0),
    CONSTRAINT uq_cupcakes_nome UNIQUE (nome)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS clientes (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome        VARCHAR(100) NOT NULL,
    email       VARCHAR(150) NOT NULL,
    telefone    VARCHAR(11)  NOT NULL,
    criado_em   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT uq_clientes_email UNIQUE (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS pedidos (
    id               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    cliente_id       INT UNSIGNED  NOT NULL,
    status           VARCHAR(30)   NOT NULL DEFAULT 'recebido',
    tipo_entrega     VARCHAR(10)   NOT NULL,
    endereco_entrega VARCHAR(255)  NULL,
    forma_pagamento  VARCHAR(10)   NOT NULL,
    subtotal         DECIMAL(10,2) NOT NULL,
    taxa_entrega     DECIMAL(8,2)  NOT NULL DEFAULT 0,
    total            DECIMAL(10,2) NOT NULL,
    troco_para       DECIMAL(10,2) NULL,
    criado_em        DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_pedidos_cliente FOREIGN KEY (cliente_id) REFERENCES clientes(id),
    CONSTRAINT ck_pedidos_status CHECK (status IN ('recebido','em_preparo','saiu_para_entrega','pronto_para_retirada','entregue','cancelado')),
    CONSTRAINT ck_pedidos_entrega CHECK (tipo_entrega IN ('entrega','retirada')),
    CONSTRAINT ck_pedidos_pagamento CHECK (forma_pagamento IN ('pix','cartao','dinheiro')),
    CONSTRAINT ck_pedidos_total CHECK (total > 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS itens_pedido (
    id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    pedido_id      INT UNSIGNED NOT NULL,
    cupcake_id     INT UNSIGNED NOT NULL,
    quantidade     INT UNSIGNED NOT NULL,
    preco_unitario DECIMAL(8,2) NOT NULL,
    CONSTRAINT fk_itens_pedido FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE CASCADE,
    CONSTRAINT fk_itens_cupcake FOREIGN KEY (cupcake_id) REFERENCES cupcakes(id),
    CONSTRAINT ck_itens_qtd CHECK (quantidade > 0),
    CONSTRAINT ck_itens_preco CHECK (preco_unitario > 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE INDEX ix_pedidos_status ON pedidos(status);
CREATE INDEX ix_itens_pedido ON itens_pedido(pedido_id);

-- Carga inicial
INSERT INTO cupcakes (nome, descricao, preco, estoque) VALUES
('Red Velvet',          'Massa aveludada com cobertura de cream cheese.',            9.50, 30),
('Chocolate Belga',     'Massa de cacau 70% e ganache de chocolate belga.',          10.00, 25),
('Pistache',            'Massa amanteigada com creme de pistache siciliano.',        12.00, 15),
('Limão Siciliano',     'Massa cítrica com merengue maçaricado.',                     9.00, 20),
('Doce de Leite',       'Recheio de doce de leite artesanal e flor de sal.',          9.50, 20),
('Frutas Vermelhas',    'Massa de baunilha com compota de frutas vermelhas.',        11.00, 18);
