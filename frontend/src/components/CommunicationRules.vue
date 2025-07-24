<template>
  <div class="communication-rules">
    <div class="controls">
      <h2>Réguas de Comunicação</h2>
      <button @click="showForm = true" class="btn-primary">
        ➕ Nova Régua
      </button>
    </div>

    <RuleForm 
      v-if="showForm" 
      :rule="editingRule"
      @save="handleSave"
      @cancel="handleCancel"
    />

    <div class="rules-list">
      <div v-if="loading" class="loading">Carregando...</div>
      
      <div v-else-if="rules.length === 0" class="empty-state">
        <p>Nenhuma régua cadastrada ainda.</p>
      </div>
      
      <div v-else class="rules-grid">
        <div 
          v-for="rule in rules" 
          :key="rule.id" 
          class="rule-card"
          :class="{ inactive: !rule.active }"
        >
          <div class="rule-header">
            <h3>{{ rule.name }}</h3>
            <span class="status-badge" :class="rule.active ? 'active' : 'inactive'">
              {{ rule.active ? 'Ativa' : 'Inativa' }}
            </span>
          </div>
          
          <div class="rule-body">
            <p><strong>Tipo:</strong> {{ rule.type }}</p>
            <p><strong>SQL:</strong> <code>{{ rule.sql_query }}</code></p>
            <p><strong>Versão:</strong> {{ rule.version }}</p>
            <p><strong>Criada:</strong> {{ formatDate(rule.created_at) }}</p>
          </div>
          
          <div class="rule-actions">
            <button @click="editRule(rule)" class="btn-secondary">✏️ Editar</button>
            <button 
              @click="toggleRule(rule)" 
              :class="rule.active ? 'btn-warning' : 'btn-success'"
            >
              {{ rule.active ? '⏸️ Desativar' : '▶️ Ativar' }}
            </button>
            <button @click="viewLogs(rule)" class="btn-info">📊 Logs</button>
          </div>
        </div>
      </div>
    </div>

    <RuleLogs 
      v-if="showLogs" 
      :ruleId="selectedRuleId"
      @close="showLogs = false"
    />
  </div>
</template>

<script>
import axios from 'axios'
import RuleForm from './RuleForm.vue'
import RuleLogs from './RuleLogs.vue'

export default {
  name: 'CommunicationRules',
  components: {
    RuleForm,
    RuleLogs
  },
  data() {
    return {
      rules: [],
      loading: false,
      showForm: false,
      showLogs: false,
      editingRule: null,
      selectedRuleId: null
    }
  },
  mounted() {
    this.loadRules()
  },
  methods: {
    async loadRules() {
      this.loading = true
      try {
        const response = await axios.get('/api/communication-rules')
        console.log('API Response:', response.data)
        
        if (response.data.status === 'success') {
          this.rules = response.data.data
          console.log('Rules loaded:', this.rules)
        } else {
          this.rules = response.data
        }
      } catch (error) {
        console.error('Erro ao carregar réguas:', error)
        alert('Erro ao carregar réguas de comunicação')
      } finally {
        this.loading = false
      }
    },
    
    editRule(rule) {
      this.editingRule = { ...rule }
      this.showForm = true
    },
    
    async toggleRule(rule) {
      try {
        const endpoint = rule.active ? 'deactivate' : 'activate'
        await axios.post(`/api/communication-rules/${rule.id}/${endpoint}`)
        await this.loadRules()
        alert(`Régua ${rule.active ? 'desativada' : 'ativada'} com sucesso!`)
      } catch (error) {
        console.error('Erro ao alterar status:', error)
        alert('Erro ao alterar status da régua')
      }
    },
    
    viewLogs(rule) {
      this.selectedRuleId = rule.id
      this.showLogs = true
    },
    
    async handleSave(ruleData) {
      try {
        if (this.editingRule) {
          // Editar
          await axios.put(`/api/communication-rules/${this.editingRule.id}`, ruleData)
          alert('Régua atualizada com sucesso!')
        } else {
          // Criar
          await axios.post('/api/communication-rules', ruleData)
          alert('Régua criada com sucesso!')
        }
        
        await this.loadRules()
        this.handleCancel()
      } catch (error) {
        console.error('Erro ao salvar régua:', error)
        alert('Erro ao salvar régua')
      }
    },
    
    handleCancel() {
      this.showForm = false
      this.editingRule = null
    },
    
    formatDate(dateString) {
      if (!dateString) return 'N/A'
      
      try {
        // Handle MySQL datetime format: YYYY-MM-DD HH:MM:SS
        const date = new Date(dateString.replace(' ', 'T'))
        
        if (isNaN(date.getTime())) {
          return 'Data inválida'
        }
        
        return date.toLocaleDateString('pt-BR', {
          year: 'numeric',
          month: '2-digit',
          day: '2-digit'
        })
      } catch (error) {
        console.error('Erro ao formatar data:', error)
        return 'Data inválida'
      }
    }
  }
}
</script>

<style scoped>
.communication-rules {
  background: white;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
  overflow: hidden;
}

.controls {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.5rem;
  background-color: #f8f9fa;
  border-bottom: 1px solid #e9ecef;
}

.controls h2 {
  color: #495057;
  margin: 0;
}

.rules-list {
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

.rules-grid {
  display: grid;
  gap: 1rem;
  grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
}

.rule-card {
  border: 1px solid #e9ecef;
  border-radius: 8px;
  overflow: hidden;
  transition: transform 0.2s, box-shadow 0.2s;
}

.rule-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.rule-card.inactive {
  opacity: 0.7;
  background-color: #f8f9fa;
}

.rule-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem;
  background-color: #f8f9fa;
  border-bottom: 1px solid #e9ecef;
}

.rule-header h3 {
  margin: 0;
  color: #495057;
}

.status-badge {
  padding: 0.25rem 0.75rem;
  border-radius: 20px;
  font-size: 0.85rem;
  font-weight: 500;
}

.status-badge.active {
  background-color: #d4edda;
  color: #155724;
}

.status-badge.inactive {
  background-color: #f8d7da;
  color: #721c24;
}

.rule-body {
  padding: 1rem;
}

.rule-body p {
  margin: 0.5rem 0;
  font-size: 0.9rem;
}

.rule-body code {
  background-color: #f8f9fa;
  padding: 0.25rem 0.5rem;
  border-radius: 4px;
  font-family: 'Courier New', monospace;
  font-size: 0.8rem;
}

.rule-actions {
  display: flex;
  gap: 0.5rem;
  padding: 1rem;
  background-color: #f8f9fa;
  border-top: 1px solid #e9ecef;
}

.btn-primary, .btn-secondary, .btn-success, .btn-warning, .btn-info {
  padding: 0.5rem 1rem;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 0.85rem;
  transition: all 0.2s;
}

.btn-primary {
  background-color: #007bff;
  color: white;
}

.btn-primary:hover {
  background-color: #0056b3;
}

.btn-secondary {
  background-color: #6c757d;
  color: white;
}

.btn-secondary:hover {
  background-color: #545b62;
}

.btn-success {
  background-color: #28a745;
  color: white;
}

.btn-success:hover {
  background-color: #1e7e34;
}

.btn-warning {
  background-color: #ffc107;
  color: #212529;
}

.btn-warning:hover {
  background-color: #e0a800;
}

.btn-info {
  background-color: #17a2b8;
  color: white;
}

.btn-info:hover {
  background-color: #117a8b;
}
</style>