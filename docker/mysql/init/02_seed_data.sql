-- Dados de exemplo para testes
-- Inserindo algumas regras de exemplo baseadas na especificação

INSERT INTO communication_rules (rule_id, name, sql_query, channel, template_id, send_time_start, send_time_end, execution_order, active) VALUES
('rule_001', 'lead_captacaod0', 
'SELECT 
    "João" as first_name,
    "5512987654321" as contact,
    JSON_OBJECT("first_name", "João", "due_date", "2025-05-12") as payload
WHERE CURRENT_DATE = CURRENT_DATE
UNION ALL
SELECT 
    "Maria" as first_name,
    "5512987654322" as contact,
    JSON_OBJECT("first_name", "Maria", "due_date", "2025-05-15") as payload
WHERE CURRENT_DATE = CURRENT_DATE', 
'WHATSAPP', '63f9a7e2b12abx458f', '09:00:00', '21:00:00', 1, TRUE),

('rule_002', 'lead_nurturing2d5', 
'SELECT 
    "Pedro" as first_name,
    "PEDRO@GMAIL.COM" as contact,
    JSON_OBJECT("first_name", "Pedro", "course", "Engenharia") as payload
WHERE CURRENT_DATE = CURRENT_DATE', 
'EMAIL', 'template_email_001', '08:00:00', '18:00:00', 2, TRUE),

('rule_003', 'alunoativo_retencaopreventiva', 
'SELECT 
    "Ana" as first_name,
    "5511999888777" as contact,
    JSON_OBJECT("first_name", "Ana", "next_payment", "2025-06-01") as payload
WHERE CURRENT_DATE = CURRENT_DATE', 
'SMS', 'template_sms_001', '10:00:00', '17:00:00', 3, FALSE);

-- Inserindo alguns logs de exemplo
INSERT INTO rule_dispatch_log (rule_snapshot, recipients, execution_status, response_payload, execution_time) VALUES
(JSON_OBJECT(
    "rule_id", "rule_001",
    "name", "lead_captacaod0",
    "channel", "WHATSAPP",
    "template_id", "63f9a7e2b12abx458f"
), 
JSON_ARRAY(
    JSON_OBJECT("first_name", "João", "contact", "5512987654321", "payload", JSON_OBJECT("first_name", "João", "due_date", "2025-05-12"))
), 
'success', 
JSON_OBJECT("status", "sent", "message_id", "msg_123456"), 
0.45),

(JSON_OBJECT(
    "rule_id", "rule_002", 
    "name", "lead_nurturing2d5",
    "channel", "EMAIL",
    "template_id", "template_email_001"
), 
JSON_ARRAY(), 
'empty', 
NULL, 
0.23);