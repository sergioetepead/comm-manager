<template>
  <div class="rule-form-overlay">
    <div class="rule-form">
      <div class="form-header">
        <h3>{{ rule ? 'Editar Régua' : 'Nova Régua de Comunicação' }}</h3>
        <button @click="$emit('cancel')" class="btn-close">✕</button>
      </div>
      
      <form @submit.prevent="handleSubmit" class="form-body">
        <div class="form-group">
          <label for="name">Nome da Régua *</label>
          <input 
            v-model="formData.name"
            type="text" 
            id="name"
            required
            :class="{ 'invalid': !isValidName }"
            placeholder="Ex: leads_novos, students_inadimplentes"
          >
          <small class="form-help">
            Use o formato <strong>status_substatus</strong> (ex: leads_novos, students_ativos, pagamentos_vencidos)
          </small>
          <small v-if="!isValidName && formData.name" class="form-error">
            ❌ Nome deve seguir o formato status_substatus (letras minúsculas e underscore)
          </small>
        </div>
        
        <div class="form-group">
          <label for="type">Canal de Comunicação *</label>
          <select v-model="formData.type" id="type" required>
            <option value="">Selecione o canal</option>
            <option value="EMAIL">E-mail</option>
            <option value="SMS">SMS</option>
            <option value="WHATSAPP">WhatsApp</option>
          </select>
        </div>
        
        <div class="form-group">
          <label for="sql_query">Consulta SQL *</label>
          <textarea 
            v-model="formData.sql_query"
            id="sql_query"
            required
            rows="6"
            placeholder="SELECT * FROM students WHERE status = 'active'"
          ></textarea>
          <small class="form-help">
            Query SQL que define os destinatários desta régua de comunicação
          </small>
        </div>
        
        <div class="form-group">
          <label for="message_template">Template ID *</label>
          <input 
            v-model="formData.message_template"
            type="text"
            id="message_template"
            required
            placeholder="TPL_001, BOAS_VINDAS_001, PAG_LEMBRETE_002"
          >
          <small class="form-help">
            ID do template no Twilio/SendGrid (ex: TPL_001, WELCOME_TEMPLATE)
          </small>
        </div>
        
        <div class="form-row">
          <div class="form-group">
            <label for="send_time_start">Horário Início</label>
            <input 
              v-model="formData.send_time_start"
              type="time"
              id="send_time_start"
              placeholder="08:00"
            >
          </div>
          <div class="form-group">
            <label for="send_time_end">Horário Fim</label>
            <input 
              v-model="formData.send_time_end"
              type="time"
              id="send_time_end"
              placeholder="18:00"
            >
          </div>
        </div>
        
        <div class="form-group">
          <label for="execution_order">Ordem de Execução</label>
          <input 
            v-model.number="formData.execution_order"
            type="number"
            id="execution_order"
            min="1"
            placeholder="1"
          >
          <small class="form-help">
            Define a ordem de execução dentro do mesmo grupo (opcional)
          </small>
        </div>
        
        <div class="form-actions">
          <button type="button" @click="testSQL" class="btn-test" :disabled="!formData.sql_query">
            🧪 Testar SQL
          </button>
          <div class="action-buttons">
            <button type="button" @click="$emit('cancel')" class="btn-secondary">
              Cancelar
            </button>
            <button type="submit" class="btn-primary" :disabled="!isValidName">
              {{ rule ? 'Atualizar' : 'Criar' }} Régua
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
export default {
  name: 'RuleForm',
  props: {
    rule: {
      type: Object,
      default: null
    }
  },
  emits: ['save', 'cancel'],
  data() {
    return {
      formData: {
        name: '',
        type: '',
        sql_query: '',
        message_template: '',
        send_time_start: '',
        send_time_end: '',
        execution_order: null
      },
      sqlTestResult: null
    }
  },
  mounted() {
    if (this.rule) {
      this.formData = { ...this.rule }
    }
  },
  computed: {
    isValidName() {
      if (!this.formData.name) return true // Allow empty for validation message
      // Regex: letters, numbers, underscore, must contain at least one underscore
      return /^[a-z0-9]+_[a-z0-9_]*$/.test(this.formData.name)
    }
  },
  methods: {
    handleSubmit() {
      if (!this.isValidName) {
        alert('Nome deve seguir o formato status_substatus')
        return
      }
      this.$emit('save', this.formData)
    },
    
    async testSQL() {
      if (!this.formData.sql_query) return
      
      try {
        // TODO: Implement API call to test SQL
        alert('Funcionalidade de teste SQL será implementada na próxima fase')
      } catch (error) {
        console.error('Erro ao testar SQL:', error)
        alert('Erro ao testar SQL')
      }
    }
  }
}
</script>

<style scoped>
.rule-form-overlay {
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

.rule-form {
  background: white;
  border-radius: 8px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
  width: 90%;
  max-width: 600px;
  max-height: 90vh;
  overflow-y: auto;
}

.form-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.5rem;
  background-color: #f8f9fa;
  border-bottom: 1px solid #e9ecef;
}

.form-header h3 {
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

.form-body {
  padding: 1.5rem;
}

.form-group {
  margin-bottom: 1.5rem;
}

.form-row {
  display: flex;
  gap: 1rem;
  margin-bottom: 1.5rem;
}

.form-row .form-group {
  flex: 1;
  margin-bottom: 0;
}

.form-group label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 500;
  color: #495057;
}

.form-group input,
.form-group select,
.form-group textarea {
  width: 100%;
  padding: 0.75rem;
  border: 1px solid #ced4da;
  border-radius: 4px;
  font-size: 1rem;
  transition: border-color 0.2s;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
  outline: none;
  border-color: #007bff;
  box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.form-group textarea {
  resize: vertical;
  font-family: 'Courier New', monospace;
  font-size: 0.9rem;
}

.form-help {
  display: block;
  margin-top: 0.25rem;
  font-size: 0.85rem;
  color: #6c757d;
}

.form-actions {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-top: 1rem;
  border-top: 1px solid #e9ecef;
}

.action-buttons {
  display: flex;
  gap: 1rem;
}

.btn-primary, .btn-secondary {
  padding: 0.75rem 1.5rem;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 1rem;
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

.btn-test {
  padding: 0.75rem 1.5rem;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 1rem;
  transition: all 0.2s;
  background-color: #17a2b8;
  color: white;
}

.btn-test:hover:not(:disabled) {
  background-color: #117a8b;
}

.btn-test:disabled {
  background-color: #6c757d;
  cursor: not-allowed;
}

.form-group input.invalid,
.form-group select.invalid,
.form-group textarea.invalid {
  border-color: #dc3545;
  box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
}

.form-error {
  display: block;
  margin-top: 0.25rem;
  font-size: 0.85rem;
  color: #dc3545;
}
</style>