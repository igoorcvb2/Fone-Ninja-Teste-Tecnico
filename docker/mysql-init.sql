-- Cria o banco de testes em paralelo ao banco principal e dá acesso ao usuário do app.
CREATE DATABASE IF NOT EXISTS erp_test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
GRANT ALL PRIVILEGES ON erp_test.* TO 'erp'@'%';
FLUSH PRIVILEGES;
