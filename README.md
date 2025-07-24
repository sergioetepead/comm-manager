# Sistema de Gestão da Régua de Comunicação - ETEP

## Visão Geral
Sistema automatizado e flexível de gestão de réguas de comunicação para o Centro Universitário ETEP, responsável por identificar, classificar e organizar contatos com leads e alunos.

## Estrutura do Projeto
```
comm-manager/
├── docker-compose.yml          # Configuração dos containers
├── Dockerfile                  # Imagem PHP + Apache
├── src/                        # Código fonte da aplicação
│   ├── public/                 # Arquivos públicos
│   ├── api/                    # Endpoints da API REST
│   ├── config/                 # Configurações
│   ├── models/                 # Models de dados
│   ├── controllers/            # Controllers
│   └── services/               # Serviços de negócio
├── docker/                     # Configurações Docker
│   ├── apache/                 # Configuração Apache
│   └── mysql/init/             # Scripts iniciais do banco
├── frontend/                   # Interface Vue.js (Fase 2)
└── logs/                       # Logs da aplicação
```

## Pré-requisitos
- Docker Desktop instalado
- Porta 8080 (aplicação) e 8081 (PHPMyAdmin) disponíveis

## Como Executar

### 1. Subir o ambiente
```bash
# No diretório do projeto
docker-compose up -d --build
```

### 2. Verificar se os containers estão rodando
```bash
docker-compose ps
```

### 3. Acessar a aplicação
- **Aplicação Principal**: http://localhost:8080
- **PHPMyAdmin**: http://localhost:8081
  - Usuário: `root`
  - Senha: `root123`

### 4. Testar a API
```bash
# Testar conexão com o banco
curl http://localhost:8080/api/test

# Listar regras de comunicação
curl http://localhost:8080/api/communication-rules
```

## Comandos Úteis

### Parar os containers
```bash
docker-compose down
```

### Ver logs dos containers
```bash
# Todos os logs
docker-compose logs -f

# Logs específicos
docker-compose logs -f app
docker-compose logs -f db
```

### Executar comandos dentro do container
```bash
# Acessar o container da aplicação
docker-compose exec app bash

# Acessar o MySQL
docker-compose exec db mysql -u root -p comm_manager
```

## Status Atual - Fase 1: Foundation & Core Backend

### ✅ Concluído
- [x] Configuração Docker Compose (PHP 8.2 + Apache + MySQL 8.0)
- [x] Estrutura de diretórios do projeto
- [x] Schema do banco de dados (communication_rules, rule_dispatch_log)
- [x] Dados de seed para testes
- [x] Endpoint básico da API (/api/test, /api/communication-rules)
- [x] Configuração Apache com rewrite rules

### 🚧 Próximos Passos
- [ ] Implementar CRUD completo para communication_rules
- [ ] Sistema de versionamento de regras
- [ ] Validação de SQL syntax
- [ ] Soft delete (ativação/desativação)
- [ ] Interface Vue.js (Fase 2)

## Banco de Dados

### Tabelas Principais
- **communication_rules**: Armazena as regras de comunicação com versionamento
- **rule_dispatch_log**: Logs de execução das regras

### Dados de Exemplo
O sistema já vem com algumas regras de exemplo:
- `lead_captacaod0` (WhatsApp)
- `lead_nurturing2d5` (Email)
- `alunoativo_retencaopreventiva` (SMS)

## Troubleshooting

### Problema: Porta em uso
Se as portas 8080 ou 8081 estiverem em uso, altere no `docker-compose.yml`:
```yaml
ports:
  - "8082:80"  # Alterar 8080 para 8082
```

### Problema: Permissões no Windows
Se houver problemas de permissão, execute o PowerShell como administrador.

### Problema: Container não sobe
```bash
# Limpar containers e volumes
docker-compose down -v
docker system prune -f
docker-compose up -d --build
```