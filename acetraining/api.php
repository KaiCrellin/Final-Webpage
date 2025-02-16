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
    'API_KEY: 0211' // Send the API key in the headers
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
        } elseif (isset($request[0]) && $request[0] === 'password_reset') {
            handlePasswordReset();
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

function handlePasswordReset()
{
    global $pdo;
    session_start();

    $data = json_decode(file_get_contents("php://input"), true);
    $csrf_token = $data['csrf_token'] ?? '';
    $email = $data['email'] ?? '';

    if (empty($csrf_token) || $csrf_token !== $_SESSION['csrf_token']) {
        http_response_code(400);
        echo json_encode(['message' => 'Invalid CSRF Token']);
        exit();
    }
    if (empty($email)) {
        http_response_code(400);
        echo json_encode(['message' => 'Email is required']);
        exit();
    }

    $user = getUserByEmail($email);
    if (!$user) {
        http_response_code(404);
        echo json_encode(['message' => 'User Not Found']);
        exit();
    }

    $resetToken = bin2hex(random_bytes(16));
    $resetTokenExpiry = date('Y-m-d H:i:s', strtotime('+1 hour'));

    saveResetToken($email, $resetToken, $resetTokenExpiry);

    sendResetEmail($email, $resetToken);

    echo json_encode(['message' => 'Password reset email sent']);
}

function getUserByEmail($email)
{
    global $pdo;
    $stmt = $pdo->prepare("SELECT email FROM students WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    return $user ? $user : false;
}

function saveResetToken($email, $resetToken, $resetTokenExpiry)
{
    global $pdo;
    if (!getUserByEmail($email)) {
        http_response_code(400);
        echo json_encode(['message' => 'Invalid Email']);
        exit();
    } else {
        $stmt = $pdo->prepare("UPDATE password_resets SET token = :token, expires = :expires WHERE email = :email");
        $stmt->execute([
            'token' => $resetToken,
            'expires' => $resetTokenExpiry,
            'email' => $email
        ]);
        return $stmt;
    }
}

function sendResetEmail($email, $resetToken)
{
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT id, email FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $resetlink = "http://localhost:8080/acetraining/reset_pass.php?token=$resetToken";
            $subject = "Password Reset";
            $message = "Click the link below to reset your password:\n\n$resetlink";


            $headers = 'From: no-reply@yourdomain.com' . "\r\n" .
                'Reply-To: no-reply@yourdomain.com' . "\r\n" .
                'X-Mailer: PHP/' . phpversion();

            if (mail($email, $subject, $message, $headers)) {
                echo 'Password reset link sent to your email address';
            } else {
                echo 'Failed to send email';
            }
        } else {
            http_response_code(400);
            echo json_encode(['message' => 'Invalid Email Address']);
        }
    } catch (PDOException $e) {
        echo 'Database Error:' . $e->getMessage();
        exit();
    }
}
?>