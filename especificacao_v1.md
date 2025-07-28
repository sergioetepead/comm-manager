📘 Estrutura Geral da Especificação – Sistema de Gestão da Régua de Comunicação ETEP









## 1. Visão Geral do Sistema

### Objetivo do Sistema

Desenvolver um sistema automatizado e flexível de gestão de réguas de comunicação para o Centro Universitário ETEP, responsável por identificar, classificar e organizar contatos com leads e alunos, encaminhando diariamente as instruções de disparo para agentes externos.
 O sistema não realiza o envio das mensagens, apenas processa e entrega os dados prontos para comunicação, respeitando regras configuráveis e controladas por interface.



### Limites de Responsabilidade

#### Está no escopo:

Interface de cadastro e gerenciamento das réguas.

Execução controlada dos SQLs das réguas cadastradas.

Validação da estrutura de saída das réguas.

Entrega diária dos dados prontos para comunicação em formato padronizado para a aplicação de mensageria via API.

Registro de logs de execução e versionamento das réguas.

Armazenamento dos registros enviados para a aplicação de mensageria.

#### Fora do escopo:

Disparo das comunicações (WhatsApp, e-mail, SMS, etc.).

Manutenção ou operação de APIs externas de envio.

Criação dos conteúdos dos templates.

Atendimento ao aluno após o envio.



### Público-Alvo

Time de dados e marketing técnico: Pedro Balerine e Sérgio Fiúza.



### Fontes de Dados

BigQuery (fonte primária de dados acadêmicos, financeiros e comportamentais).

Sistema acadêmico Jacad, eventualmente, via API.



## 2. Arquitetura Funcional



### 2.1 Entradas

Define-se como uma communication_rule (régua de comunicação) o conjunto configurável dos seguintes 6 elementos:

SQL parametrizada

Deve retornar, no mínimo, os campos: first_name, contact, payload.

A SQL é responsável por filtrar os registros relevantes para o dia da execução (current_date) e aplicar toda a lógica de elegibilidade, personalização e duplicidade.

channel

Canal de comunicação a ser utilizado.

Ex: WHATSAPP, EMAIL, SMS.

template_id

Identificador do template de mensagem no sistema de mensageria.

Ex: 63f9a7e2b12abx458f.

name

Nome interno da régua, usado para organização e rastreamento (ex: reengajamento_ava_7dias).

send_time_start (opcional)

Horário mínimo permitido para disparo.

String no formato "HH:MM".

send_time_end (opcional)

Horário máximo permitido para disparo.

String no formato "HH:MM".

#### 📌 Fontes de dados

BigQuery é a fonte oficial de dados na versão inicial.

(Versão 2) poderá integrar fontes complementares como APIs dos sistemas acadêmico ou financeiro, a fim de ampliar as possibilidades de regras, especialmente quando os dados não estiverem completos no BigQuery.

⚠️ O sistema não interpreta dados brutos. Toda a lógica de filtragem, agrupamento, personalização e deduplicação é responsabilidade da SQL da communication_rule.



### 2.2 Processamento

O sistema executa diariamente todas as communication_rules ativas, preferencialmente durante a madrugada.

Para cada regra:

Executa a SQL associada no BigQuery.

Combina o resultado da consulta com os parâmetros fixos da communication_rule (channel, template_id, name, send_time_start, send_time_end).

Gera o daily_dispatch (disparo do dia): um pacote contendo todos os dados necessários para envio da comunicação.

Envia o daily_dispatch via API ao messaging_system da ETEP, que é o responsável por orquestrar os disparos por plataforma (Twilio, SendGrid, etc.).

#### 📦 rule_dispatch_log

Para cada envio realizado, é registrado um log contendo:

Cópia dos campos da communication_rule (name, channel, template_id, horários, etc.)

Resultado da SQL (lista de destinatários com first_name, contact, payload)

rule_dispatch_timestamp: data/hora do envio ao messaging_system

Status da execução, com três possibilidades:

success — pacote gerado e entregue com sucesso ao messaging_system

empty — SQL executada com sucesso, mas não retornou nenhum destinatário

error — falha ao tentar entregar o pacote ao messaging_system (ex: erro HTTP, falha de autenticação)

Resposta recebida do messaging_system (ex: sucesso, erro, ID de rastreio, etc.)



### 2.3 Saídas

O daily_dispatch, definido no processamento, é o pacote de dados enviado ao messaging_system da ETEP, responsável por efetivar os disparos de comunicação.

Ele inclui os parâmetros da communication_rule + os destinatários processados.

