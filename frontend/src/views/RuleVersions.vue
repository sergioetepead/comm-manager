<template>
  <div class="rule-versions">
    <div class="header">
      <h2>📚 Histórico de Versões</h2>
      <p>Consulte e compare versões anteriores das réguas de comunicação</p>
    </div>
    
    <div class="construction-notice">
      <div class="construction-content">
        <div class="construction-icon">🚧</div>
        <h3>Funcionalidade em Construção</h3>
        <p>
          O histórico de versões das réguas está sendo desenvolvido e estará disponível em breve.
        </p>
        
        <div class="features-preview">
          <h4>🔜 Funcionalidades previstas:</h4>
          <div class="features-grid">
            <div class="feature-item">
              <div class="feature-icon">📋</div>
              <strong>Listagem de Versões</strong>
              <p>Visualize todas as versões de cada regra com timestamps</p>
            </div>
            
            <div class="feature-item">
              <div class="feature-icon">🔍</div>
              <strong>Comparação Visual</strong>
              <p>Compare diferenças entre versões lado a lado</p>
            </div>
            
            <div class="feature-item">
              <div class="feature-icon">⏮️</div>
              <strong>Rollback Seguro</strong>
              <p>Restaure versões anteriores quando necessário</p>
            </div>
            
            <div class="feature-item">
              <div class="feature-icon">📊</div>
              <strong>Rastreabilidade</strong>
              <p>Histórico completo de modificações e autores</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'RuleVersions'
}
</script>

<style scoped>
.rule-versions {
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
  max-width: 800px;
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
}

.features-preview h4 {
  color: var(--secondary-color);
  margin-bottom: 1.5rem;
  text-align: center;
  font-size: 1.2rem;
}

.features-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
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
</style>