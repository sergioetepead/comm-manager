<?php

class VersionReader {
    private $versionFile;
    private $infraVersionFile;
    
    public function __construct() {
        $this->versionFile = __DIR__ . '/../VERSION.md';
        $this->infraVersionFile = __DIR__ . '/../../comm-manager-infra/VERSION.md';
    }
    
    public function getCurrentVersion() {
        if (!file_exists($this->versionFile)) {
            return '1.0.0';
        }
        
        $content = file_get_contents($this->versionFile);
        
        // Procura pela primeira linha que começa com "## v"
        if (preg_match('/^## v([0-9]+\.[0-9]+\.[0-9]+)/m', $content, $matches)) {
            return $matches[1];
        }
        
        return '1.0.0';
    }
    
    public function getVersionInfo() {
        $appVersion = $this->getAppVersionInfo();
        $infraVersion = $this->getInfraVersionInfo();
        
        return [
            'app' => $appVersion,
            'infra' => $infraVersion
        ];
    }
    
    private function getAppVersionInfo() {
        if (!file_exists($this->versionFile)) {
            return [
                'version' => '1.0.0',
                'description' => 'Versão inicial',
                'date' => date('Y-m-d')
            ];
        }
        
        $content = file_get_contents($this->versionFile);
        
        // Procura pela primeira entrada de versão
        if (preg_match('/^## v([0-9]+\.[0-9]+\.[0-9]+) - ([0-9-]+)\s*\n\*\*(.+?)\*\*/m', $content, $matches)) {
            return [
                'version' => $matches[1],
                'date' => $matches[2],
                'description' => $matches[3]
            ];
        }
        
        return [
            'version' => '1.0.0',
            'description' => 'Versão inicial',
            'date' => date('Y-m-d')
        ];
    }
    
    private function getInfraVersionInfo() {
        if (!file_exists($this->infraVersionFile)) {
            return [
                'version' => '1.0.0',
                'description' => 'Infraestrutura Base',
                'date' => date('Y-m-d')
            ];
        }
        
        $content = file_get_contents($this->infraVersionFile);
        
        // Procura pela primeira entrada de versão
        if (preg_match('/^## v([0-9]+\.[0-9]+\.[0-9]+) - ([0-9-]+)\s*\n\*\*(.+?)\*\*/m', $content, $matches)) {
            return [
                'version' => $matches[1],
                'date' => $matches[2],
                'description' => $matches[3]
            ];
        }
        
        return [
            'version' => '1.0.0',
            'description' => 'Infraestrutura Base',
            'date' => date('Y-m-d')
        ];
    }
}