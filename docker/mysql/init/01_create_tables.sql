-- Database: comm_manager
-- Sistema de Gestão da Régua de Comunicação ETEP

-- Tabela principal das regras de comunicação
CREATE TABLE IF NOT EXISTS communication_rules (
    id INT AUTO_INCREMENT PRIMARY KEY,
    rule_id VARCHAR(50) NOT NULL,
    name VARCHAR(255) NOT NULL UNIQUE,
    sql_query TEXT NOT NULL,
    channel ENUM('WHATSAPP', 'EMAIL', 'SMS') NOT NULL,
    template_id VARCHAR(100) NOT NULL,
    send_time_start TIME NULL,
    send_time_end TIME NULL,
    execution_order INT NULL,
    active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    superseded BOOLEAN DEFAULT FALSE,
    
    INDEX idx_rule_id (rule_id),
    INDEX idx_name (name),
    INDEX idx_active (active),
    INDEX idx_created_at (created_at)
);

-- Tabela de logs de execução/dispatch das regras
CREATE TABLE IF NOT EXISTS rule_dispatch_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    rule_dispatch_timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    rule_snapshot JSON NOT NULL,
    recipients JSON NULL,
    execution_status ENUM('success', 'empty', 'error') NOT NULL,
    response_payload JSON NULL,
    execution_time FLOAT NULL,
    error_message TEXT NULL,
    
    INDEX idx_timestamp (rule_dispatch_timestamp),
    INDEX idx_status (execution_status)
);