#### 📦 Formato da saída (JSON)

json

CopiarEditar

{

"channel": "WHATSAPP",

"template_id": "63f9a7e2b12abx458f",

"send_time_start": "09:00",

"send_time_end": "21:00",

"recipients": [

{

"first_name": "João",

"contact": "5512987654321",

"payload": {

"first_name": "João",

"due_date": "2025-05-12"

}

},

{

"first_name": "Maria",

"contact": "5512987654322",

"payload": {

"first_name": "Maria",

"due_date": "2025-05-15"

}

}

]

}



#### 🔎 Observações

O campo contact deve ser coerente com o channel:

Para WHATSAPP ou SMS: DDI + DDD + número (ex: 5512987654321)

Para EMAIL: endereço em caixa alta (ex: JOANA@GMAIL.COM)

O campo payload deve conter exatamente os campos esperados pelo template_id correspondente, respeitando o formato exigido pelo sistema de mensageria.

## 3. Gestão das communication_rules

### 3.1 Organização e estrutura das regras

Cada communication_rule é uma unidade independente de lógica de envio, composta por:

Um identificador interno: rule_id (gerado automaticamente pelo sistema, sequencial e único)

Um identificador declarativo: name (definido pelo usuário, obrigatório e padronizado)

Uma SQL parametrizada que retorna os destinatários e seus dados personalizados

Parâmetros fixos: channel, template_id, send_time_start, send_time_end

As regras são executadas de forma paralela e isolada, mesmo quando atingem o mesmo destinatário.



### 3.2 Padrão de nomenclatura (name)

O campo name deve seguir obrigatoriamente o padrão:

ini

CopiarEditar

name = status + "_" + substatus



#### 📌 Regras de normalização:

status e substatus vêm da planilha de referência

Espaços são removidos

Acentos, cedilhas e símbolos são eliminados

Tudo convertido para minúsculas

O único underscore permitido é o que separa status de substatus

#### 🧪 Exemplos:

| Status | Etapa/Substatus | name |
| --- | --- | --- |
| Lead | Captação (D0) | lead_captacaod0 |
| Lead | Nurturing 2 (D+5) | lead_nurturing2d5 |
| Aluno Ativo | Retenção Preventiva | alunoativo_retencaopreventiva |
| Inadimplente | Atraso D+7 | inadimplente_atrasod7 |




### 3.3 Boas práticas na construção das SQLs

As SQLs devem ser autônomas, autoexplicativas e alinhadas ao comportamento esperado do sistema:

O resultado da SQL deve conter apenas os registros cuja comunicação deve ser enviada no dia da execução (current_date)

Os campos obrigatórios de saída são: first_name, contact, payload

Toda a lógica de elegibilidade, deduplicação, agrupamento e personalização deve estar contida na própria query

A SQL deve ser testável, clara e validável pela interface do sistema antes de ser ativada



### 3.4 Ativação e desativação de regras

Cada communication_rule possui um status ativo/inativo controlado pela interface administrativa

A desativação de uma regra executa um soft delete: a regra permanece registrada no sistema, mas não é considerada nas execuções automáticas

A interface permite:

Visualizar separadamente regras ativas e inativas

Reativar regras inativas a qualquer momento

Não é possível excluir uma regra de forma definitiva. Toda regra criada permanece registrada com seu rule_id, mesmo após inativação, garantindo rastreabilidade e histórico completo



### 3.5 Expansão futura (V2)

Estão previstas, para versões futuras:

Agrupamento de regras por tipo (ex: engajamento, cobrança, captação)

Priorização ou exclusão mútua entre regras

Encadeamento lógico de regras (ex: "só disparar a B se A não foi enviada")



### 3.6 Armazenamento das regras

As communication_rules são persistidas em banco de dados do sistema como registros versionados. Cada vez que o usuário salva uma regra (mesmo com o mesmo rule_id), uma nova versão é criada com um novo created_at.

A versão mais recente de cada rule_id é a considerada para execução automática. Versões anteriores são preservadas e podem ser consultadas na interface administrativa.

#### 📦 Campos obrigatórios por versão:

rule_id (chave primária lógica, associada ao conceito de regra)

name (obrigatório, único no sistema)

sql (texto da query SQL)

channel

template_id

send_time_start (opcional)

send_time_end (opcional)

execution_order (opcional; numérico para fins de ordenação visual)

active (booleano; indica se a versão está ativa ou foi desativada pelo usuário)

created_at (timestamp automático ao salvar a versão)

