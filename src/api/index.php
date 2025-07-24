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
    switch ($path) {
        case '/test':
            if ($method === 'GET') {
                testConnection();
            }
            break;
            
        case '/communication-rules':
            if ($method === 'GET') {
                getCommunicationRules();
            } elseif ($method === 'POST') {
                createCommunicationRule();
            }
            break;
            
        default:
            http_response_code(404);
            echo json_encode(['error' => 'Endpoint not found']);
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
            $input['active'] ?? true
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