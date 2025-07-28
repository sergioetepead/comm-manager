<template>
  <div id="app">
    <div class="flex-row">
      <Sidebar/>
      <div class="flex-item flex-col">
        <router-view/>
      </div>
    </div>
  </div>
</template>

<script>
import Sidebar from './components/Sidebar.vue'

export default {
  name: 'App',
  components: {
    Sidebar
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

/* Flex utilities */
.flex-row {
  display: flex;
  flex-direction: row;
}

.flex-col {
  display: flex;
  flex-direction: column;
}

.flex-item {
  flex: 1;
}
</style>