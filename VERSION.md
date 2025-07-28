# Histórico de Versões - Sistema de Gestão da Régua de Comunicação ETEP

## v2.10.0 - 2025-07-28
**MAJOR: Histórico de Versões das Réguas COMPLETO**

### 🎯 NOVA FUNCIONALIDADE: HISTÓRICO DE VERSÕES
- ✅ **Menu sidebar**: "Histórico de Versões" implementado
- ✅ **Lista agrupada por status**: mesma ordenação da tela de edição
- ✅ **Cards recolhidos**: clique expande para mostrar tabela completa
- ✅ **Tabela de versões**: todas as versões ordenadas por timestamp (mais recente primeiro)
- ✅ **Campo SQL destacado**: coluna SQL com código formatado (o mais importante!)

### 🔧 BACKEND API COMPLETO
- ✅ **Endpoint `/api/communication-rules/versions-summary`**: lista regras agrupadas por rule_id
- ✅ **Endpoint `/api/communication-rules/versions/{rule_id}`**: todas as versões de uma regra
- ✅ **Dados reais**: versionamento funcional testado com regras existentes
- ✅ **Orderização inteligente**: por execution_order depois por nome

### 🎨 INTERFACE COMPLETA
- ✅ **Campos mostrados**: Versão, Data/Hora, Situação, Nome, **SQL**, Canal, Template, Horários, Ordem, Ativa
- ✅ **Situação das versões**: "Atual" vs "Substituída" com badges coloridos
- ✅ **Versão atual destacada**: fundo laranja ETEP para versão ativa
- ✅ **Design ETEP**: cores e tipografia consistentes com identidade visual

### 🔧 CORREÇÕES TÉCNICAS
- ✅ **Vue 3 compatibility**: removido `this.$set` (substituído por reatividade nativa)
- ✅ **Routing fix**: endpoints específicos posicionados antes dos genéricos
- ✅ **SQL formatação**: código SQL com `<code>` blocks e quebra de linha

### ✨ Resultado Final
Interface completa para consultar todo o histórico de modificações das réguas, permitindo rastreabilidade total das alterações e comparação entre versões. Fundamental para auditoria e troubleshooting.

## v2.9.0 - 2025-07-28
**MAJOR: Implementação Completa do Menu Sidebar + Navegação**

### ✅ SISTEMA DE NAVEGAÇÃO SIDEBAR IMPLEMENTADO
- ✅ **Menu estruturado**: Réguas, Execuções, Ferramentas
- ✅ **Painel Diário** como página inicial com estatísticas reais
- ✅ **Páginas placeholder** para funcionalidades em desenvolvimento
- ✅ **Vue Router 4** configurado com histórico para /admin/
- ✅ **Layout responsivo** com ETEP brand colors

### 🧩 FUNCIONALIDADES IMPLEMENTADAS
- ✅ **Sidebar com menu expansível**: seguindo padrão app-front
- ✅ **DailyPanel**: contadores de réguas ativas/inativas carregados da API
- ✅ **Páginas RuleVersions e DetailedLogs**: em construção com preview das funcionalidades
- ✅ **Navegação fluida**: entre seções sem reload de página
- ✅ **Design system ETEP**: integrado em todos os componentes

### 🔜 Estrutura Preparada Para
- Implementar histórico de versões das réguas
- Painel visual de execuções (dias × réguas)
- Logs detalhados com filtros avançados
- Sistema de relatórios e métricas

## v2.8.1 - 2025-07-28
**HOTFIX: Cards UX Perfect - Alinhamento e Botões**

### 🔧 CORREÇÕES DE UX
- ✅ **Cards uniformes**: Flexbox column elimina tarja branca desalinhada
- ✅ **Background consistente**: Todo card com fundo cinza ETEP uniforme
- ✅ **Botões alinhados**: Actions sempre no bottom, independente do conteúdo
- ✅ **Cores dos botões**: Todos os botões agora seguem identidade ETEP

### 🎨 BOTÕES COM BRANDING ETEP
- **✏️ Editar**: Laranja ETEP (secondary)
- **▶️ Ativar**: Laranja ETEP (success) 
- **⏸️ Desativar**: Cinza ETEP (warning)
- **📊 Logs**: Azul escuro ETEP (info)

### ✨ Resultado Final
- Cards lado a lado perfeitamente alinhados
- Visual 100% consistente com identidade ETEP
- UX profissional sem elementos desalinhados

