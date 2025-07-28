<template>
  <div class="sidebar flex-col" v-show="show">
    <div class="logo">
      <div class="logo-text">
        <h2>ETEP</h2>
        <small>EAD</small>
      </div>
    </div>
    <hr>
    
    <div class="system-title">
      <h3>🎛️ Gestor de Comunicações</h3>
    </div>
    
    <div class="menu flex-item">
      <template v-for="item in menu" :key="item.title">
        <div 
          :class="($route.name === item.route ? 'selected' : '') + ' item'" 
          v-if="item?.route" 
          @click="route(item.route)"
        >
          <i :class="item.icon"></i>&nbsp;&nbsp;{{ item.title }}
        </div>
        
        <div class="item open" v-else>
          <i :class="item.icon"></i>&nbsp;&nbsp;{{ item.title }}
          <div class="sub-menu">
            <div 
              v-for="subitem in item.list" 
              :key="subitem.title"
              :class="($route.name === subitem.route ? 'selected' : '') + ' sub-item'" 
              @click="route(subitem.route)"
            >
              <i :class="subitem.icon"></i>&nbsp;&nbsp;{{ subitem.title }}
            </div>
          </div>
        </div>
      </template>
    </div>
    
    <div class="sidebar-footer">
      <div class="version-info">
        <small>v2.9.0 - Sistema ETEP</small>
      </div>
    </div>
  </div>
  
  <div class="sidebar-toggle flex-row flex-center">
    <i 
      :class="show ? 'fa-solid fa-chevron-left' : 'fa-solid fa-chevron-right'" 
      class="clickable" 
      @click="show = !show"
    ></i>
  </div>
</template>

<script>
export default {
  name: 'Sidebar',
  data() {
    return {
      menu: [
        { 
          title: 'Réguas', 
          icon: 'fa-solid fa-list', 
          list: [
            { 
              title: 'Gerenciar Réguas', 
              icon: 'fa-solid fa-cogs', 
              route: 'CommunicationRules' 
            },
            { 
              title: 'Histórico de Versões', 
              icon: 'fa-solid fa-history', 
              route: 'RuleVersions' 
            }
          ]
        },
        { 
          title: 'Execuções', 
          icon: 'fa-solid fa-play-circle', 
          list: [
            { 
              title: 'Painel Diário', 
              icon: 'fa-solid fa-calendar-check', 
              route: 'DailyPanel' 
            },
            { 
              title: 'Logs Detalhados', 
              icon: 'fa-solid fa-file-alt', 
              route: 'DetailedLogs' 
            }
          ]
        },
        { 
          title: 'Ferramentas', 
          icon: 'fa-solid fa-tools', 
          list: [
            { 
              title: 'PHPMyAdmin', 
              icon: 'fa-solid fa-database', 
              route: 'PHPMyAdmin' 
            }
          ] 
        }
      ],
      show: true,
    }
  },
  methods: {
    route(routeName) {
      if (routeName === 'PHPMyAdmin') {
        window.open('http://localhost:8081', '_blank')
        return
      }
      
      if (this.$route.name !== routeName) {
        this.$router.push({ name: routeName })
      }
    }
  }
}
</script>

<style scoped>
.sidebar-toggle {
  width: 1em;
  height: 100vh;
  box-sizing: border-box;
  padding: 10px;
  background: linear-gradient(90deg, var(--background-color) 0%, var(--background-color) 80%, rgba(182,182,182,1) 100%);
  color: var(--text-color);
}

.sidebar {
  width: 250px;
  background-color: var(--background-color);
  height: 100vh;
  padding: 20px 0px 20px 20px;
  box-sizing: border-box;
}

.logo {
  padding: 10px;
  width: 100%;
  box-sizing: border-box;
}

.logo-text {
  text-align: center;
  color: var(--primary-color);
}

.logo-text h2 {
  margin: 0;
  font-size: 1.8rem;
  font-weight: bold;
}

.logo-text small {
  font-size: 0.8rem;
  color: var(--secondary-color);
}

.system-title {
  text-align: center;
  margin: 1rem 0;
  padding: 0 1rem;
}

.system-title h3 {
  color: var(--primary-color);
  font-size: 1.1rem;
  margin: 0;
  font-weight: 600;
}

.menu {
  padding-top: 20px;
}

.item {
  cursor: pointer;
  padding: 12px 15px;
  border-radius: 8px;
  border: 1px solid transparent;
  margin-bottom: 8px;
  margin-right: 15px;
  transition: all 0.3s;
  color: var(--text-color);
}

.item:hover {
  background-color: var(--primary-color);
  color: var(--primary-contrast-color);
  border-color: var(--primary-color);
}

.item.selected {
  background-color: var(--secondary-color);
  color: var(--primary-contrast-color);
  border: none;
  cursor: default;
}

.item.open {
  cursor: default;
}

.item.open:hover {
  background-color: unset;
  color: var(--text-color);
  border-color: transparent;
}

.sub-menu {
  display: none;
  padding-left: 20px;
  font-size: 0.9rem;
  margin-top: 8px;
}

.item.open:hover .sub-menu, .sub-menu:has(.selected) {
  display: block;
}

.sub-item {
  cursor: pointer;
  padding: 8px 12px;
  border-radius: 6px;
  margin-bottom: 4px;
  transition: all 0.3s;
  color: var(--text-color);
}

.sub-item:hover {
  background-color: var(--primary-color);
  color: var(--primary-contrast-color);
}

.sub-item.selected {
  background-color: var(--secondary-color);
  color: var(--primary-contrast-color);
  cursor: default;
}

.sidebar-footer {
  padding: 1rem;
  border-top: 1px solid rgba(0,0,0,0.1);
  margin-top: auto;
}

.version-info {
  text-align: center;
  color: var(--text-light-color);
}

hr {
  border: none;
  height: 1px;
  background-color: rgba(0,0,0,0.1);
  margin: 1rem 0;
}

/* Flex utilities que faltavam */
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

.flex-center {
  justify-content: center;
  align-items: center;
}

.clickable {
  cursor: pointer;
}
</style>