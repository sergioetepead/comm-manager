import { createRouter, createWebHistory } from 'vue-router'
import CommunicationRules from '../components/CommunicationRules.vue'
import DailyPanel from '../views/DailyPanel.vue'
import RuleVersions from '../views/RuleVersions.vue'
import DetailedLogs from '../views/DetailedLogs.vue'

const routes = [
  {
    path: '/',
    name: 'DailyPanel',
    component: DailyPanel
  },
  {
    path: '/rules',
    name: 'CommunicationRules', 
    component: CommunicationRules
  },
  {
    path: '/versions',
    name: 'RuleVersions',
    component: RuleVersions
  },
  {
    path: '/logs',
    name: 'DetailedLogs',
    component: DetailedLogs
  }
]

const router = createRouter({
  history: createWebHistory('/admin/'),
  routes
})

export default router