<template>
  <div>
    <div class="page-header">
      <h2>Réguas de Comunicação</h2>
      <router-link to="/rules/new" class="btn">+ Nova Régua</router-link>
    </div>

    <div class="card">
      <div class="table-container">
        <table class="rules-table">
          <thead>
            <tr>
              <th>Nome</th>
              <th>Canal</th>
              <th>Template ID</th>
              <th>Horário</th>
              <th>Status</th>
              <th>Ações</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="rule in rules" :key="rule.id">
              <td>
                <strong>{{ rule.name }}</strong>
                <small class="rule-id">ID: {{ rule.rule_id }}</small>
              </td>
              <td>
                <span class="channel-badge" :class="'channel-' + rule.channel.toLowerCase()">
                  {{ rule.channel }}
                </span>
              </td>
              <td>{{ rule.template_id }}</td>
              <td>
                <span v-if="rule.send_time_start && rule.send_time_end">
                  {{ rule.send_time_start }} - {{ rule.send_time_end }}
                </span>
                <span v-else class="text-muted">Sem restrição</span>
              </td>
              <td>
                <span class="status-indicator" :class="rule.active ? 'status-active' : 'status-inactive'">
                  {{ rule.active ? 'Ativa' : 'Inativa' }}
                </span>
              </td>
              <td>
                <div class="actions">
                  <router-link :to="`/rules/${rule.id}/edit`" class="btn btn-sm">Editar</router-link>
                  <router-link :to="`/rules/${rule.id}/logs`" class="btn btn-sm btn-secondary">Logs</router-link>
                  <button @click="toggleRule(rule)" class="btn btn-sm" :class="rule.active ? 'btn-danger' : 'btn-secondary'">
                    {{ rule.active ? 'Desativar' : 'Ativar' }}
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <div v-if="loading" class="loading">
      Carregando...
    </div>

    <div v-if="error" class="error">
      {{ error }}
    </div>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  name: 'CommunicationRules',
  data() {
    return {
      rules: [],
      loading: false,
      error: null
    }
  },
  async mounted() {
    await this.loadRules()
  },
  methods: {
    async loadRules() {
      this.loading = true
      this.error = null
      
      try {
        const response = await axios.get('/api/communication-rules')
        if (response.data.status === 'success') {
          this.rules = response.data.data
        } else {
          this.error = response.data.message || 'Erro ao carregar réguas'
        }
      } catch (err) {
        this.error = 'Erro de conexão com a API'
        console.error('Erro ao carregar réguas:', err)
      } finally {
        this.loading = false
      }
    },

    async toggleRule(rule) {
      try {
        const newStatus = !rule.active
        const endpoint = newStatus ? 'activate' : 'deactivate'
        
        await axios.post(`/api/communication-rules/${rule.id}/${endpoint}`)
        
        rule.active = newStatus
        
        this.$nextTick(() => {
          this.loadRules() // Recarregar lista para garantir consistência
        })
      } catch (err) {
        this.error = 'Erro ao alterar status da régua'
        console.error('Erro ao alterar status:', err)
      }
    }
  }
}
</script>

<style scoped>
.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
}

.page-header h2 {
  color: #2d3748;
  font-size: 1.75rem;
}

.table-container {
  overflow-x: auto;
}

.rules-table {
  width: 100%;
  border-collapse: collapse;
}

.rules-table th,
.rules-table td {
  padding: 0.75rem 1rem;
  text-align: left;
  border-bottom: 1px solid #e2e8f0;
}

.rules-table th {
  background-color: #f7fafc;
  font-weight: 600;
  color: #4a5568;
  font-size: 0.875rem;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.rules-table tbody tr:hover {
  background-color: #f7fafc;
}

.rule-id {
  display: block;
  color: #718096;
  font-size: 0.8rem;
  margin-top: 0.25rem;
}

.channel-badge {
  display: inline-block;
  padding: 0.25rem 0.75rem;
  border-radius: 12px;
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: uppercase;
}

.channel-whatsapp {
  background-color: #c6f6d5;
  color: #22543d;
}

.channel-email {
  background-color: #bee3f8;
  color: #2a4365;
}

.channel-sms {
  background-color: #fbb6ce;
  color: #702459;
}

.text-muted {
  color: #a0aec0;
  font-style: italic;
}

.actions {
  display: flex;
  gap: 0.5rem;
  flex-wrap: wrap;
}

.btn-sm {
  padding: 0.5rem 0.75rem;
  font-size: 0.8rem;
}

.loading {
  text-align: center;
  padding: 2rem;
  color: #718096;
}

.error {
  background-color: #fed7d7;
  color: #742a2a;
  padding: 1rem;
  border-radius: 6px;
  margin-top: 1rem;
}
</style>