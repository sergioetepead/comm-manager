<template>
  <div class="daily-panel">
    <div class="header">
      <h2>📅 Painel Diário de Execuções</h2>
      <p>Acompanhe o status das execuções das réguas de comunicação</p>
    </div>
    
    <div class="stats-cards">
      <div class="stat-card">
        <div class="stat-icon">📊</div>
        <div class="stat-content">
          <h3>{{ executionsCount }}</h3>
          <p>Execuções Realizadas</p>
        </div>
      </div>
      
      <div class="stat-card">
        <div class="stat-icon">📝</div>
        <div class="stat-content">
          <h3>{{ totalRules }}</h3>
          <p>Réguas Cadastradas</p>
        </div>
      </div>
      
      <div class="stat-card">
        <div class="stat-icon">✅</div>
        <div class="stat-content">
          <h3>{{ activeRules }}</h3>
          <p>Réguas Ativas</p>
        </div>
      </div>
      
      <div class="stat-card">
        <div class="stat-icon">⏸️</div>
        <div class="stat-content">
          <h3>{{ inactiveRules }}</h3>
          <p>Réguas Inativas</p>
        </div>
      </div>
    </div>
    
    <div class="panel-placeholder">
      <div class="placeholder-content">
        <div class="placeholder-icon">🚧</div>
        <h3>Painel de Execuções em Desenvolvimento</h3>
        <p>
          O painel visual dias × réguas com indicadores coloridos será implementado na próxima versão.
        </p>
        <p>
          <strong>Previsto:</strong> Matrix com bolinhas 🟢 sucesso, 🔴 erro, 🔵 vazio, ⚪ não executado
        </p>
        
        <div class="next-features">
          <h4>🔜 Próximas funcionalidades:</h4>
          <ul>
            <li>📊 Grade cruzada de dias × réguas</li>
            <li>🎯 Status visual em tempo real</li>
            <li>🔍 Drill-down para logs específicos</li>
            <li>📈 Gráficos de desempenho</li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  name: 'DailyPanel',
  data() {
    return {
      executionsCount: 0,
      totalRules: 0,
      activeRules: 0,
      inactiveRules: 0,
      loading: true
    }
  },
  async mounted() {
    await this.loadStats()
  },
  methods: {
    async loadStats() {
      try {
        // Carregar estatísticas das réguas
        const response = await axios.get('/api/communication-rules')
        const rules = response.data.data || []
        
        this.totalRules = rules.length
        this.activeRules = rules.filter(rule => rule.active).length
        this.inactiveRules = rules.filter(rule => !rule.active).length
        
        // Por enquanto, execuções = 0 (será implementado depois)
        this.executionsCount = 0
        
      } catch (error) {
        console.error('Erro ao carregar estatísticas:', error)
      } finally {
        this.loading = false
      }
    }
  }
}
</script>

<style scoped>
.daily-panel {
  padding: 2rem;
  max-width: 1200px;
}

.header {
  margin-bottom: 2rem;
  text-align: center;
}

.header h2 {
  color: var(--primary-color);
  font-size: 2rem;
  margin-bottom: 0.5rem;
}

.header p {
  color: var(--text-color);
  font-size: 1.1rem;
}

.stats-cards {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 1.5rem;
  margin-bottom: 3rem;
}

.stat-card {
  background: var(--primary-contrast-color);
  border-radius: 12px;
  padding: 1.5rem;
  box-shadow: 0 4px 15px rgba(0,0,0,0.1);
  display: flex;
  align-items: center;
  gap: 1rem;
  transition: transform 0.3s, box-shadow 0.3s;
}

.stat-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.stat-icon {
  font-size: 2.5rem;
  width: 60px;
  text-align: center;
}

.stat-content h3 {
  color: var(--primary-color);
  font-size: 2rem;
  margin: 0;
  font-weight: bold;
}

.stat-content p {
  color: var(--text-color);
  margin: 0.25rem 0 0 0;
  font-size: 0.95rem;
}

.panel-placeholder {
  background: var(--primary-contrast-color);
  border-radius: 12px;
  padding: 3rem;
  text-align: center;
  box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.placeholder-content {
  max-width: 600px;
  margin: 0 auto;
}

.placeholder-icon {
  font-size: 4rem;
  margin-bottom: 1rem;
}

.placeholder-content h3 {
  color: var(--primary-color);
  font-size: 1.5rem;
  margin-bottom: 1rem;
}

.placeholder-content p {
  color: var(--text-color);
  line-height: 1.6;
  margin-bottom: 1rem;
}

.next-features {
  background: var(--background-color);
  border-radius: 8px;
  padding: 1.5rem;
  margin-top: 2rem;
  text-align: left;
}

.next-features h4 {
  color: var(--secondary-color);
  margin-bottom: 1rem;
}

.next-features ul {
  list-style: none;
  padding: 0;
}

.next-features li {
  color: var(--text-color);
  padding: 0.5rem 0;
  border-bottom: 1px solid rgba(0,0,0,0.1);
}

.next-features li:last-child {
  border-bottom: none;
}
</style>