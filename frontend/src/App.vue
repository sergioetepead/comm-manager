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
:root {
  --primary-color: #252e62;
  --secondary-color: #ed6e26;
  --text-color: #555555;
  --text-light-color: #8a8a8a;
  --primary-contrast-color: #ffffff;
  --background-color: #f0f0f0;
}

* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  font-family: 'Open Sans', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  background-color: var(--background-color);
  color: var(--text-color);
}

#app {
  min-height: 100vh;
}

.header {
  background: var(--primary-color);
  color: var(--primary-contrast-color);
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
  color: var(--secondary-color);
}

.main-content {
  max-width: 1200px;
  margin: 2rem auto;
  padding: 0 1rem;
}
</style>