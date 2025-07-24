<?php
class VersionHelper {
    private $versionFile = __DIR__ . '/../VERSION.md';
    
    public function updateVersion($description, $changes = []) {
        $currentVersion = $this->getCurrentVersion();
        $newVersion = $this->incrementVersion($currentVersion);
        
        $content = $this->buildVersionContent($newVersion, $description, $changes);
        
        // Prepend to existing content
        $existingContent = file_get_contents($this->versionFile);
        $updatedContent = $content . "\n\n" . $existingContent;
        
        file_put_contents($this->versionFile, $updatedContent);
        
        return $newVersion;
    }
    
    private function getCurrentVersion() {
        if (!file_exists($this->versionFile)) {
            return '1.0.0';
        }
        
        $content = file_get_contents($this->versionFile);
        preg_match('/## v(\d+\.\d+\.\d+)/', $content, $matches);
        
        return $matches[1] ?? '1.0.0';
    }
    
    private function incrementVersion($version) {
        $parts = explode('.', $version);
        $parts[2] = (int)$parts[2] + 1;
        
        return implode('.', $parts);
    }
    
    private function buildVersionContent($version, $description, $changes) {
        $date = date('Y-m-d');
        $content = "# Histórico de Versões - Sistema de Gestão da Régua de Comunicação ETEP\n\n";
        $content .= "## v{$version} - {$date}\n";
        $content .= "**{$description}**\n\n";
        
        if (!empty($changes)) {
            $content .= "### Adicionado\n";
            foreach ($changes as $change) {
                $content .= "- ✅ {$change}\n";
            }
        }
        
        return $content;
    }
    
    public function commitAndPush($version, $description) {
        $commitMessage = "v{$version} - {$description}

🤖 Generated with [Claude Code](https://claude.ai/code)

Co-Authored-By: Claude <noreply@anthropic.com>";
        
        // Execute git commands
        $commands = [
            'git add .',
            'git commit -m "' . addslashes($commitMessage) . '"',
            'git push origin main'
        ];
        
        foreach ($commands as $command) {
            exec($command, $output, $returnCode);
            if ($returnCode !== 0) {
                throw new Exception("Failed to execute: {$command}");
            }
        }
        
        return true;
    }
}

// Usage example:
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    $helper = new VersionHelper();
    
    $description = $argv[1] ?? 'Update';
    $changes = array_slice($argv, 2);
    
    try {
        $version = $helper->updateVersion($description, $changes);
        $helper->commitAndPush($version, $description);
        echo "Version {$version} created and pushed successfully!\n";
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
}