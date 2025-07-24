# Plano de Implementação - Sistema de Gestão da Régua de Comunicação ETEP

## **FASE 1: Foundation & Core Backend**
**MVP: Sistema básico funcional localmente**

### 1.1 Infraestrutura Base
- Setup Docker Compose (PHP 8.x + Apache + MySQL)
- Estrutura de diretórios seguindo padrão ETEP
- Configuração básica de ambiente de desenvolvimento

### 1.2 Database Schema
- Criar tabelas `communication_rules` e `rule_dispatch_log`
- Scripts de migração e seed para dados de teste
- Validar estrutura com dados mockados

### 1.3 Backend API Core
- Endpoints básicos CRUD para `communication_rules`
- Sistema de versionamento (rule_id + versioning)
- Validação de SQL syntax (mock inicialmente)
- Soft delete (active/inactive)

---

## **FASE 2: Interface Administrativa**
**MVP: CRUD completo via interface web**

### 2.1 Frontend Vue.js Setup
- Estrutura SPA básica integrada ao admin existente
- Componentes base: Form.vue, Index.vue
- Integração com API backend

### 2.2 Funcionalidades Core UI
- Cadastro/edição de regras
- Listagem com agrupamento por status
- Ativação/desativação (soft delete)
- Teste básico de SQL (syntax validation)

### 2.3 Histórico e Versionamento
- Visualização de versões anteriores
- Comparação entre versões

---

## **FASE 3: Execução e Processamento**
**MVP: Execução automática das regras**

### 3.1 Mock de Integrações
- BigQuery simulator (arquivo JSON com dados de teste)
- Messaging System mock (log local das requisições)
- Estrutura de daily_dispatch

### 3.2 Engine de Execução
- Script de execução das regras ativas
- Geração de daily_dispatch
- Sistema de logs (rule_dispatch_log)
- Tratamento de erros e status

### 3.3 Agendamento
- Scripts separados para fase 1 (execução) e fase 2 (envio)
- Estrutura para integração com cron/scheduler

---

## **FASE 4: Monitoramento e Visualização**
**MVP: Dashboard de acompanhamento**

### 4.1 Interface de Logs
- Log.vue - visualização detalhada dos dispatches
- Filtros por data, regra, status

### 4.2 Dashboard Matrix
- Matrix.vue - painel cruzado dias x regras
- StatusDot.vue - indicadores coloridos
- Drill-down para logs específicos

---

## **FASE 5: Integrações Reais e Deploy**
**MVP: Sistema em produção**

### 5.1 Integração BigQuery
- Substituir mock por conexão real
- Testes de performance e timeout
- Validação de estrutura de dados

### 5.2 Integração Messaging System
- API real para envio de daily_dispatch
- Autenticação e tratamento de erros
- Retry logic se necessário

### 5.3 Autenticação Google OAuth
- Integração com domínio @etepead.com.br
- Middleware de autorização
- Testes de segurança

### 5.4 Deploy e Monitoramento
- Configuração de produção
- Scripts de backup/restore
- Monitoring básico

---

## **Vantagens desta Abordagem:**

1. **Testabilidade**: Cada fase produz um sistema funcionalmente testável
2. **Redução de Riscos**: Integrações complexas ficam para o final
3. **Feedback Rápido**: Interface utilizável desde a Fase 2
4. **Paralelização**: UI pode ser desenvolvida em paralelo com backend
5. **Rollback Fácil**: Cada fase é um checkpoint estável

---

## **Próximos Passos:**
- Começar pela Fase 1: Infraestrutura Base
- Criar estrutura Docker Compose
- Implementar database schema
- Desenvolver API backend core