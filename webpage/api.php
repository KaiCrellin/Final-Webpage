// Purpose: API environment for handling request and responses to and from the database.
<?php
header("Content-type: application/json");

require_once 'lib/db.php';
require_once 'config/env.php';

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
    http_response_code(403); // forbidden
    echo json_encode(['message' => "Forbidden: Invalid API KEY"]);
    exit();
}

switch($method) {
    case 'GET':
        if(isset($request[0]) && $request[0] === 'students') {
            getStudents();
        } else {
            http_response_code(400);
            echo  json_encode(['message' => 'Invalid GET request']);
        }
        break;
    case 'POST':
        if(isset($request[0]) && $request[0] === 'students') {
            createStudents();
        } else {
            http_response_code(400);
            echo json_encode(['message' => 'Invalid POST request']);
        }
        break;
    case 'PUT':
        if(isset($request[0]) && $request[0] === 'students') {
            // function update student
        } else {
            http_response_code(400);
            echo json_encode(['message]' => 'Invalid PUT request']);
        }
        break;
    case 'PATCH':
        if (isset($request[0]) && $request[0] === 'students') {
            // function patch student 
        } else {
            http_response_code(400);
            echo json_encode(['message]' => 'Invalio PATCH request']);
        }
        break;
    case 'DELETE':
        if(isset($request[0]) && $request[0] === 'students') {
            //function delete students
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

function getStudents() {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM students");
    $students = $stmt->fetchALL();
    echo  json_encode($students);

}

function createStudents() {
    global $pdo;
    $data = json_decode(file_get_contents("php://input"), true);
    $stmt = $pdo->prepare("INSERT INTO students (name, email,password) VALUES (?,?,?)");
    if ($stmt->execute([$data['name'], $data['email'], $data['password']])) {
        http_response_code(201);
        echo json_encode(['message' => 'Student Created Successfully']);
    }else {
        http_response_code(400);
        echo json_encode(['message' => 'Error creating student']);
    }
}

/*

Logic pertaining to students can be changed here. Further implementation could possibly
encorperate downloading, uploading or appending information withina  specific course depending 
on role premissions
*/

?>