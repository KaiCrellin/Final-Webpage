// Purpose: API environment for handling request and responses to and from the database.
<?php
header("Content-type: application/json");

require_once 'lib/db.php';

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

echo json_encode(['message' => "API key validated, proceed with request"]);

/*

ALL logic can be handled here e.g requesting to change password, removing a student from a course,
adding a student to the course, uploading or downloading a file through href, login, logout, redirecting to
certain pages depending on role in database.

*/

/* 

define methods (GET,POST,PUT,PATCH,DELETE) to call functions e.g case 'GET': if(isset($request[0]) && $request[0] === 'students') {getStudents();}
disregard "FUNCTION" not needed for code. just to show where to call the function. 

*/

/*

at end of method if else method is invalid use http_response_code(400) and echo json_encode(['message' =>' to return
error messages
if else method is valid use http_response_code(201) and echo json_encode(['message' =>']) to return success
messages.
use "break:" to end the method and then "case" to create a new one 

*/ 


/*  

create functions to access the database (use PDO to prevent SQL injection and http_response_code to return status code)
e.g 
function getStudents() {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM students");
    $students = $stmt->fetchAll();
    echo json_encode($students);
}

OR
function createStudent() {
    global $pdo;
    $data = json_decode(file_get_contents("php://input"), true);
    $stmt = $pdo->prepare("INSERT INTO students (name, email, password) VALUES (?, ?, ?)");
    if ($stmt->execute([$data['name'], $data['email'], $data['password']])) {
        http_response_code(201);
        echo json_encode(["message" => "Student created successfully"]);
    } else {
        http_response_code(400);
        echo json_encode(["message" => "Error creating student"]);
    }
}

*/
?>