superseded (booleano; true se houver versão mais recente para o mesmo rule_id)

A interface só mostra para edição a versão mais recente de cada rule_id, mas mantém acesso de leitura ao histórico completo de versões.

## 4. Interface de Administração

### 4.1 Objetivo

A interface de administração é o ponto central para cadastrar, editar, testar, ativar/desativar e acompanhar a execução das communication_rules. Ela permite ao usuário operar com segurança e autonomia, mantendo rastreabilidade completa.



### 4.2 Funcionalidades principais

#### 4.2.1 Cadastro e edição de regras

O usuário pode cadastrar uma nova communication_rule preenchendo:

name (manual)

sql

channel

template_id

send_time_start, send_time_end (opcional)

execution_order (opcional)

Ao salvar, o sistema:

Gera novo rule_id (se for uma regra nova) ou reaproveita o existente (em caso de edição)

Cria uma nova versão da regra (created_at)

Marca versões anteriores como superseded = true



#### 4.2.2 Teste de saúde da SQL

O usuário pode acionar um botão de "testar SQL" a qualquer momento

Além disso, o sistema testa automaticamente a SQL ao tentar salvar

O teste retorna:

Erro: SQL não executa (ex: sintaxe inválida)

Inadequação: SQL executa, mas o resultado não contém os campos obrigatórios (first_name, contact, payload)

O resultado é exibido imediatamente na interface



#### 4.2.3 Ativação e desativação (soft delete)

O usuário pode ativar ou desativar uma regra por botão

A desativação marca a versão como active = false (soft delete)

A listagem distingue claramente entre:

Regras ativas (executadas diariamente)

Regras inativas (podem ser reativadas)

Não é possível excluir uma regra em definitivo



#### 4.2.4 Visualização por grupos e ordenação

A interface exibe as regras agrupadas por status (derivado do prefixo de name)

Dentro de cada grupo, as regras são ordenadas por execution_order (quando presente), e em seguida por name

O campo execution_order é editável e serve apenas para fins visuais de organização da régua



#### 4.2.5 Histórico de versões

Para cada rule_id, a interface mostra:

A versão atual (mais recente created_at)

Todas as versões anteriores, em modo leitura

Permite comparar versões e consultar modificações anteriores





#### 4.2.6 Execuções diárias (visualização tipo painel de orquestração)

A interface oferece uma visualização consolidada das execuções diárias das regras de comunicação, em formato de grade cruzando dias e regras.

Cada linha representa um dia de execução, e cada coluna representa uma communication_rule.

O cruzamento de uma regra com um dia resulta em uma bolinha de status, com uma das seguintes cores:

| Cor | Significado |
| --- | --- |
| 🟢 Verde | A regra foi executada com sucesso e o daily_dispatch foi entregue ao messaging_system |
| 🔴 Vermelho | A execução foi tentada, mas a entrega falhou (ex: erro de rede, falha HTTP) |
| 🔵 Azul | A execução foi feita, mas a SQL não retornou nenhum registro (nenhum aluno elegível naquele dia) |
| ⚪ Cinza | O sistema não tentou executar essa regra naquele dia (ex: falha de pipeline, regra recém-criada, sistema desligado) |


Essa visualização tem como objetivo permitir que o usuário monitore rapidamente o comportamento diário do sistema como um todo, identificando falhas, dias inativos ou ausência de resultados de forma agregada.

O usuário pode clicar sobre qualquer bolinha para abrir o log técnico detalhado (rule_dispatch_log) referente àquele disparo (se houver).







## 5. Integração com Sistemas Externos



### 5.1 Integração com BigQuery

O sistema executa diariamente as SQLs das communication_rules diretamente no BigQuery, que atua como fonte oficial de dados acadêmicos, financeiros e comportamentais.

#### Requisitos da integração:

A conexão é realizada por meio de uma conta de serviço autenticada, com permissões restritas à execução de queries e leitura de dados.

As queries são pré-cadastradas como parte das regras e executadas sob demanda conforme o ciclo diário.

A execução deve:

Retornar os campos obrigatórios: first_name, contact, payload

Estar limitada a uma janela de tempo razoável para prevenir timeouts

Gerar logs de erro, quando falhar, para consulta posterior

O sistema não altera nem escreve dados no BigQuery, apenas consulta.



### 5.2 Integração com o messaging_system

Após processar cada communication_rule, o sistema gera um daily_dispatch e o envia ao messaging_system, que é o responsável por realizar os disparos de mensagens (via Twilio, SendGrid ou outros canais).

