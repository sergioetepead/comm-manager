<template>
  <div class="rule-versions">
    <div class="header">
      <h2>📚 Histórico de Versões</h2>
      <p>Consulte versões anteriores das réguas de comunicação organizadas por status</p>
    </div>
    
    <div class="versions-list">
      <div v-if="loading" class="loading">Carregando histórico de versões...</div>
      
      <div v-else-if="Object.keys(groupedRules).length === 0" class="empty-state">
        <p>Nenhuma régua encontrada no sistema.</p>
      </div>
      
      <div v-else class="rules-grouped">
        <div 
          v-for="(group, status) in groupedRules" 
          :key="status"
          class="status-group"
        >
          <div class="group-header">
            <h3>{{ formatStatusName(status) }}</h3>
            <span class="group-count">{{ group.length }} régua{{ group.length === 1 ? '' : 's' }}</span>
          </div>
          
          <div class="rules-grid">
            <div 
              v-for="rule in group" 
              :key="rule.rule_id" 
              class="rule-card"
              :class="{ expanded: expandedRule === rule.rule_id }"
            >
              <div 
                class="rule-header"
                @click="toggleRuleExpansion(rule.rule_id)"
              >
                <div class="rule-info">
                  <h3>{{ rule.current_name }}</h3>
                  <span class="version-count">{{ rule.version_count }} vers{{ rule.version_count === 1 ? 'ão' : 'ões' }}</span>
                </div>
                <div class="expand-icon">
                  {{ expandedRule === rule.rule_id ? '▼' : '▶' }}
                </div>
              </div>
              
              <div v-if="expandedRule === rule.rule_id" class="versions-table">
                <div v-if="loadingVersions" class="loading-versions">
                  Carregando versões...
                </div>
                <div v-else>
                  <table class="versions-data">
                    <thead>
                      <tr>
                        <th>Versão</th>
                        <th>Data/Hora</th>
                        <th>Situação</th>
                        <th>Nome</th>
                        <th>SQL</th>
                        <th>Canal</th>
                        <th>Template ID</th>
                        <th>Horário Início</th>
                        <th>Horário Fim</th>
                        <th>Ordem</th>
                        <th>Ativa</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr 
                        v-for="(version, index) in ruleVersions[rule.rule_id] || []" 
                        :key="version.id"
                        :class="{ 
                          'current-version': index === 0,
                          'inactive-version': !version.active 
                        }"
                      >
                        <td class="version-number">
                          {{ index === 0 ? 'Atual' : `v${ruleVersions[rule.rule_id].length - index}` }}
                          {{ !version.superseded && version.active ? '✅' : '' }}
                        </td>
                        <td>{{ formatDateTime(version.created_at) }}</td>
                        <td class="situation-cell">
                          <span 
                            class="situation-badge"
                            :class="version.superseded ? 'superseded' : 'current'"
                          >
                            {{ version.superseded ? 'Substituída' : 'Atual' }}
                          </span>
                        </td>
                        <td class="name-cell">{{ version.name }}</td>
                        <td class="sql-cell">
                          <code>{{ version.sql_query }}</code>
                        </td>
                        <td>{{ version.channel }}</td>
                        <td class="template-cell">{{ version.template_id }}</td>
                        <td>{{ version.send_time_start || 'N/A' }}</td>
                        <td>{{ version.send_time_end || 'N/A' }}</td>
                        <td>{{ version.execution_order || 'N/A' }}</td>
                        <td>
                          <span 
                            class="active-badge"
                            :class="version.active ? 'active' : 'inactive'"
                          >
                            {{ version.active ? 'Sim' : 'Não' }}
                          </span>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  name: 'RuleVersions',
  data() {
    return {
      rules: [], // Lista de rules agrupadas por rule_id
      loading: false,
      loadingVersions: false,
      expandedRule: null, // ID da regra expandida
      ruleVersions: {} // Cache das versões: { rule_id: [versions...] }
    }
  },
  computed: {
    groupedRules() {
      const groups = {}
      
      this.rules.forEach(rule => {
        const ruleStatus = this.getStatusFromName(rule.current_name)
        if (!groups[ruleStatus]) {
          groups[ruleStatus] = []
        }
        groups[ruleStatus].push(rule)
      })
      
      // Sort each group by execution_order, then by name (same logic as CommunicationRules)
      Object.keys(groups).forEach(ruleStatus => {
        groups[ruleStatus].sort((a, b) => {
          if (a.min_execution_order && b.min_execution_order) {
            return a.min_execution_order - b.min_execution_order
          }
          if (a.min_execution_order && !b.min_execution_order) return -1
          if (!a.min_execution_order && b.min_execution_order) return 1
          return a.current_name.localeCompare(b.current_name)
        })
      })
      
      // Create sorted groups object ordered by minimum execution_order
      const sortedGroups = {}
      const groupsWithOrder = []
      const groupsWithoutOrder = []
      
      Object.keys(groups).forEach(ruleStatus => {
        const rulesInGroup = groups[ruleStatus]
        const minExecutionOrder = Math.min(...rulesInGroup
          .filter(rule => rule.min_execution_order)
          .map(rule => rule.min_execution_order))
        
        if (minExecutionOrder !== Infinity) {
          groupsWithOrder.push({ ruleStatus, minOrder: minExecutionOrder, rules: rulesInGroup })
        } else {
          groupsWithoutOrder.push({ ruleStatus, rules: rulesInGroup })
        }
      })
      
      // Sort groups with execution_order by minimum order
      groupsWithOrder.sort((a, b) => a.minOrder - b.minOrder)
      
      // Build final sorted groups object
      groupsWithOrder.forEach(group => {
        sortedGroups[group.ruleStatus] = group.rules
      })
      groupsWithoutOrder.forEach(group => {
        sortedGroups[group.ruleStatus] = group.rules
      })
      
      return sortedGroups
    }
  },
  mounted() {
    this.loadRules()
  },
  methods: {
    async loadRules() {
      this.loading = true
      try {
        // Carregar regras agrupadas por rule_id
        const response = await axios.get('/api/communication-rules/versions-summary')
        console.log('Versions Summary API Response:', response.data)
        
        if (response.data.status === 'success') {
          this.rules = response.data.data
        } else {
          this.rules = response.data
        }
      } catch (error) {
        console.error('Erro ao carregar histórico de versões:', error)
        alert('Erro ao carregar histórico de versões')
      } finally {
        this.loading = false
      }
    },
    
    async toggleRuleExpansion(ruleId) {
      if (this.expandedRule === ruleId) {
        // Recolher
        this.expandedRule = null
      } else {
        // Expandir e carregar versões se necessário
        this.expandedRule = ruleId
        
        if (!this.ruleVersions[ruleId]) {
          await this.loadRuleVersions(ruleId)
        }
      }
    },
    
    async loadRuleVersions(ruleId) {
      this.loadingVersions = true
      try {
        const response = await axios.get(`/api/communication-rules/versions/${ruleId}`)
        console.log(`Versions for rule ${ruleId}:`, response.data)
        
        if (response.data.status === 'success') {
          // Ordenar por created_at descrescente (mais recente primeiro)
          const versions = response.data.data.sort((a, b) => 
            new Date(b.created_at) - new Date(a.created_at)
          )
          this.ruleVersions[ruleId] = versions
        } else {
          this.ruleVersions[ruleId] = response.data
        }
      } catch (error) {
        console.error(`Erro ao carregar versões da régua ${ruleId}:`, error)
        alert('Erro ao carregar versões da régua')
      } finally {
        this.loadingVersions = false
      }
    },
    
    getStatusFromName(name) {
      if (!name || !name.includes('_')) {
        return 'outros'
      }
      return name.split('_')[0]
    },
    
    formatStatusName(ruleStatus) {
      // Usa o ruleStatus original com primeira letra maiúscula
      return ruleStatus.charAt(0).toUpperCase() + ruleStatus.slice(1)
    },
    
    formatDateTime(dateString) {
      if (!dateString) return 'N/A'
      
      try {
        // Handle MySQL datetime format: YYYY-MM-DD HH:MM:SS
        const date = new Date(dateString.replace(' ', 'T'))
        
        if (isNaN(date.getTime())) {
          return 'Data inválida'
        }
        
        return date.toLocaleDateString('pt-BR', {
          year: 'numeric',
          month: '2-digit',
          day: '2-digit',
          hour: '2-digit',
          minute: '2-digit'
        })
      } catch (error) {
        console.error('Erro ao formatar data/hora:', error)
        return 'Data inválida'
      }
    }
  }
}
</script>

