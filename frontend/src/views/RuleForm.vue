<template>
  <div>
    <div class="page-header">
      <h2>{{ isEdit ? 'Editar Régua' : 'Nova Régua' }}</h2>
      <router-link to="/rules" class="btn btn-secondary">← Voltar</router-link>
    </div>

    <div class="card">
      <form @submit.prevent="saveRule">
        <div class="form-row">
          <div class="form-group">
            <label for="name">Nome da Régua *</label>
            <input
              id="name"
              v-model="form.name"
              type="text"
              class="form-control"
              placeholder="Ex: lead_captacaod0"
              required
            />
            <small class="help-text">Formato: status_substatus (sem espaços, acentos ou símbolos)</small>
          </div>

          <div class="form-group">
            <label for="rule_id">Rule ID</label>
            <input
              id="rule_id"
              v-model="form.rule_id"
              type="text"
              class="form-control"
              placeholder="Gerado automaticamente se vazio"
              :readonly="isEdit"
            />
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="channel">Canal *</label>
            <select id="channel" v-model="form.channel" class="form-control" required>
              <option value="">Selecione...</option>
              <option value="WHATSAPP">WhatsApp</option>
              <option value="EMAIL">E-mail</option>
              <option value="SMS">SMS</option>
            </select>
          </div>

          <div class="form-group">
            <label for="template_id">Template ID *</label>
            <input
              id="template_id"
              v-model="form.template_id"
              type="text"
              class="form-control"
              placeholder="Ex: 63f9a7e2b12abx458f"
              required
            />
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="send_time_start">Horário Início (opcional)</label>
            <input
              id="send_time_start"
              v-model="form.send_time_start"
              type="time"
              class="form-control"
            />
          </div>

          <div class="form-group">
            <label for="send_time_end">Horário Fim (opcional)</label>
            <input
              id="send_time_end"
              v-model="form.send_time_end"
              type="time"
              class="form-control"
            />
          </div>

          <div class="form-group">
            <label for="execution_order">Ordem de Execução (opcional)</label>
            <input
              id="execution_order"
              v-model.number="form.execution_order"
              type="number"
              class="form-control"
              placeholder="1, 2, 3..."
            />
          </div>
        </div>

        <div class="form-group">
          <label for="sql_query">SQL da Régua *</label>
          <textarea
            id="sql_query"
            v-model="form.sql_query"
            class="form-control sql-textarea"
            placeholder="SELECT 'João' as first_name, '5512987654321' as contact, JSON_OBJECT('first_name', 'João') as payload WHERE CURRENT_DATE = CURRENT_DATE"
            required
            rows="8"
          ></textarea>
          <small class="help-text">
            Deve retornar no mínimo: first_name, contact, payload
          </small>
        </div>

        <div class="form-group">
          <label class="checkbox-label">
            <input v-model="form.active" type="checkbox" />
            Régua ativa
          </label>
        </div>

        <div class="form-actions">
          <button type="button" @click="testSQL" class="btn btn-secondary" :disabled="!form.sql_query">
            Testar SQL
          </button>
          <button type="submit" class="btn" :disabled="saving">
            {{ saving ? 'Salvando...' : 'Salvar Régua' }}
          </button>
        </div>
      </form>
    </div>

    <div v-if="sqlTestResult" class="card">
      <h3>Resultado do Teste SQL</h3>
      <pre class="sql-result">{{ sqlTestResult }}</pre>
    </div>

    <div v-if="error" class="error">
      {{ error }}
    </div>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  name: 'RuleForm',
  props: {
    id: String
  },
  data() {
    return {
      form: {
        rule_id: '',
        name: '',
        sql_query: '',
        channel: '',
        template_id: '',
        send_time_start: '',
        send_time_end: '',
        execution_order: null,
        active: true
      },
      saving: false,
      error: null,
      sqlTestResult: null
    }
  },
  computed: {
    isEdit() {
      return !!this.id
    }
  },
  async mounted() {
    if (this.isEdit) {
      await this.loadRule()
    }
  },
  methods: {
    async loadRule() {
      try {
        const response = await axios.get(`/api/communication-rules/${this.id}`)
        if (response.data.status === 'success') {
          this.form = { ...response.data.data }
        } else {
          this.error = 'Régua não encontrada'
        }
      } catch (err) {
        this.error = 'Erro ao carregar régua'
        console.error('Erro ao carregar régua:', err)
      }
    },

    async testSQL() {
      if (!this.form.sql_query) return
      
      try {
        const response = await axios.post('/api/communication-rules/test', {
          sql_query: this.form.sql_query
        })
        this.sqlTestResult = JSON.stringify(response.data, null, 2)
      } catch (err) {
        this.sqlTestResult = 'Erro: ' + (err.response?.data?.message || err.message)
      }
    },

    async saveRule() {
      this.saving = true
      this.error = null

      try {
        const method = this.isEdit ? 'put' : 'post'
        const url = this.isEdit ? `/api/communication-rules/${this.id}` : '/api/communication-rules'
        
        const response = await axios[method](url, this.form)
        
        if (response.data.status === 'success') {
          this.$router.push('/rules')
        } else {
          this.error = response.data.message || 'Erro ao salvar régua'
        }
      } catch (err) {
        this.error = err.response?.data?.message || 'Erro ao salvar régua'
        console.error('Erro ao salvar régua:', err)
      } finally {
        this.saving = false
      }
    }
  }
}
</script>

<style scoped>
.form-row {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 1.5rem;
}

.sql-textarea {
  font-family: 'Courier New', monospace;
  resize: vertical;
}

.help-text {
  display: block;
  margin-top: 0.5rem;
  color: #718096;
  font-size: 0.8rem;
}

.checkbox-label {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  cursor: pointer;
}

.form-actions {
  display: flex;
  gap: 1rem;
  justify-content: flex-end;
  margin-top: 2rem;
  padding-top: 1.5rem;
  border-top: 1px solid #e2e8f0;
}

.sql-result {
  background-color: #f7fafc;
  padding: 1rem;
  border-radius: 6px;
  overflow-x: auto;
  font-family: 'Courier New', monospace;
  font-size: 0.9rem;
}

.error {
  background-color: #fed7d7;
  color: #742a2a;
  padding: 1rem;
  border-radius: 6px;
  margin-top: 1rem;
}
</style>