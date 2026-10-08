<?php

session_start();

require_once "../backend/config.php";


if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit;
}


$id = (int) ($_GET["id"] ?? 0);

if ($id > 0) {

    $stmt = $pdo->prepare(
        "UPDATE messages
         SET status = 'read'
         WHERE id = :id"
    );

    $stmt->execute([
        ":id" => $id
    ]);
}


header("Location: dashboard.php");
exit;

?>