## v2.8.0 - 2025-07-28
**NOVA SKIN: Cores ETEP Aplicadas**

### 🎨 VISUAL IDENTITY ETEP IMPLEMENTADO
- ✅ **Cores oficiais ETEP**: Primary `#252e62`, Secondary `#ed6e26`
- ✅ **Header**: Azul escuro ETEP com subtítulo laranja
- ✅ **Background**: Cinza claro ETEP `#f0f0f0` 
- ✅ **Botões**: Primary azul ETEP, Secondary laranja ETEP
- ✅ **Toggle**: Estados com cores oficiais ETEP
- ✅ **Tipografia**: Open Sans + cores padrão ETEP

### 🎯 Baseado em
- Análise completa do padrão visual admin.etepead.com.br
- Extração das variáveis CSS do app-front
- Manutenção da estrutura atual (v2 será layout + menu)

### ✨ Resultado
- Interface visualmente alinhada com identidade ETEP
- Mesma funcionalidade, novo visual profissional
- Preparação para v2 com sidebar + menu structure

## v2.7.0 - 2025-07-25
**MAJOR: UX Perfeita + Toggles Material Design Implementados**

### ✅ TOGGLES MATERIAL DESIGN COMPLETOS
- ✅ **Campos opcionais**: "Preencher horário?" e "Preencher ordem?"
- ✅ **Lógica inteligente**: toggles habilitados automaticamente se campos preenchidos
- ✅ **Watchers**: campos limpos automaticamente quando toggle desabilitado
- ✅ **Demo page**: 6 estilos de toggle para escolha do usuário

### ✅ VALIDAÇÃO EM TEMPO REAL FUNCIONANDO
- ✅ **API endpoint**: `/communication-rules/check-name` 
- ✅ **Debounce 500ms**: 🔍 Verificando → ❌ Existe → ✅ Disponível
- ✅ **Bloqueio submit**: impede criar réguas com nome duplicado
- ✅ **Campo nome**: desabilitado durante edição (sem confusão)

### 🔧 CORREÇÕES TÉCNICAS
- ✅ **Campo active**: convertido para int(bool) para evitar erros MySQL
- ✅ **Função nullIfEmpty**: melhorada para tratar mais casos
- ✅ **Sistema versionamento**: constraint UNIQUE removida para funcionar

## v2.6.0 - 2025-07-25
**MAJOR: Interface Totalmente Funcional + CRUD Completo**

### ✅ FUNCIONALIDADES COMPLETAS VALIDADAS
- ✅ **Criação de réguas**: Funcionando com UTF-8 ✅
- ✅ **Edição de réguas**: Funcionando perfeitamente ✅  
- ✅ **Ativar/Desativar**: Funcionando perfeitamente ✅
- ✅ **Exibição de campos**: Canal, Template, Horário, Ordem todos visíveis ✅
- ✅ **Mapeamento correto**: Frontend ↔ API fields (type→channel, message_template→template_id) ✅

### ✅ ENCODING UTF-8 SISTEMÁTICO COMPLETO (5/5 PASSOS) 🎉
- ✅ **PASSO 1**: Banco com encoding utf8mb4_unicode_ci correto
- ✅ **PASSO 2**: API enviando dados UTF-8 com JSON_UNESCAPED_UNICODE  
- ✅ **PASSO 3**: Admin exibindo UTF-8 corretamente (confirmado: "João" e "🎂")
- ✅ **PASSO 4**: Admin enviando UTF-8 (mb_convert_encoding na API) ✅
- ✅ **PASSO 5**: Banco gravando UTF-8 ✅ (confirmado via interface: "José da Silva 🎂")

### 🎯 Melhorias UX Identificadas (Backlog)
- Mensagem erro nome duplicado mais amigável
- Campos opcionais (horário/ordem) com toggle habilitação
- Fix: campos vazios salvando 00:00:00 → deveria ser N/A

## v2.5.0 - 2025-07-25
**MAJOR: Sistema de Encoding UTF-8 COMPLETAMENTE FUNCIONAL**

### ✅ ENCODING SISTEMÁTICO RESOLVIDO (5 PASSOS)
- ✅ **PASSO 1**: Banco com encoding utf8mb4_unicode_ci correto
- ✅ **PASSO 2**: API enviando dados UTF-8 com JSON_UNESCAPED_UNICODE
- ✅ **PASSO 3**: Admin exibindo UTF-8 corretamente (confirmado pelo usuário: "João" e "🎂")
- 🔄 **PASSO 4**: Admin enviando dados UTF-8 (em teste)
- 🔄 **PASSO 5**: Banco gravando UTF-8 (próximo)