#### Requisitos da integração:

O envio é feito via requisição HTTP (POST) para um endpoint do messaging_system, em formato JSON padronizado.

O corpo da requisição contém:

channel, template_id, send_time_start, send_time_end, recipients

A autenticação deve ser feita por cabeçalho HTTP (ex: Authorization: Bearer <token>) ou outro mecanismo acordado.

O sistema registra:

A tentativa de envio

A resposta recebida do messaging_system (ex: 200 OK, 500 Internal Server Error)

Não há tentativas de reenvio automático.
 Cada execução é única e seu resultado é registrado no rule_dispatch_log.



### 5.3 Integração com Google para login institucional

O acesso à interface administrativa do sistema deve ser restrito a usuários autenticados com contas Google vinculadas ao domínio institucional @etepead.com.br.

#### Requisitos da integração:

A autenticação será feita por meio do protocolo OAuth 2.0, via Google Identity.

Apenas contas com e-mails no domínio @etepead.com.br poderão acessar o sistema.

A aplicação deve validar o domínio do e-mail após autenticação, e negar acesso a qualquer usuário externo.

O sistema não gerencia senhas diretamente. Toda autenticação é delegada ao provedor Google.

## 6. Automação e Agendamento

### 6.1 Modos de operação e disponibilidade / agendamento

O sistema opera continuamente em dois modos complementares:

Modo de Edição: disponível a qualquer momento para cadastro, edição, teste e ativação/desativação de communication_rules.
 Não há restrição de horário para uso da interface administrativa.

Modo de Execução Automática: processo diário responsável por processar e entregar os daily_dispatch ao sistema de mensageria, com base nas regras ativas.
 Essa execução é realizada de forma agendada, sem intervenção manual.



### 6.2 Janela de execução do modo de execução automática

A execução automática ocorre uma vez por dia, dividida em duas fases:

| Fase | Horário sugerido | Descrição |
| --- | --- | --- |
| Execução das SQLs | Início da madrugada (ex: 01:00) | Executa todas as communication_rules ativas. Cada SQL é avaliada individualmente e pode ou não gerar destinatários. |
| Envio dos dispatches | Até o início da manhã (ex: 06:00) | Cada daily_dispatch gerado é enviado ao messaging_system. Se a regra não retornar destinatários, nada é enviado. |


A separação entre execução e envio tem como objetivo garantir que todos os dados estejam prontos antes do momento de disparo.



### 6.3 Prioridade e ordenação

Não há critérios de priorização ou ordenação entre regras.

Todas as regras ativas são executadas uma vez por dia

O sistema não aplica nenhuma lógica de exclusão mútua, prioridade ou dependência entre communication_rules

Esse comportamento poderá ser revisado em versões futuras, caso haja necessidade de agrupamento ou controle de colisão entre regras.



### 6.4 Monitoramento de performance (V2)

A V1 do sistema não armazena o tempo de execução das queries individuais nem o tempo total do processo automático.

Esse monitoramento poderá ser incluído futuramente, com os seguintes objetivos:

Medir tempo de execução de cada communication_rule

Alertar sobre execuções lentas ou fora da janela prevista

Acompanhar o tempo total de execução do ciclo diário

## 7. Especificação Técnica do Sistema

### 7.1 Arquitetura Geral da Aplicação

#### 7.1.1 Visão Geral

O sistema será desenvolvido como um novo módulo containerizado dentro da infraestrutura Docker já utilizada pela ETEP. Ele seguirá os padrões arquiteturais vigentes, integrando-se à aplicação administrativa existente, tanto no backend quanto no frontend.

#### 7.1.2 Padrão Arquitetural Aplicado

| Camada | Tecnologia / Convenção |
| --- | --- |
| Frontend | Vue.js 3 + Vite + Vue Router |
| Backend | PHP 8.x + CakePHP Database (ORM customizado) |
| Infraestrutura | Docker + Apache + MySQL containerizado |
| Autenticação | Google OAuth (via vue3-google-login) |
| API Externa | BigQuery, messaging_system |


#### 7.1.3 Containers e Domínios

prod-apache: Servirá os endpoints da API em api.etepead.com.br/communication-rules

prod-db: Reaproveita o MySQL existente, adicionando novas tabelas

admin.etepead.com.br: Novo módulo Vue no SPA para interface de administração das regras



### 7.2 Banco de Dados

#### 7.2.1 Tabela communication_rules

