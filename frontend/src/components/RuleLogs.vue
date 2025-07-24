<template>
  <div class="logs-overlay">
    <div class="logs-modal">
      <div class="modal-header">
        <h3>Logs de Execução</h3>
        <button @click="$emit('close')" class="btn-close">✕</button>
      </div>
      
      <div class="modal-body">
        <div v-if="loading" class="loading">Carregando logs...</div>
        
        <div v-else-if="logs.length === 0" class="empty-state">
          <p>Nenhum log encontrado para esta régua.</p>
        </div>
        
        <div v-else class="logs-list">
          <div v-for="log in logs" :key="log.id" class="log-item">
            <div class="log-header">
              <span class="log-date">{{ formatDateTime(log.executed_at) }}</span>
              <span class="log-status" :class="log.status">
                {{ getStatusText(log.status) }}
              </span>
            </div>
            
            <div class="log-details">
              <p><strong>Destinatários:</strong> {{ log.recipients_count || 'N/A' }}</p>
              <p><strong>Resultado:</strong> {{ log.result_summary || 'N/A' }}</p>
              
              <div v-if="log.error_message" class="log-error">
                <strong>Erro:</strong> {{ log.error_message }}
              </div>
              
              <div v-if="log.execution_details" class="log-json">
                <strong>Detalhes:</strong>
                <pre>{{ formatJson(log.execution_details) }}</pre>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  name: 'RuleLogs',
  props: {
    ruleId: {
      type: [String, Number],
      required: true
    }
  },
  emits: ['close'],
  data() {
    return {
      logs: [],
      loading: false
    }
  },
  mounted() {
    this.loadLogs()
  },
  methods: {
    async loadLogs() {
      this.loading = true
      try {
        const response = await axios.get(`/api/communication-rules/${this.ruleId}/logs`)
        this.logs = response.data
      } catch (error) {
        console.error('Erro ao carregar logs:', error)
        alert('Erro ao carregar logs da régua')
      } finally {
        this.loading = false
      }
    },
    
    formatDateTime(dateString) {
      return new Date(dateString).toLocaleString('pt-BR')
    },
    
    getStatusText(status) {
      const statusMap = {
        'success': 'Sucesso',
        'error': 'Erro',
        'partial': 'Parcial',
        'pending': 'Pendente'
      }
      return statusMap[status] || status
    },
    
    formatJson(jsonString) {
      try {
        return JSON.stringify(JSON.parse(jsonString), null, 2)
      } catch {
        return jsonString
      }
    }
  }
}
</script>

<style scoped>
.logs-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
}

.logs-modal {
  background: white;
  border-radius: 8px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
  width: 90%;
  max-width: 800px;
  max-height: 90vh;
  display: flex;
  flex-direction: column;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.5rem;
  background-color: #f8f9fa;
  border-bottom: 1px solid #e9ecef;
  border-radius: 8px 8px 0 0;
}

.modal-header h3 {
  margin: 0;
  color: #495057;
}

.btn-close {
  background: none;
  border: none;
  font-size: 1.5rem;
  cursor: pointer;
  color: #6c757d;
  padding: 0.25rem;
  line-height: 1;
}

.btn-close:hover {
  color: #495057;
}

.modal-body {
  flex: 1;
  overflow-y: auto;
  padding: 1.5rem;
}

.loading {
  text-align: center;
  padding: 2rem;
  color: #6c757d;
}

.empty-state {
  text-align: center;
  padding: 3rem;
  color: #6c757d;
}

.logs-list {
  space-y: 1rem;
}

.log-item {
  border: 1px solid #e9ecef;
  border-radius: 8px;
  overflow: hidden;
  margin-bottom: 1rem;
}

.log-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem;
  background-color: #f8f9fa;
  border-bottom: 1px solid #e9ecef;
}

.log-date {
  font-weight: 500;
  color: #495057;
}

.log-status {
  padding: 0.25rem 0.75rem;
  border-radius: 20px;
  font-size: 0.85rem;
  font-weight: 500;
}

.log-status.success {
  background-color: #d4edda;
  color: #155724;
}

.log-status.error {
  background-color: #f8d7da;
  color: #721c24;
}

.log-status.partial {
  background-color: #fff3cd;
  color: #856404;
}

.log-status.pending {
  background-color: #d1ecf1;
  color: #0c5460;
}

.log-details {
  padding: 1rem;
}

.log-details p {
  margin: 0.5rem 0;
  font-size: 0.9rem;
}

.log-error {
  background-color: #f8d7da;
  color: #721c24;
  padding: 0.75rem;
  border-radius: 4px;
  margin: 0.5rem 0;
}

.log-json {
  margin: 0.5rem 0;
}

.log-json pre {
  background-color: #f8f9fa;
  padding: 0.75rem;
  border-radius: 4px;
  overflow-x: auto;
  font-size: 0.8rem;
  font-family: 'Courier New', monospace;
}
</style>