### 🎯 Scripts MySQL Atualizados
- Scripts de inicialização `docker/mysql/init/` atualizados
- Tabelas criadas com charset utf8mb4_unicode_ci
- Dados de exemplo com caracteres UTF-8 e emojis
- SET NAMES utf8mb4 forçado nos scripts

### 🎂 Funcionalidades Testadas
- "João" exibido corretamente na interface ✅
- Emoji "🎂" exibido corretamente na interface ✅ 
- API retornando UTF-8 sem problemas ✅
- Build automatizado funcionando ✅

## v2.4.3 - 2025-07-24
**Fix: Encoding UTF-8 corrigido na exibição do SQL**

### ✅ Correção Aplicada
- 🔤 Forçado encoding UTF-8 na conexão PDO com MySQL
- 📝 Adicionado SET NAMES utf8mb4 e SET CHARACTER SET utf8mb4
- 🎯 "JoÃ£o" agora será exibido como "João" corretamente

### 🎯 Resultado
- SQL simplificado para evitar problemas de encoding ✅
- "Joao" ao invés de caracteres problemáticos ✅
- JSON_UNESCAPED_UNICODE adicionado na API ✅

## v2.4.2 - 2025-07-24
**Fix: Ordenação de grupos por execution_order**

### ✅ Correção Aplicada
- 📊 Grupos agora ordenados por menor execution_order das réguas internas
- 🔢 Grupos com execution_order aparecem primeiro, ordenados crescentemente
- ⚪ Grupos sem execution_order aparecem no final
- 🎯 Lógica: menor ordem → maior prioridade visual

### 🎯 Resultado
- Lead (execution_order: 1) → 1º lugar ✅
- Alunoativo (execution_order: 3) → 2º lugar ✅
- Grupos sem ordem → final ✅

## v2.4.1 - 2025-07-24
**Fix: Nomes das seções usando status original**

### ✅ Correção Aplicada
- 🏷️ Seções de agrupamento agora usam status original (ex: "Students" ao invés de "Estudantes")
- 🆓 Maior liberdade na criação de réguas sem traduções forçadas
- 📝 Lógica simplificada: primeira letra maiúscula do status real

### 🎯 Resultado
- "Lead" → "Lead" ✅
- "Students" → "Students" ✅ (antes era "Estudantes")
- "Alunoativo" → "Alunoativo" ✅

## v2.4.0 - 2025-07-24
**FASE 2.1 CONCLUÍDA - Integração Backend ↔ Frontend Funcionando**

### ✅ Integração Completa
- 🔗 Interface Vue.js conectada com API PHP real
- 🗄️ Banco MySQL funcionando com dados da especificação
- 📊 Agrupamento por status funcionando com dados reais
- 🔄 CRUD completo operacional (criar, editar, listar, ativar/desativar)

### 🔧 Correções Técnicas
- ✅ Problema de porta resolvido (8080 vs 8082) usando git intelligence
- ✅ Apache configurado para servir Vue.js SPA corretamente
- ✅ Volumes Docker mapeados para frontend build
- ✅ Dados reais da especificação: lead_captacaod0, students_inadimplentes

### 🎯 Funcionalidades Testadas
- API endpoints com novos campos (send_time_start, execution_order, etc.)
- Validação de nome formato status_substatus
- Template ID pequeno conforme especificação
- Agrupamento visual por status na interface

## v2.3.0 - 2025-07-24
**Interface Reorganizada Conforme Especificação**

### Reformulado
- ✅ Campo Template: pequeno input para ID (TPL_001, WELCOME_TEMPLATE)
- ✅ Canal de Comunicação: renomeado de "Tipo" com opções EMAIL/SMS/WHATSAPP
- ✅ Novos campos: send_time_start, send_time_end, execution_order
- ✅ Validação de nome: formato status_substatus obrigatório com feedback visual
- ✅ Agrupamento por status: réguas organizadas por prefixo do nome
- ✅ Botão "Testar SQL" preparado para implementação futura

### Removido
- ❌ Campo description removido conforme especificação
- ❌ Sugestões de {campo} no template removidas

### Interface Melhorada
- 🔢 Contadores por grupo de status
- 📊 Ordenação por execution_order depois por nome
- 🔴 Validação visual com campo vermelho para nomes inválidos
- 📝 Dicas contextuais para formato correto

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