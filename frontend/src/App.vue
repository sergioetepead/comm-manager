<template>
  <div id="app">
    <header class="header">
      <div class="header-content">
        <h1>Sistema de Gestão da Régua de Comunicação</h1>
        <p>Centro Universitário ETEP</p>
      </div>
    </header>

    <nav class="nav">
      <div class="nav-content">
        <router-link to="/rules" class="nav-link">Réguas de Comunicação</router-link>
        <a href="/" class="nav-link">← Voltar ao Início</a>
      </div>
    </nav>

    <main class="main">
      <router-view />
    </main>
  </div>
</template>

<script>
export default {
  name: 'App'
}
</script>

<style>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  background-color: #f5f7fa;
  color: #333;
}

.header {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  padding: 2rem 0;
  margin-bottom: 2rem;
}

.header-content {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 2rem;
}

.header h1 {
  font-size: 2rem;
  margin-bottom: 0.5rem;
}

.header p {
  font-size: 1.1rem;
  opacity: 0.9;
}

.nav {
  background: white;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
  margin-bottom: 2rem;
}

.nav-content {
  max-width: 1200px;
  margin: 0 auto;
  padding: 1rem 2rem;
  display: flex;
  gap: 2rem;
}

.nav-link {
  text-decoration: none;
  color: #4a5568;
  font-weight: 500;
  padding: 0.5rem 1rem;
  border-radius: 6px;
  transition: all 0.2s;
}

.nav-link:hover {
  background-color: #edf2f7;
  color: #2d3748;
}

.nav-link.router-link-active {
  background-color: #667eea;
  color: white;
}

.main {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 2rem;
}

/* Componentes úteis */
.btn {
  display: inline-block;
  padding: 0.75rem 1.5rem;
  background-color: #667eea;
  color: white;
  text-decoration: none;
  border-radius: 6px;
  border: none;
  cursor: pointer;
  font-size: 0.9rem;
  font-weight: 500;
  transition: background-color 0.2s;
}

.btn:hover {
  background-color: #5a6fd8;
}

.btn-secondary {
  background-color: #a0aec0;
}

.btn-secondary:hover {
  background-color: #9ca3af;
}

.btn-danger {
  background-color: #e53e3e;
}

.btn-danger:hover {
  background-color: #c53030;
}

.card {
  background: white;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  padding: 1.5rem;
  margin-bottom: 1.5rem;
}

.form-group {
  margin-bottom: 1.5rem;
}

.form-group label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 500;
  color: #2d3748;
}

.form-control {
  width: 100%;
  padding: 0.75rem;
  border: 1px solid #d2d6dc;
  border-radius: 6px;
  font-size: 0.9rem;
}

.form-control:focus {
  outline: none;
  border-color: #667eea;
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.status-indicator {
  display: inline-block;
  padding: 0.25rem 0.75rem;
  border-radius: 12px;
  font-size: 0.8rem;
  font-weight: 500;
}

.status-active {
  background-color: #c6f6d5;
  color: #22543d;
}

.status-inactive {
  background-color: #fed7d7;
  color: #742a2a;
}
</style>