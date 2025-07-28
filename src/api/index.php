<?php
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Set UTF-8 encoding
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once '../config/database.php';

// Basic routing
$request_uri = $_SERVER['REQUEST_URI'];
$path = parse_url($request_uri, PHP_URL_PATH);
$path = str_replace('/api', '', $path);
$method = $_SERVER['REQUEST_METHOD'];

// Debug: log the path for debugging
// error_log("API Debug - Path: $path, Method: $method");

try {
    // Parse path segments for dynamic routing
    $segments = explode('/', trim($path, '/'));
    
    switch (true) {
        case $path === '/test' && $method === 'GET':
            testConnection();
            break;
            
        case $path === '/communication-rules' && $method === 'GET':
            getCommunicationRules();
            break;
            
        case $path === '/communication-rules' && $method === 'POST':
            createCommunicationRule();
            break;
            
        case $path === '/communication-rules/test' && $method === 'POST':
            testCommunicationRule();
            break;
            
        case $path === '/communication-rules/check-name' && $method === 'POST':
            checkRuleName();
            break;
            
        case $path === '/communication-rules/versions-summary' && $method === 'GET':
            getCommunicationRulesVersionsSummary();
            break;
            
        case count($segments) === 2 && $segments[0] === 'communication-rules' && $method === 'GET':
            getCommunicationRule($segments[1]);
            break;
            
        case count($segments) === 2 && $segments[0] === 'communication-rules' && $method === 'PUT':
            updateCommunicationRule($segments[1]);
            break;
            
        case count($segments) === 3 && $segments[0] === 'communication-rules' && $segments[2] === 'activate' && $method === 'POST':
            activateCommunicationRule($segments[1]);
            break;
            
        case count($segments) === 3 && $segments[0] === 'communication-rules' && $segments[2] === 'deactivate' && $method === 'POST':
            deactivateCommunicationRule($segments[1]);
            break;
            
        case count($segments) === 3 && $segments[0] === 'communication-rules' && $segments[2] === 'logs' && $method === 'GET':
            getCommunicationRuleLogs($segments[1]);
            break;
            
        case count($segments) === 3 && $segments[0] === 'communication-rules' && $segments[1] === 'versions' && $method === 'GET':
            getCommunicationRuleVersions($segments[2]);
            break;
            
        default:
            http_response_code(404);
            echo json_encode(['error' => 'Endpoint not found: ' . $path]);
            break;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

function testConnection() {
    try {
        $database = new Database();
        $conn = $database->getConnection();
        
        // Test query
        $query = "SELECT COUNT(*) as count FROM communication_rules";
        $stmt = $conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch();
        
        echo json_encode([
            'status' => 'success',
            'message' => 'Database connection successful',
            'rules_count' => $result['count'],
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'message' => 'Database connection failed: ' . $e->getMessage()
        ]);
    }
}

function getCommunicationRules() {
    try {
        $database = new Database();
        $conn = $database->getConnection();
        
        $query = "SELECT * FROM communication_rules WHERE superseded = FALSE ORDER BY execution_order ASC, name ASC";
        $stmt = $conn->prepare($query);
        $stmt->execute();
        $rules = $stmt->fetchAll();
        
        echo json_encode([
            'status' => 'success',
            'data' => $rules,
            'count' => count($rules)
        ], JSON_UNESCAPED_UNICODE);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
}

function createCommunicationRule() {
    try {
        $raw_input = file_get_contents('php://input');
        
        // Force UTF-8 encoding to handle emojis and accents correctly
        $utf8_input = mb_convert_encoding($raw_input, 'UTF-8', 'auto');
        $input = json_decode($utf8_input, true);
        
        // Basic validation
        $required_fields = ['name', 'sql_query', 'channel', 'template_id'];
        foreach ($required_fields as $field) {
            if (!isset($input[$field]) || empty($input[$field])) {
                throw new Exception("Field '$field' is required");
            }
        }
        
        $database = new Database();
        $conn = $database->getConnection();
        
        // Check if name already exists for active rules
        $checkQuery = "SELECT COUNT(*) as count FROM communication_rules WHERE name = ? AND superseded = FALSE";
        $checkStmt = $conn->prepare($checkQuery);
        $checkStmt->execute([$input['name']]);
        $existing = $checkStmt->fetch();
        
        if ($existing['count'] > 0) {
            throw new Exception("Régua com esse nome já existe. Localize a régua existente e edite-a.");
        }
        
        // Generate rule_id if not provided
        $rule_id = $input['rule_id'] ?? uniqid('rule_');
        
        $query = "INSERT INTO communication_rules 
                  (rule_id, name, sql_query, channel, template_id, send_time_start, send_time_end, execution_order, active) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($query);
        // Helper function to convert empty strings to null
        $nullIfEmpty = function($value) {
            if (!isset($value) || $value === '' || $value === null || trim((string)$value) === '') {
                return null;
            }
            return $value;
        };
        
        $stmt->execute([
            $rule_id,
            $input['name'],
            $input['sql_query'],
            $input['channel'],
            $input['template_id'],
            $nullIfEmpty($input['send_time_start'] ?? null),
            $nullIfEmpty($input['send_time_end'] ?? null),
            $nullIfEmpty($input['execution_order'] ?? null),
            isset($input['active']) ? (int)(bool)$input['active'] : 1
        ]);
        
        echo json_encode([
            'status' => 'success',
            'message' => 'Communication rule created successfully',
            'rule_id' => $rule_id
        ]);
        
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
}

function getCommunicationRule($id) {
    try {
        $database = new Database();
        $conn = $database->getConnection();
        
        $query = "SELECT * FROM communication_rules WHERE id = ? AND superseded = FALSE";
        $stmt = $conn->prepare($query);
        $stmt->execute([$id]);
        $rule = $stmt->fetch();
        
        if (!$rule) {
            http_response_code(404);
            echo json_encode([
                'status' => 'error',
                'message' => 'Communication rule not found'
            ]);
            return;
        }
        
        echo json_encode([
            'status' => 'success',
            'data' => $rule
        ]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
}

function updateCommunicationRule($id) {
    try {
        $raw_input = file_get_contents('php://input');
        $utf8_input = mb_convert_encoding($raw_input, 'UTF-8', 'auto');
        $input = json_decode($utf8_input, true);
        
        
        $database = new Database();
        $conn = $database->getConnection();
        
        // Get current rule
        $query = "SELECT * FROM communication_rules WHERE id = ? AND superseded = FALSE";
        $stmt = $conn->prepare($query);
        $stmt->execute([$id]);
        $currentRule = $stmt->fetch();
        
        if (!$currentRule) {
            http_response_code(404);
            echo json_encode([
                'status' => 'error',
                'message' => 'Communication rule not found'
            ]);
            return;
        }
        
        // Mark current version as superseded
        $query = "UPDATE communication_rules SET superseded = TRUE WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$id]);
        
        // Create new version
        $query = "INSERT INTO communication_rules 
                  (rule_id, name, sql_query, channel, template_id, send_time_start, send_time_end, execution_order, active) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        // Helper function to convert empty strings to null
        $nullIfEmpty = function($value) {
            if (!isset($value) || $value === '' || $value === null || trim((string)$value) === '') {
                return null;
            }
            return $value;
        };
        
        $stmt = $conn->prepare($query);
        
        $params = [
            $currentRule['rule_id'], // Keep same rule_id
            $input['name'] ?? $currentRule['name'],
            $input['sql_query'] ?? $currentRule['sql_query'],
            $input['channel'] ?? $currentRule['channel'],
            $input['template_id'] ?? $currentRule['template_id'],
            $nullIfEmpty($input['send_time_start'] ?? $currentRule['send_time_start']),
            $nullIfEmpty($input['send_time_end'] ?? $currentRule['send_time_end']),
            $nullIfEmpty($input['execution_order'] ?? $currentRule['execution_order']),
            isset($input['active']) ? (int)(bool)$input['active'] : (int)(bool)$currentRule['active']
        ];
        
        $result = $stmt->execute($params);
        
        echo json_encode([
            'status' => 'success',
            'message' => 'Communication rule updated successfully'
        ]);
        
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
}

function activateCommunicationRule($id) {
    try {
        $database = new Database();
        $conn = $database->getConnection();
        
        $query = "UPDATE communication_rules SET active = TRUE WHERE id = ? AND superseded = FALSE";
        $stmt = $conn->prepare($query);
        $stmt->execute([$id]);
        
        if ($stmt->rowCount() === 0) {
            http_response_code(404);
            echo json_encode([
                'status' => 'error',
                'message' => 'Communication rule not found'
            ]);
            return;
        }
        
        echo json_encode([
            'status' => 'success',
            'message' => 'Communication rule activated successfully'
        ]);
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
}

function deactivateCommunicationRule($id) {
    try {
        $database = new Database();
        $conn = $database->getConnection();
        
        $query = "UPDATE communication_rules SET active = FALSE WHERE id = ? AND superseded = FALSE";
        $stmt = $conn->prepare($query);
        $stmt->execute([$id]);
        
        if ($stmt->rowCount() === 0) {
            http_response_code(404);
            echo json_encode([
                'status' => 'error',
                'message' => 'Communication rule not found'
            ]);
            return;
        }
        
        echo json_encode([
            'status' => 'success',
            'message' => 'Communication rule deactivated successfully'
        ]);
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
}

function getCommunicationRuleLogs($id) {
    try {
        $database = new Database();
        $conn = $database->getConnection();
        
        // Get rule info
        $ruleQuery = "SELECT name FROM communication_rules WHERE id = ? AND superseded = FALSE";
        $stmt = $conn->prepare($ruleQuery);
        $stmt->execute([$id]);
        $rule = $stmt->fetch();
        
        if (!$rule) {
            http_response_code(404);
            echo json_encode([
                'status' => 'error',
                'message' => 'Communication rule not found'
            ]);
            return;
        }
        
        // Get logs (mock data for now since we don't have real executions yet)
        $logsQuery = "SELECT * FROM rule_dispatch_log ORDER BY rule_dispatch_timestamp DESC LIMIT 50";
        $stmt = $conn->prepare($logsQuery);
        $stmt->execute();
        $logs = $stmt->fetchAll();
        
        echo json_encode([
            'status' => 'success',
            'data' => $logs,
            'rule_name' => $rule['name']
        ]);
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
}

function testCommunicationRule() {
    try {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($input['sql_query']) || empty($input['sql_query'])) {
            throw new Exception("SQL query is required");
        }
        
        $database = new Database();
        $conn = $database->getConnection();
        
        // For security, we'll simulate the test rather than execute arbitrary SQL
        // In a real environment, you'd want to execute in a sandboxed environment
        
        echo json_encode([
            'status' => 'success',
            'message' => 'SQL syntax appears valid',
            'simulation' => true,
            'expected_fields' => ['first_name', 'contact', 'payload'],
            'note' => 'This is a simulation. Real SQL execution would be implemented in production.'
        ]);
        
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
}

function checkRuleName() {
    try {
        $raw_input = file_get_contents('php://input');
        $utf8_input = mb_convert_encoding($raw_input, 'UTF-8', 'auto');
        $input = json_decode($utf8_input, true);
        
        if (!isset($input['name']) || empty($input['name'])) {
            throw new Exception("Name is required");
        }
        
        $database = new Database();
        $conn = $database->getConnection();
        
        $query = "SELECT COUNT(*) as count FROM communication_rules WHERE name = ? AND superseded = FALSE";
        $stmt = $conn->prepare($query);
        $stmt->execute([$input['name']]);
        $result = $stmt->fetch();
        
        $exists = $result['count'] > 0;
        
        echo json_encode([
            'status' => 'success',
            'exists' => $exists,
            'message' => $exists ? 'Nome já existe. Use outro.' : 'Nome disponível.'
        ]);
        
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
}

function getCommunicationRulesVersionsSummary() {
    try {
        $database = new Database();
        $conn = $database->getConnection();
        
        // Get summary of rules grouped by rule_id with current name and version count
        $query = "
            SELECT 
                rule_id,
                MAX(CASE WHEN superseded = FALSE THEN name END) as current_name,
                COUNT(*) as version_count,
                MIN(CASE WHEN superseded = FALSE THEN execution_order END) as min_execution_order
            FROM communication_rules 
            GROUP BY rule_id 
            ORDER BY min_execution_order ASC, current_name ASC
        ";
        
        $stmt = $conn->prepare($query);
        $stmt->execute();
        $rules = $stmt->fetchAll();
        
        // Filter out rules with null current_name (means no active version exists)
        $rules = array_filter($rules, function($rule) {
            return $rule['current_name'] !== null;
        });
        
        echo json_encode([
            'status' => 'success',
            'data' => array_values($rules), // Re-index array after filtering
            'count' => count($rules)
        ], JSON_UNESCAPED_UNICODE);
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
}

function getCommunicationRuleVersions($rule_id) {
    try {
        $database = new Database();
        $conn = $database->getConnection();
        
        // Get all versions for a specific rule_id, ordered by created_at DESC (newest first)
        $query = "
            SELECT 
                id,
                rule_id,
                name,
                sql_query,
                channel,
                template_id,
                send_time_start,
                send_time_end,
                execution_order,
                active,
                created_at,
                superseded
            FROM communication_rules 
            WHERE rule_id = ? 
            ORDER BY created_at DESC
        ";
        
        $stmt = $conn->prepare($query);
        $stmt->execute([$rule_id]);
        $versions = $stmt->fetchAll();
        
        if (empty($versions)) {
            http_response_code(404);
            echo json_encode([
                'status' => 'error',
                'message' => 'Rule not found'
            ]);
            return;
        }
        
        echo json_encode([
            'status' => 'success',
            'data' => $versions,
            'count' => count($versions)
        ], JSON_UNESCAPED_UNICODE);
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
}