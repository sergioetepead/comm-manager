<template>
  <div class="detailed-logs">
    <div class="header">
      <h2>📋 Logs Detalhados</h2>
      <p>Consulte o histórico completo de execuções das réguas</p>
    </div>
    
    <div class="construction-notice">
      <div class="construction-content">
        <div class="construction-icon">🚧</div>
        <h3>Funcionalidade em Construção</h3>
        <p>
          O sistema de logs detalhados está sendo desenvolvido e estará disponível em breve.
        </p>
        
        <div class="features-preview">
          <h4>🔜 Funcionalidades previstas:</h4>
          <div class="features-grid">
            <div class="feature-item">
              <div class="feature-icon">🔍</div>
              <strong>Filtros Avançados</strong>
              <p>Filtre por data, régua, status e tipo de execução</p>
            </div>
            
            <div class="feature-item">
              <div class="feature-icon">📊</div>
              <strong>Detalhes Completos</strong>
              <p>Visualize rule_dispatch_log com todos os campos</p>
            </div>
            
            <div class="feature-item">
              <div class="feature-icon">💾</div>
              <strong>Export de Dados</strong>
              <p>Exporte logs para CSV ou Excel para análise</p>
            </div>
            
            <div class="feature-item">
              <div class="feature-icon">🎯</div>
              <strong>Drill-down</strong>
              <p>Acesse dados específicos de cada execução</p>
            </div>
            
            <div class="feature-item">
              <div class="feature-icon">📈</div>
              <strong>Métricas de Performance</strong>
              <p>Acompanhe tempo de execução e eficiência</p>
            </div>
            
            <div class="feature-item">
              <div class="feature-icon">🚨</div>
              <strong>Alertas e Erros</strong>
              <p>Identifique rapidamente falhas e problemas</p>
            </div>
          </div>
        </div>
        
        <div class="temp-info">
          <h4>📝 Estrutura dos Logs:</h4>
          <div class="log-structure">
            <div class="log-field">
              <strong>rule_dispatch_timestamp:</strong> Data/hora da execução
            </div>
            <div class="log-field">
              <strong>rule_snapshot:</strong> Parâmetros da regra no momento da execução
            </div>
            <div class="log-field">
              <strong>recipients:</strong> Lista com first_name, contact, payload
            </div>
            <div class="log-field">
              <strong>execution_status:</strong> success, empty, error
            </div>
            <div class="log-field">
              <strong>response_payload:</strong> Resposta do messaging_system
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'DetailedLogs'
}
</script>

<style scoped>
.detailed-logs {
  padding: 2rem;
  max-width: 1200px;
}

.header {
  margin-bottom: 3rem;
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

.construction-notice {
  background: var(--primary-contrast-color);
  border-radius: 12px;
  padding: 3rem;
  text-align: center;
  box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.construction-content {
  max-width: 1000px;
  margin: 0 auto;
}

.construction-icon {
  font-size: 4rem;
  margin-bottom: 1rem;
}

.construction-content h3 {
  color: var(--primary-color);
  font-size: 1.8rem;
  margin-bottom: 1rem;
}

.construction-content > p {
  color: var(--text-color);
  font-size: 1.1rem;
  line-height: 1.6;
  margin-bottom: 2rem;
}

.features-preview {
  background: var(--background-color);
  border-radius: 8px;
  padding: 2rem;
  text-align: left;
  margin-bottom: 2rem;
}

.features-preview h4 {
  color: var(--secondary-color);
  margin-bottom: 1.5rem;
  text-align: center;
  font-size: 1.2rem;
}

.features-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 1.5rem;
}

.feature-item {
  background: var(--primary-contrast-color);
  border-radius: 8px;
  padding: 1.5rem;
  text-align: center;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.feature-icon {
  font-size: 2rem;
  margin-bottom: 1rem;
}

.feature-item strong {
  color: var(--primary-color);
  display: block;
  margin-bottom: 0.5rem;
  font-size: 1.1rem;
}

.feature-item p {
  color: var(--text-color);
  font-size: 0.9rem;
  line-height: 1.4;
  margin: 0;
}

.temp-info {
  background: var(--background-color);
  border-radius: 8px;
  padding: 2rem;
  text-align: left;
}

.temp-info h4 {
  color: var(--secondary-color);
  margin-bottom: 1.5rem;
  text-align: center;
  font-size: 1.2rem;
}

.log-structure {
  display: grid;
  gap: 1rem;
}

.log-field {
  background: var(--primary-contrast-color);
  border-radius: 6px;
  padding: 1rem;
  border-left: 4px solid var(--secondary-color);
}

.log-field strong {
  color: var(--primary-color);
  font-family: 'Courier New', monospace;
}
</style>