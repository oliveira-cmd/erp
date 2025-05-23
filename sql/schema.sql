CREATE DATABASE IF NOT EXISTS mini_erp_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE mini_erp_db;

CREATE TABLE IF NOT EXISTS `produtos` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nome` VARCHAR(255) NOT NULL,
  `preco` DECIMAL(10, 2) NOT NULL,
  `variacoes_descricao` TEXT NULL COMMENT 'Descrição textual das variações, ex: Cor: Azul/Vermelho, Tamanho: P/M/G',
  `data_criacao` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `data_atualizacao` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `estoque` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `produto_id` INT NOT NULL,
  `quantidade` INT NOT NULL DEFAULT 0,
  FOREIGN KEY (`produto_id`) REFERENCES `produtos`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `cupons` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `codigo` VARCHAR(50) NOT NULL UNIQUE,
  `tipo_desconto` ENUM('percentual', 'fixo') NOT NULL,
  `valor_desconto` DECIMAL(10, 2) NOT NULL,
  `data_validade` DATE NULL,
  `usos_maximos` INT NULL,
  `usos_atuais` INT DEFAULT 0,
  `ativo` BOOLEAN DEFAULT TRUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `cupons` (`codigo`, `tipo_desconto`, `valor_desconto`, `data_validade`, `ativo`) VALUES
('DESC10', 'percentual', 10.00, '2026-12-31', TRUE),
('FRETEGRATISVIP', 'fixo', 0.00, NULL, TRUE),
('VALOR5', 'fixo', 5.00, '2024-08-30', TRUE);


CREATE TABLE IF NOT EXISTS `pedidos` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `data_pedido` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `subtotal` DECIMAL(10, 2) NOT NULL,
  `valor_frete` DECIMAL(10, 2) NOT NULL,
  `cupom_id` INT NULL,
  `valor_desconto_cupom` DECIMAL(10, 2) DEFAULT 0.00,
  `valor_total` DECIMAL(10, 2) NOT NULL,
  `cep` VARCHAR(9) NULL,
  `endereco_entrega` TEXT NULL,
  `status_pedido` VARCHAR(50) DEFAULT 'Pendente',
  FOREIGN KEY (`cupom_id`) REFERENCES `cupons`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `pedido_itens` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `pedido_id` INT NOT NULL,
  `produto_id` INT NOT NULL,
  `quantidade` INT NOT NULL,
  `preco_unitario` DECIMAL(10, 2) NOT NULL COMMENT 'Preço do produto no momento da compra',
  FOREIGN KEY (`pedido_id`) REFERENCES `pedidos`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`produto_id`) REFERENCES `produtos`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;