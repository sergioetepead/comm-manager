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
            placeholder="Ex: Boas-vindas novos alunos"
          >
        </div>
        
        <div class="form-group">
          <label for="type">Tipo de Comunicação *</label>
          <select v-model="formData.type" id="type" required>
            <option value="">Selecione o tipo</option>
            <option value="email">E-mail</option>
            <option value="sms">SMS</option>
            <option value="whatsapp">WhatsApp</option>
            <option value="push">Push Notification</option>
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
          <label for="message_template">Template da Mensagem</label>
          <textarea 
            v-model="formData.message_template"
            id="message_template"
            rows="4"
            placeholder="Olá {nome}, bem-vindo(a) ao ETEP!"
          ></textarea>
          <small class="form-help">
            Use {campo} para variáveis dinâmicas baseadas na consulta SQL
          </small>
        </div>
        
        <div class="form-group">
          <label for="description">Descrição</label>
          <textarea 
            v-model="formData.description"
            id="description"
            rows="3"
            placeholder="Descrição da finalidade desta régua..."
          ></textarea>
        </div>
        
        <div class="form-actions">
          <button type="button" @click="$emit('cancel')" class="btn-secondary">
            Cancelar
          </button>
          <button type="submit" class="btn-primary">
            {{ rule ? 'Atualizar' : 'Criar' }} Régua
          </button>
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
        description: ''
      }
    }
  },
  mounted() {
    if (this.rule) {
      this.formData = { ...this.rule }
    }
  },
  methods: {
    handleSubmit() {
      this.$emit('save', this.formData)
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
  gap: 1rem;
  justify-content: flex-end;
  padding-top: 1rem;
  border-top: 1px solid #e9ecef;
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
</style>