<style scoped>
.rule-versions {
  padding: 2rem;
  max-width: 1400px;
}

.header {
  margin-bottom: 2rem;
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

.versions-list {
  background: var(--primary-contrast-color);
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
  overflow: hidden;
}

.loading {
  text-align: center;
  padding: 2rem;
  color: #6c757d;
}

.empty-state {
  text-align: center;
  padding: 3rem;
  color: #6c757d;
}

.rules-grouped {
  display: flex;
  flex-direction: column;
}

.status-group {
  background: var(--background-color);
  border-bottom: 1px solid #e9ecef;
}

.status-group:last-child {
  border-bottom: none;
}

.group-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem 1.5rem;
  background-color: var(--primary-color);
}

.group-header h3 {
  margin: 0;
  color: var(--primary-contrast-color);
  font-size: 1.1rem;
}

.group-count {
  background-color: var(--secondary-color);
  color: var(--primary-contrast-color);
  padding: 0.25rem 0.75rem;
  border-radius: 12px;
  font-size: 0.85rem;
  font-weight: 500;
}

.rules-grid {
  padding: 1.5rem;
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.rule-card {
  border: 1px solid #e9ecef;
  border-radius: 8px;
  overflow: hidden;
  background-color: var(--primary-contrast-color);
  transition: box-shadow 0.2s;
}

.rule-card.expanded {
  box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.rule-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem;
  background-color: var(--background-color);
  cursor: pointer;
  transition: background-color 0.2s;
}

.rule-header:hover {
  background-color: rgba(240, 240, 240, 0.8);
}

.rule-info h3 {
  margin: 0 0 0.25rem 0;
  color: var(--primary-color);
  font-size: 1.1rem;
}

.version-count {
  color: var(--text-color);
  font-size: 0.9rem;
}

.expand-icon {
  color: var(--secondary-color);
  font-size: 1.2rem;
  font-weight: bold;
}

.versions-table {
  border-top: 1px solid #e9ecef;
  background-color: var(--primary-contrast-color);
}

.loading-versions {
  padding: 2rem;
  text-align: center;
  color: #6c757d;
  font-style: italic;
}

.versions-data {
  width: 100%;
  border-collapse: collapse;
  font-size: 0.9rem;
}

.versions-data th {
  background-color: var(--background-color);
  color: var(--primary-color);
  padding: 1rem 0.75rem;
  text-align: left;
  font-weight: 600;
  border-bottom: 2px solid #e9ecef;
}

.versions-data td {
  padding: 0.75rem;
  border-bottom: 1px solid #f8f9fa;
  vertical-align: top;
}

.versions-data tr:hover {
  background-color: var(--background-color);
}

.current-version {
  background-color: rgba(237, 110, 38, 0.1) !important;
}

.current-version:hover {
  background-color: rgba(237, 110, 38, 0.15) !important;
}

.inactive-version {
  opacity: 0.7;
}

.version-number {
  font-weight: 600;
  color: var(--primary-color);
}

.situation-cell {
  text-align: center;
}

.situation-badge {
  padding: 0.25rem 0.75rem;
  border-radius: 20px;
  font-size: 0.8rem;
  font-weight: 500;
  text-transform: uppercase;
}

.situation-badge.current {
  background-color: var(--secondary-color);
  color: var(--primary-contrast-color);
}

.situation-badge.superseded {
  background-color: var(--text-light-color);
  color: var(--primary-contrast-color);
}

.name-cell {
  font-family: 'Courier New', monospace;
  font-size: 0.85rem;
  color: var(--primary-color);
  max-width: 200px;
  word-break: break-word;
}

.sql-cell {
  font-family: 'Courier New', monospace;
  font-size: 0.75rem;
  color: var(--primary-color);
  max-width: 250px;
  word-break: break-word;
}

.sql-cell code {
  background-color: var(--background-color);
  padding: 0.25rem 0.5rem;
  border-radius: 4px;
  display: block;
  white-space: pre-wrap;
  line-height: 1.3;
}

.template-cell {
  font-family: 'Courier New', monospace;
  font-size: 0.8rem;
  color: var(--text-color);
  max-width: 150px;
  word-break: break-all;
}

.active-badge {
  padding: 0.2rem 0.6rem;
  border-radius: 15px;
  font-size: 0.8rem;
  font-weight: 500;
}

.active-badge.active {
  background-color: var(--secondary-color);
  color: var(--primary-contrast-color);
}

.active-badge.inactive {
  background-color: var(--text-light-color);
  color: var(--primary-contrast-color);
}

/* Responsive para tabela */
@media (max-width: 1200px) {
  .versions-data {
    font-size: 0.8rem;
  }
  
  .versions-data th,
  .versions-data td {
    padding: 0.5rem;
  }
}

@media (max-width: 900px) {
  .rule-versions {
    padding: 1rem;
  }
  
  .versions-data {
    display: block;
    overflow-x: auto;
    white-space: nowrap;
  }
}
</style>