| Campo | Tipo | Descrição |
| --- | --- | --- |
| id | INT (PK) | Identificador único da versão |
| rule_id | VARCHAR | Identificador lógico da regra (mesmo após edição) |
| name | VARCHAR | Nome padronizado (status_substatus) |
| sql | TEXT | SQL parametrizada a ser executada |
| channel | VARCHAR | WHATSAPP / EMAIL / SMS |
| template_id | VARCHAR | ID do template da mensageria |
| send_time_start | VARCHAR | HH:MM (opcional) |
| send_time_end | VARCHAR | HH:MM (opcional) |
| execution_order | INT | Ordem visual (opcional) |
| active | BOOLEAN | Regra está ativa? |
| created_at | DATETIME | Timestamp da criação da versão |
| superseded | BOOLEAN | Esta versão foi substituída? |


📌 Todas as regras ativas são executadas diariamente. O SQL deve decidir internamente, via current_date, se há ou não destinatários no dia.

#### 7.2.2 Tabela rule_dispatch_log

Cada execução de uma regra gera um log com cópia exata da versão da regra e os destinatários enviados.

| Campo | Tipo | Descrição |
| --- | --- | --- |
| id | INT (PK) | Identificador do log |
| rule_dispatch_timestamp | DATETIME | Data/hora da execução |
| rule_snapshot | JSON | Cópia dos parâmetros da regra no momento da execução |
| recipients | JSON | Lista completa com first_name, contact, payload |
| execution_status | ENUM | success, empty, error |
| response_payload | JSON | Resposta do messaging_system |
| execution_time | FLOAT | Tempo de execução da SQL (segundos) |
| error_message | TEXT | Mensagem de erro, se houver |




### 7.3 Backend – API REST

#### 7.3.1 Endpoints Propostos

| Método | Endpoint | Descrição |
| --- | --- | --- |
| GET | /communication-rules/active | Lista regras ativas |
| POST | /communication-rules | Cria nova regra |
| PUT | /communication-rules/:id | Edita regra existente (cria nova versão) |
| POST | /communication-rules/test | Testa SQL da regra |
| POST | /communication-rules/:id/activate | Ativa regra |
| POST | /communication-rules/:id/deactivate | Desativa regra |
| GET | /communication-rules/:id/logs | Consulta histórico de dispatch |
| POST | /run-executions | Executa todas as SQLs (fase 1) |
| POST | /run-dispatches | Envia todos os daily_dispatch (fase 2) |


#### 7.3.2 Autenticação

Middleware OAuth Google para validação de domínio @etepead.com.br

Autorização apenas a usuários autenticados e com perfil técnico

Token de API para envio ao messaging_system via variável de ambiente



### 7.4 Frontend – SPA Admin (Vue.js)

#### 7.4.1 Módulo Vue: views/communicationRules/

| Arquivo Vue | Função |
| --- | --- |
| Index.vue | Lista todas as regras |
| Form.vue | Cadastro e edição da regra |
| Log.vue | Visualização do histórico de execução |
| Matrix.vue | Painel cruzado de dias x regras |
| components/StatusDot.vue | Indicadores 🔴🟢🔵⚪ por status |


#### 7.4.2 Plugins

| Plugin | Função |
| --- | --- |
| communicationApi.js | Comunicação REST com backend |
| auth.js | Verificação OAuth com domínio |
| schedulerApi.js | Integração com rotina de execução |




### 7.5 Execução e Agendamento

#### 7.5.1 Lógica de Execução

Todas as regras ativas são executadas diariamente

A lógica de elegibilidade está 100% encapsulada no SQL

O sistema não interpreta nem filtra registros fora da consulta

#### 7.5.2 Fases do Agendamento

| Fase | Horário sugerido | Descrição |
| --- | --- | --- |
| Execução das regras | 01:00 | Executa todas as SQLs, gera os daily_dispatch |
| Envio das comunicações | Até 06:00 | Envia os pacotes de dados ao messaging_system via API |


Ambas as fases devem ser scripts separados e agendados no ambiente produtivo.



### 7.6 Integrações

| Sistema | Tipo | Função |
| --- | --- | --- |
| BigQuery | Leitura | Execução das SQLs das regras |
| messaging_system | Escrita | Recebimento dos daily_dispatch |
| Google OAuth | Login | Acesso à interface administrativa |
| Docker Compose | Infra | Execução local dos containers |




### 7.7 Segurança

SQL Injection: toda SQL será testada na criação/edição

OAuth obrigatório: acesso restrito via conta institucional

Dados sensíveis protegidos: contact, payload e logs não são expostos no frontend

