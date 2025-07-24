<?php
require_once 'utils/version.php';
$versionReader = new VersionReader();
$versionInfo = $versionReader->getVersionInfo();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Gestão da Régua de Comunicação - ETEP</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            text-align: center;
            margin-bottom: 30px;
        }
        .status {
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .success {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }
        .info {
            background-color: #d1ecf1;
            border: 1px solid #bee5eb;
            color: #0c5460;
        }
        .links {
            display: flex;
            gap: 20px;
            justify-content: center;
            margin-top: 30px;
        }
        .links a {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .links a:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Sistema de Gestão da Régua de Comunicação</h1>
        <h2>Centro Universitário ETEP</h2>
        
        <div class="status success">
            <strong>✓ Sistema Inicializado</strong><br>
            A infraestrutura base foi configurada com sucesso.
        </div>
        
        <div class="status info">
            <strong>ℹ️ Versão Atual: v<?php echo $versionInfo['version']; ?></strong><br>
            <?php echo $versionInfo['description']; ?> - <?php echo $versionInfo['date']; ?><br>
            Docker Compose configurado com PHP 8.2, Apache e MySQL 8.0
        </div>
        
        <div class="links">
            <a href="/api/test">Testar API</a>
            <a href="http://localhost:8081" target="_blank">PHPMyAdmin</a>
        </div>
        
        <div style="margin-top: 30px; font-size: 14px; color: #666; text-align: center;">
            <p><strong>Próximos Passos:</strong></p>
            <ul style="text-align: left; display: inline-block;">
                <li>Implementar endpoints da API REST</li>
                <li>Criar models para communication_rules</li>
                <li>Desenvolver interface Vue.js</li>
                <li>Integrar sistema de execução</li>
            </ul>
        </div>
    </div>
</body>
</html>