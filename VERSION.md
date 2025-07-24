# Histórico de Versões - Sistema de Gestão da Régua de Comunicação ETEP

## v1.0.1 - 2025-01-24
**Correções de Configuração - Apache e API funcionando**

### Corrigido
- ✅ Configuração do Apache para eliminar loop de redirects
- ✅ Adicionado .htaccess para roteamento correto da API
- ✅ Movido index.html para diretório correto
- ✅ API /api/test funcionando e conectando no MySQL
- ✅ PHPMyAdmin acessível e funcional

### Detalhes Técnicos
- DocumentRoot configurado para /var/www/html
- AllowOverride habilitado para uso do .htaccess
- DirectoryIndex configurado para index.php na pasta /api
- Rewrite rules funcionando corretamente

## v1.0.0 - 2025-01-24
**Fase 1: Foundation & Core Backend - Infraestrutura Base**

### Adicionado
- ✅ Configuração Docker Compose (PHP 8.2 + Apache + MySQL 8.0)
- ✅ Estrutura completa de diretórios do projeto
- ✅ Schema do banco de dados (communication_rules, rule_dispatch_log)
- ✅ Scripts de inicialização e dados de seed
- ✅ API REST básica com endpoints /api/test e /api/communication-rules
- ✅ Configuração Apache com rewrite rules
- ✅ Classe Database para conexão PDO
- ✅ Interface HTML básica de status
- ✅ Documentação completa no README.md

### Detalhes Técnicos
- Docker Compose com 3 serviços: app, db, phpmyadmin
- Portas: 8080 (app), 3307 (mysql), 8081 (phpmyadmin)
- Banco de dados: MySQL 8.0 com tabelas conforme especificação
- API: PHP 8.2 com endpoints RESTful básicos
- Estrutura preparada para Vue.js na Fase 2

### Próximos Passos
- Implementar CRUD completo para communication_rules
- Sistema de versionamento de regras
- Validação de SQL syntax
- Interface Vue.js administrativa