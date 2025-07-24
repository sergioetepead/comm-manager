<template>
  <div id="app">
    <header class="header">
      <h1>Sistema de Gestão da Régua de Comunicação - ETEP</h1>
      <p>Interface Administrativa</p>
    </header>
    
    <main class="main-content">
      <CommunicationRules />
    </main>
  </div>
</template>

<script>
import CommunicationRules from './components/CommunicationRules.vue'

export default {
  name: 'App',
  components: {
    CommunicationRules
  }
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
  background-color: #f5f5f5;
  color: #333;
}

#app {
  min-height: 100vh;
}

.header {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  padding: 2rem;
  text-align: center;
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.header h1 {
  font-size: 2rem;
  margin-bottom: 0.5rem;
}

.header p {
  font-size: 1.2rem;
  opacity: 0.9;
}

.main-content {
  max-width: 1200px;
  margin: 2rem auto;
  padding: 0 1rem;
}
</style>