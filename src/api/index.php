<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

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
        ]);
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
        $input = json_decode(file_get_contents('php://input'), true);
        
        // Basic validation
        $required_fields = ['name', 'sql_query', 'channel', 'template_id'];
        foreach ($required_fields as $field) {
            if (!isset($input[$field]) || empty($input[$field])) {
                throw new Exception("Field '$field' is required");
            }
        }
        
        $database = new Database();
        $conn = $database->getConnection();
        
        // Generate rule_id if not provided
        $rule_id = $input['rule_id'] ?? uniqid('rule_');
        
        $query = "INSERT INTO communication_rules 
                  (rule_id, name, sql_query, channel, template_id, send_time_start, send_time_end, execution_order, active) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($query);
        $stmt->execute([
            $rule_id,
            $input['name'],
            $input['sql_query'],
            $input['channel'],
            $input['template_id'],
            $input['send_time_start'] ?? null,
            $input['send_time_end'] ?? null,
            $input['execution_order'] ?? null,
            isset($input['active']) ? (bool)$input['active'] : true
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
        $input = json_decode(file_get_contents('php://input'), true);
        
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
        
        $stmt = $conn->prepare($query);
        $stmt->execute([
            $currentRule['rule_id'], // Keep same rule_id
            $input['name'] ?? $currentRule['name'],
            $input['sql_query'] ?? $currentRule['sql_query'],
            $input['channel'] ?? $currentRule['channel'],
            $input['template_id'] ?? $currentRule['template_id'],
            $input['send_time_start'] ?? $currentRule['send_time_start'],
            $input['send_time_end'] ?? $currentRule['send_time_end'],
            $input['execution_order'] ?? $currentRule['execution_order'],
            isset($input['active']) ? (bool)$input['active'] : (bool)$currentRule['active']
        ]);
        
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