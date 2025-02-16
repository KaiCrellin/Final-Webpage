<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header("Content-type: application/json");

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, "http://localhost/acetraining/api.php/some-endpoint");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

$headers = [
    'Content-Type: application/json',
    'API_KEY: 0211'
];

curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$response = curl_exec($ch);
if (curl_errno($ch)) {
    echo 'Error:' . curl_error($ch);
}
curl_close($ch);

echo $response;

require_once __DIR__ . '/lib/db.php';
require_once __DIR__ . '/config/env.php';

loadenv(__DIR__ . '/.env');

$method = $_SERVER['REQUEST_METHOD'];
$request = isset($_SERVER['PATH_INFO']) ? explode('/', trim($_SERVER['PATH_INFO'], '/')) : [];

$apikey = $_SERVER['HTTP_API_KEY'] ?? '';
$validapikey = getenv('API_KEY');
if (empty($apikey)) {
    http_response_code(400);
    echo json_encode(['message' => 'Missing API Key']);
    exit();
}

if ($apikey !== $validapikey) {
    http_response_code(403);
    echo json_encode(['message' => "Forbidden: Invalid API KEY"]);
    exit();
}

switch ($method) {
    case 'GET':
        if (isset($request[0]) && $request[0] === 'students') {
            getStudents();
        } else {
            http_response_code(400);
            echo json_encode(['message' => 'Invalid GET request']);
        }
        break;
    case 'POST':
        if (isset($request[0]) && $request[0] === 'students') {
            createStudents();
        } else {
            http_response_code(400);
            echo json_encode(['message' => 'Invalid POST request']);
        }
        break;
    case 'PUT':
        if (isset($request[0]) && $request[0] === 'students') {
            // function update student
        } else {
            http_response_code(400);
            echo json_encode(['message' => 'Invalid PUT request']);
        }
        break;
    case 'PATCH':
        if (isset($request[0]) && $request[0] === 'students') {
            // function patch student 
        } else {
            http_response_code(400);
            echo json_encode(['message' => 'Invalid PATCH request']);
        }
        break;
    case 'DELETE':
        if (isset($request[0]) && $request[0] === 'students') {
            // function delete students
        } else {
            http_response_code(400);
            echo json_encode(['message' => 'Invalid DELETE request']);
        }
        break;
    default:
        http_response_code(405);
        echo json_encode(['message' => 'Method Not Allowed']);
        break;
}

function getStudents()
{
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM students");
    $students = $stmt->fetchAll();
    echo json_encode($students);
}

function createStudents()
{
    global $pdo;
    $data = json_decode(file_get_contents("php://input"), true);
    $stmt = $pdo->prepare("INSERT INTO students (name, email, password) VALUES (?,?,?)");
    if ($stmt->execute([$data['name'], $data['email'], $data['password']])) {
        http_response_code(201);
        echo json_encode(['message' => 'Student Created Successfully']);
    } else {
        http_response_code(400);
        echo json_encode(['message' => 'Error creating student']);
    }
}

?>