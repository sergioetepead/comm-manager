<template>
  <div>
    <div class="page-header">
      <h2>Logs de Execução</h2>
      <router-link to="/rules" class="btn btn-secondary">← Voltar</router-link>
    </div>

    <div class="card">
      <div class="logs-header">
        <h3>Régua: {{ ruleName }}</h3>
        <div class="filters">
          <select v-model="statusFilter" class="form-control">
            <option value="">Todos os status</option>
            <option value="success">Sucesso</option>
            <option value="empty">Vazio</option>
            <option value="error">Erro</option>
          </select>
        </div>
      </div>

      <div class="table-container">
        <table class="logs-table">
          <thead>
            <tr>
              <th>Data/Hora</th>
              <th>Status</th>
              <th>Destinatários</th>
              <th>Tempo</th>
              <th>Ações</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="log in filteredLogs" :key="log.id">
              <td>{{ formatDateTime(log.rule_dispatch_timestamp) }}</td>
              <td>
                <span class="status-badge" :class="'status-' + log.execution_status">
                  {{ getStatusLabel(log.execution_status) }}
                </span>
              </td>
              <td>
                <span v-if="log.execution_status === 'success'">
                  {{ getRecipientCount(log.recipients) }} destinatário(s)
                </span>
                <span v-else-if="log.execution_status === 'empty'">
                  Nenhum destinatário
                </span>
                <span v-else class="text-danger">
                  Falha na execução
                </span>
              </td>
              <td>
                <span v-if="log.execution_time">
                  {{ log.execution_time.toFixed(2) }}s
                </span>
                <span v-else>-</span>
              </td>
              <td>
                <button @click="showDetails(log)" class="btn btn-sm">
                  Ver Detalhes
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Modal de Detalhes -->
    <div v-if="selectedLog" class="modal-overlay" @click="closeDetails">
      <div class="modal" @click.stop>
        <div class="modal-header">
          <h3>Detalhes da Execução</h3>
          <button @click="closeDetails" class="close-btn">&times;</button>
        </div>
        <div class="modal-body">
          <div class="detail-section">
            <h4>Informações Gerais</h4>
            <p><strong>Data/Hora:</strong> {{ formatDateTime(selectedLog.rule_dispatch_timestamp) }}</p>
            <p><strong>Status:</strong> {{ getStatusLabel(selectedLog.execution_status) }}</p>
            <p><strong>Tempo de Execução:</strong> {{ selectedLog.execution_time?.toFixed(2) || 'N/A' }}s</p>
          </div>

          <div v-if="selectedLog.recipients && selectedLog.recipients.length > 0" class="detail-section">
            <h4>Destinatários ({{ selectedLog.recipients.length }})</h4>
            <div class="recipients-list">
              <div v-for="(recipient, index) in selectedLog.recipients" :key="index" class="recipient-item">
                <strong>{{ recipient.first_name }}</strong> - {{ recipient.contact }}
                <pre class="payload">{{ JSON.stringify(recipient.payload, null, 2) }}</pre>
              </div>
            </div>
          </div>

          <div v-if="selectedLog.error_message" class="detail-section">
            <h4>Mensagem de Erro</h4>
            <pre class="error-message">{{ selectedLog.error_message }}</pre>
          </div>

          <div v-if="selectedLog.response_payload" class="detail-section">
            <h4>Resposta do Sistema de Mensageria</h4>
            <pre class="response-payload">{{ JSON.stringify(selectedLog.response_payload, null, 2) }}</pre>
          </div>
        </div>
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
  name: 'RuleLogs',
  props: {
    id: String
  },
  data() {
    return {
      logs: [],
      ruleName: '',
      loading: false,
      error: null,
      statusFilter: '',
      selectedLog: null
    }
  },
  computed: {
    filteredLogs() {
      if (!this.statusFilter) {
        return this.logs
      }
      return this.logs.filter(log => log.execution_status === this.statusFilter)
    }
  },
  async mounted() {
    await this.loadLogs()
  },
  methods: {
    async loadLogs() {
      this.loading = true
      this.error = null
      
      try {
        const response = await axios.get(`/api/communication-rules/${this.id}/logs`)
        if (response.data.status === 'success') {
          this.logs = response.data.data
          this.ruleName = response.data.rule_name || 'Régua não encontrada'
        } else {
          this.error = response.data.message || 'Erro ao carregar logs'
        }
      } catch (err) {
        this.error = 'Erro de conexão com a API'
        console.error('Erro ao carregar logs:', err)
      } finally {
        this.loading = false
      }
    },

    formatDateTime(datetime) {
      return new Date(datetime).toLocaleString('pt-BR')
    },

    getStatusLabel(status) {
      const labels = {
        success: 'Sucesso',
        empty: 'Vazio',
        error: 'Erro'
      }
      return labels[status] || status
    },

    getRecipientCount(recipients) {
      if (!recipients) return 0
      if (typeof recipients === 'string') {
        try {
          recipients = JSON.parse(recipients)
        } catch (e) {
          return 0
        }
      }
      return Array.isArray(recipients) ? recipients.length : 0
    },

    showDetails(log) {
      // Parse JSON strings if needed
      if (typeof log.recipients === 'string') {
        try {
          log.recipients = JSON.parse(log.recipients)
        } catch (e) {
          console.error('Erro ao parsear recipients:', e)
        }
      }
      if (typeof log.response_payload === 'string') {
        try {
          log.response_payload = JSON.parse(log.response_payload)
        } catch (e) {
          console.error('Erro ao parsear response_payload:', e)
        }
      }
      
      this.selectedLog = log
    },

    closeDetails() {
      this.selectedLog = null
    }
  }
}
</script>