Versionamento forçado: toda edição cria uma nova versão com histórico preservado



### 7.8 Ambiente de Desenvolvimento

#### 7.8.1 Diretriz Geral

O sistema deve ser desenvolvido localmente, com base Docker, banco local (MySQL) e mocks para integrações externas.

Não utilizar BigQuery, OAuth ou sistema de mensageria reais na V1

A integração será feita apenas após o sistema estar testado e validado localmente

#### 7.8.2 Stack mínima para desenvolvimento

| Serviço | Componente |
| --- | --- |
| Backend API | PHP 8.x com MVC custom |
| Frontend | Vue.js 3 com Vite |
| Banco local | MySQL containerizado |
| Infraestrutura | Docker Compose |
| Logs | Arquivos em volume local |




## 8. Glossário

### 📌 Termos de Domínio e Negócio

| Termo | Definição |
| --- | --- |
| communication_rule | Unidade de configuração lógica que define uma régua de comunicação com SQL, canal, template e faixa horária. |
| daily_dispatch | Pacote JSON gerado diariamente contendo os dados a serem enviados ao sistema de mensageria. |
| rule_id | Identificador lógico e fixo de uma regra. Todas as versões de uma mesma regra compartilham o mesmo rule_id. |
| name | Nome declarativo da regra, seguindo o padrão status_substatus. |
| status/substatus | Identificadores padronizados derivados da categorização dos leads/alunos. |
| canal / channel | Meio pelo qual a comunicação será enviada: WHATSAPP, EMAIL ou SMS. |
| template_id | Identificador do template cadastrado no sistema de mensageria, utilizado para personalização. |
| payload | Objeto JSON com os dados personalizados esperados pelo template, por destinatário. |




### 🛠️ Termos Técnicos e Arquiteturais

| Termo | Definição |
| --- | --- |
| SQL parametrizada | Query configurada pelo usuário para buscar registros elegíveis à comunicação. |
| current_date | Data da execução da SQL, usada como referência para elegibilidade. |
| BigQuery | Data warehouse utilizado como fonte oficial de dados para execução das queries. |
| messaging_system | Aplicação externa que recebe os daily_dispatch e realiza os envios via canais integrados. |
| rule_dispatch_log | Registro persistente de cada execução de uma regra com os dados enviados e o status da operação. |
| superseded | Flag que indica que uma versão foi substituída por uma mais recente. |
| created_at | Timestamp de criação da versão de uma regra. |
| execution_status | Resultado da execução: success, empty (sem destinatários) ou error. |
| execution_order | Campo opcional para definir a ordem de exibição das regras na interface. |




### 🖥️ Componentes da Aplicação

| Termo | Definição |
| --- | --- |
| Vue.js | Framework JavaScript utilizado para construir o frontend da interface administrativa. |
| Vite | Ferramenta de build e desenvolvimento para aplicações Vue.js. |
| SPA | Single Page Application — arquitetura usada no painel admin do sistema. |
| PHP 8.x | Linguagem e versão do backend utilizada na aplicação da ETEP. |
| CakePHP ORM | Biblioteca ORM usada para abstração de banco no backend em PHP. |
| Docker / Docker Compose | Tecnologia de containerização usada no ambiente de desenvolvimento e produção. |
| Apache | Servidor web usado como reverse proxy e executor de requisições PHP. |
| MySQL | Banco de dados relacional usado para armazenar regras, versões e logs. |




### 🔐 Segurança e Acesso

| Termo | Definição |
| --- | --- |
| Google OAuth | Sistema de autenticação federada utilizado para login institucional. |
| @etepead.com.br | Domínio restrito para acesso à interface administrativa. |
| soft delete | Estratégia em que registros são marcados como inativos mas não são excluídos fisicamente. |
| validação de SQL | Processo automatizado que testa a sintaxe e formato da SQL antes da ativação. |
| autenticação via token | Mecanismo de segurança para o envio ao messaging_system. |




### 📊 Interface e Visualização

| Termo | Definição |
| --- | --- |
| Form.vue | Componente Vue responsável pelo cadastro e edição de regras. |
| Log.vue | Tela de exibição do histórico de execuções (rule_dispatch_log). |
| Matrix.vue | Painel cruzado com dias e regras, com status por bolinha colorida. |
| StatusDot.vue | Componente visual que representa o status de execução diário. |
| 🟢 / 🔴 / 🔵 / ⚪ | Indicadores de execução: sucesso, erro, sem destinatários, não executado. |


