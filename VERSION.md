# Histórico de Versões - Sistema de Gestão da Régua de Comunicação ETEP

## v2.2.0 - 2025-07-24
**Interface Vue.js Corrigida - Deploy Automatizado**

### Corrigido
- ✅ Vue.js data binding: interface agora exibe todos os campos corretamente
- ✅ Problema com response.data vs response.data.data resolvido
- ✅ Build npm funcionando corretamente (bypass cross-env no Windows)
- ✅ Deploy automatizado com scripts deploy.bat e deploy.sh

### Funcionalidades Validadas
- 🔧 Todos os campos visíveis: Tipo, SQL, Versão, Data de Criação
- 🔧 Datas formatadas corretamente em pt-BR
- 🔧 Emojis sendo exibidos perfeitamente na interface
- 🔧 Sistema de deploy profissional implementado

### Deploy Automation
- Script deploy.bat para Windows
- Script deploy.sh para Unix/Linux
- Build → Restart → Test automatizado
- Verificação de status HTTP incluída

## v2.1.0 - 2025-07-24
**UTF-8 e Emojis Implementados**

### Adicionado
- ✅ Encoding UTF-8 completo em API e banco de dados
- ✅ Suporte nativo a emojis em mensagens e nomes
- ✅ 3 novas réguas de teste com emojis
- ✅ Formatação de datas JavaScript melhorada

### Funcionalidades
- 🎨 Réguas com emojis: 🎂 Aniversário, ⚠️ Pagamento Urgente, 🎉 Boas-vindas
- 🌐 Charset UTF-8 configurado em headers HTTP
- 🗄️ MySQL com utf8mb4_unicode_ci para emojis
- 📅 Datas MySQL formatadas corretamente no frontend

## v2.0.0 - 2025-07-24
**Interface Vue.js Funcional**

### Adicionado
- ✅ Interface administrativa Vue.js 3 completa
- ✅ Componentes: CommunicationRules, RuleForm, RuleLogs
- ✅ Sistema de versionamento conjunto App + Infra
- ✅ CRUD completo de réguas de comunicação

### Funcionalidades
- 📊 Listagem de réguas com cards responsivos
- ➕ Criação e edição de réguas
- 🔄 Ativação/desativação de réguas
- 📈 Visualização de logs (preparado)
- 🎨 Interface moderna com Bootstrap-style CSS

### Integração
- 🔗 API REST PHP totalmente funcional
- 🗄️ Banco MySQL com dados de exemplo
- 🌐 Servido via Apache com aliases corretos
- 📱 SPA routing configurado

## v1.0.0 - 2025-01-24
**Foundation - API REST PHP**

### Adicionado
- ✅ API REST PHP 8.2 completa
- ✅ Endpoints CRUD para communication_rules
- ✅ Banco MySQL com estrutura completa
- ✅ Sistema de versionamento visual

### Funcionalidades
- 🔌 GET /api/communication-rules - Listar réguas
- ➕ POST /api/communication-rules - Criar régua
- ✏️ PUT /api/communication-rules/{id} - Editar régua
- 🔄 POST /api/communication-rules/{id}/activate - Ativar
- ⏸️ POST /api/communication-rules/{id}/deactivate - Desativar
- 📊 GET /api/communication-rules/{id}/logs - Logs

### Arquitetura
- 🗄️ MySQL 8.0 com tabelas normalizadas
- 🐳 Docker Compose para desenvolvimento
- 🌐 Apache configurado com mod_rewrite
- 📝 Documentação técnica completa