<style scoped>
.logs-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
}

.filters {
  display: flex;
  gap: 1rem;
}

.filters .form-control {
  width: auto;
  min-width: 150px;
}

.logs-table {
  width: 100%;
  border-collapse: collapse;
}

.logs-table th,
.logs-table td {
  padding: 0.75rem 1rem;
  text-align: left;
  border-bottom: 1px solid #e2e8f0;
}

.logs-table th {
  background-color: #f7fafc;
  font-weight: 600;
  color: #4a5568;
}

.status-badge {
  display: inline-block;
  padding: 0.25rem 0.75rem;
  border-radius: 12px;
  font-size: 0.75rem;
  font-weight: 600;
}

.status-success {
  background-color: #c6f6d5;
  color: #22543d;
}

.status-empty {
  background-color: #faf089;
  color: #744210;
}

.status-error {
  background-color: #fed7d7;
  color: #742a2a;
}

.text-danger {
  color: #e53e3e;
}

/* Modal Styles */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.modal {
  background: white;
  border-radius: 8px;
  max-width: 800px;
  max-height: 90vh;
  width: 90%;
  overflow-y: auto;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.5rem;
  border-bottom: 1px solid #e2e8f0;
}

.close-btn {
  background: none;
  border: none;
  font-size: 1.5rem;
  cursor: pointer;
  color: #a0aec0;
}

.close-btn:hover {
  color: #2d3748;
}

.modal-body {
  padding: 1.5rem;
}

.detail-section {
  margin-bottom: 2rem;
}

.detail-section h4 {
  margin-bottom: 1rem;
  color: #2d3748;
}

.recipients-list {
  max-height: 300px;
  overflow-y: auto;
}

.recipient-item {
  padding: 1rem;
  border: 1px solid #e2e8f0;
  border-radius: 6px;
  margin-bottom: 1rem;
}

.payload,
.error-message,
.response-payload {
  background-color: #f7fafc;
  padding: 1rem;
  border-radius: 6px;
  overflow-x: auto;
  font-family: 'Courier New', monospace;
  font-size: 0.9rem;
  margin-top: 0.5rem;
}

.error-message {
  background-color: #fed7d7;
  color: #742a2a;
}

.loading,
.error {
  text-align: center;
  padding: 2rem;
}

.error {
  background-color: #fed7d7;
  color: #742a2a;
  border-radius: 6px;
}
</style>