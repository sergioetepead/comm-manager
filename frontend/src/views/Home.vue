<template>
  <div class="home-view">
    <div class="welcome-card">
      <h2>🎛️ Gestor de Comunicações ETEP</h2>
      <p>Bem-vindo ao sistema de gestão da régua de comunicação!</p>
      
      <div class="features-grid">
        <div class="feature-card">
          <div class="feature-icon">📋</div>
          <h3>Réguas de Comunicação</h3>
          <p>Gerencie e configure suas réguas de comunicação automatizadas</p>
          <router-link to="/rules" class="btn-primary">
            Acessar Interface
          </router-link>
        </div>
        
        <div class="feature-card">
          <div class="feature-icon">📊</div>
          <h3>Logs e Relatórios</h3>
          <p>Acompanhe a execução e resultados das suas réguas</p>
          <router-link to="/logs" class="btn-secondary">
            Ver Logs
          </router-link>
        </div>
        
        <div class="feature-card">
          <div class="feature-icon">🗄️</div>
          <h3>Banco de Dados</h3>
          <p>Acesse diretamente o banco de dados via PHPMyAdmin</p>
          <a href="http://localhost:8081" target="_blank" class="btn-info">
            Abrir PHPMyAdmin
          </a>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Home'
}
</script>

<style scoped>
.home-view {
  padding: 2rem;
}

.welcome-card {
  background: var(--primary-contrast-color);
  border-radius: 12px;
  padding: 2rem;
  box-shadow: 0 4px 20px rgba(0,0,0,0.1);
  text-align: center;
}

.welcome-card h2 {
  color: var(--primary-color);
  margin-bottom: 1rem;
  font-size: 2.2rem;
}

.welcome-card > p {
  color: var(--text-color);
  font-size: 1.1rem;
  margin-bottom: 3rem;
}

.features-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 2rem;
  margin-top: 2rem;
}

.feature-card {
  background: var(--background-color);
  border-radius: 8px;
  padding: 2rem;
  text-align: center;
  transition: transform 0.3s, box-shadow 0.3s;
}

.feature-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.feature-icon {
  font-size: 3rem;
  margin-bottom: 1rem;
}

.feature-card h3 {
  color: var(--primary-color);
  margin-bottom: 1rem;
  font-size: 1.3rem;
}

.feature-card p {
  color: var(--text-color);
  margin-bottom: 1.5rem;
  line-height: 1.5;
}

.btn-primary, .btn-secondary, .btn-info {
  display: inline-block;
  padding: 0.75rem 1.5rem;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-size: 0.95rem;
  text-decoration: none;
  transition: all 0.3s;
  font-weight: 500;
}

.btn-primary {
  background-color: var(--primary-color);
  color: var(--primary-contrast-color);
}

.btn-primary:hover {
  background-color: rgba(37, 46, 98, 0.8);
  color: var(--primary-contrast-color);
}

.btn-secondary {
  background-color: var(--secondary-color);
  color: var(--primary-contrast-color);
}

.btn-secondary:hover {
  background-color: rgba(237, 110, 38, 0.8);
  color: var(--primary-contrast-color);
}

.btn-info {
  background-color: var(--primary-color);
  color: var(--primary-contrast-color);
}

.btn-info:hover {
  background-color: rgba(37, 46, 98, 0.8);
  color: var(--primary-contrast-color);
}
</style>