# Histórico de Versões - Sistema de Gestão da Régua de Comunicação ETEP

## v1.1.0 - 2025-01-24
**Fase 2: Interface Administrativa Vue.js - CRUD Completo**

### Adicionado
- ✅ Node.js 18 integrado ao container Docker
- ✅ Interface Vue.js 3 + Vite com componentes completos
- ✅ Componente CommunicationRules.vue para listagem
- ✅ Componente RuleForm.vue para criação/edição
- ✅ Componente RuleLogs.vue para visualização de logs
- ✅ API REST completa com todos os endpoints CRUD
- ✅ Sistema de roteamento dinâmico na API
- ✅ Endpoints: GET, POST, PUT, ativação/desativação, logs
- ✅ Build automático do Vue.js durante container build

### Funcionalidades
- Interface administrativa em /admin/ totalmente funcional
- CRUD completo de réguas de comunicação
- Sistema de versionamento automático (id vs rule_id)
- Ativação/desativação de réguas (soft delete)
- Teste de SQL com simulação de validação
- Visualização de logs de execução
- Interface responsiva e profissional

### Detalhes Técnicos
- Vue.js 3 com Composition API
- Axios para comunicação com API
- Build process integrado no Dockerfile
- API REST com roteamento por segments
- Validação de campos obrigatórios
- Sistema de status visual (badges coloridos)

## v1.0.2 - 2025-01-24
**Display de Versão Dinâmico - Sistema de Versionamento Visual**

### Adicionado
- ✅ Classe VersionReader em PHP para ler VERSION.md
- ✅ Página inicial convertida para PHP (index.php)
- ✅ Display dinâmico da versão atual na página inicial
- ✅ VERSION.md movido para src/ para acesso via web
- ✅ Apache configurado para servir index.php como padrão

### Funcionalidade
- A página http://localhost:8080 agora mostra a versão atual automaticamente
- Ao editar VERSION.md e fazer push, a versão é atualizada com refresh da página
- Sistema visual para confirmar que nova versão foi carregada

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