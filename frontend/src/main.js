import { createApp } from 'vue'
import { createRouter, createWebHistory } from 'vue-router'
import App from './App.vue'

// Importar componentes
import CommunicationRules from './views/CommunicationRules.vue'
import RuleForm from './views/RuleForm.vue'
import RuleLogs from './views/RuleLogs.vue'

// Configurar rotas
const routes = [
  { path: '/', redirect: '/rules' },
  { path: '/rules', component: CommunicationRules, name: 'rules' },
  { path: '/rules/new', component: RuleForm, name: 'new-rule' },
  { path: '/rules/:id/edit', component: RuleForm, name: 'edit-rule', props: true },
  { path: '/rules/:id/logs', component: RuleLogs, name: 'rule-logs', props: true }
]

const router = createRouter({
  history: createWebHistory('/admin/'),
  routes
})

// Criar e montar aplicação
const app = createApp(App)
app.use(router)
app.mount('#app')