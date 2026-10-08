<?php

require_once "config.php";

header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode([
        "success" => false,
        "message" => "Invalid request."
    ]);
    exit;
}

$name = trim($_POST["name"] ?? "");
$email = trim($_POST["email"] ?? "");
$message = trim($_POST["message"] ?? "");

if ($name === "" || $email === "" || $message === "") {
    echo json_encode([
        "success" => false,
        "message" => "Please fill in all fields."
    ]);
    exit;
}

if (strlen($name) < 2 || strlen($name) > 50) {
    echo json_encode([
        "success" => false,
        "message" => "Name must be between 2 and 50 characters."
    ]);
    exit;
}

if (!preg_match("/^[a-zA-Z\s.'-]+$/", $name)) {
    echo json_encode([
        "success" => false,
        "message" => "Please enter a valid name."
    ]);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode([
        "success" => false,
        "message" => "Please enter a valid email address."
    ]);
    exit;
}

if (strlen($message) < 10) {
    echo json_encode([
        "success" => false,
        "message" => "Message must be at least 10 characters."
    ]);
    exit;
}

if (strlen($message) > 1000) {
    echo json_encode([
        "success" => false,
        "message" => "Message must not exceed 1000 characters."
    ]);
    exit;
}

try {

    $sql = "INSERT INTO messages (name, email, message)
            VALUES (:name, :email, :message)";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ":name" => $name,
        ":email" => $email,
        ":message" => $message
    ]);

    echo json_encode([
        "success" => true,
        "message" => "Message sent successfully! I'll get back to you soon."
    ]);

    exit;

} catch (PDOException $e) {

    echo json_encode([
        "success" => false,
        "message" => "Something went wrong. Please try again."
    ]);

    exit;
}

